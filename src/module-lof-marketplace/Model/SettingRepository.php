<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\SettingInterface;
use Lof\MarketPlace\Api\Data\SettingInterfaceFactory;
use Lof\MarketPlace\Api\Data\SettingSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SettingRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Setting as ResourceSetting;
use Lof\MarketPlace\Model\ResourceModel\Setting\CollectionFactory as SettingCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SettingRepository implements SettingRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SettingInterfaceFactory
     */
    protected $settingFactory;

    /**
     * @var Setting
     */
    protected $searchResultsFactory;

    /**
     * @var SettingCollectionFactory
     */
    protected $settingCollectionFactory;

    /**
     * @var ResourceSetting
     */
    protected $resource;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @param ResourceSetting $resource
     * @param SettingInterfaceFactory $settingFactory
     * @param SettingCollectionFactory $settingCollectionFactory
     * @param SettingSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerFactory $sellerFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        ResourceSetting $resource,
        SettingInterfaceFactory $settingFactory,
        SettingCollectionFactory $settingCollectionFactory,
        SettingSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerFactory $sellerFactory,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->resource = $resource;
        $this->settingFactory = $settingFactory;
        $this->settingCollectionFactory = $settingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->sellerFactory = $sellerFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(SettingInterface $setting)
    {
        try {
            $this->resource->save($setting);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the setting: %1',
                $exception->getMessage()
            ));
        }
        return $setting;
    }

    /**
     * @inheritDoc
     */
    public function saveMySetting(int $customerId, SettingInterface $setting)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            try {
                $setting->setSellerId($seller->getId());
                $this->resource->save($setting);
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the setting: %1',
                    $exception->getMessage()
                ));
            }
            return $setting;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function getMySetting(int $customerId, $settingId)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $setting = $this->settingFactory->create();
            $this->resource->load($setting, $settingId);
            if (!$setting->getId()) {
                throw new NoSuchEntityException(__('Setting with id "%1" does not exist.', $settingId));
            }
            return $setting;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function get($settingId)
    {
        $setting = $this->settingFactory->create();
        $this->resource->load($setting, $settingId);
        if (!$setting->getId()) {
            throw new NoSuchEntityException(__('Setting with id "%1" does not exist.', $settingId));
        }
        return $setting;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->settingCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $collection = $this->settingCollectionFactory->create();

            $this->collectionProcessor->process($criteria, $collection);

            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);

            $items = [];
            foreach ($collection as $model) {
                $items[] = $model;
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(SettingInterface $setting)
    {
        try {
            $settingModel = $this->settingFactory->create();
            $this->resource->load($settingModel, $setting->getSettingId());
            $this->resource->delete($settingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Setting: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($settingId)
    {
        return $this->delete($this->get($settingId));
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        $seller = $this->sellerCollectionFactory->create()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }
}

