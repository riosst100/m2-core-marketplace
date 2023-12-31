<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model\Seller;

use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class responsible for creating and updating seller entities.
 */
class Save
{
    /**
     * @var \Lof\MarketPermissions\Model\SaveHandlerPool
     */
    private $saveHandlerPool;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller
     */
    private $sellerResource;

    /**
     * @var \Lof\MarketPermissions\Model\SellerFactory
     */
    private $sellerFactory;

    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerInterfaceFactory
     */
    private $dataSellerFactory;

//    /**
//     * @var \Lof\MarketPermissions\Model\SaveValidatorPool
//     */
//    private $saveValidatorPool;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Lof\MarketPermissions\Model\SaveHandlerPool $saveHandlerPool
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller $sellerResource
     * @param \Lof\MarketPermissions\Api\Data\SellerInterfaceFactory $dataSellerFactory
     * @param \Lof\MarketPermissions\Model\SellerFactory $sellerFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Lof\MarketPermissions\Model\SaveHandlerPool $saveHandlerPool,
        \Lof\MarketPlace\Model\ResourceModel\Seller $sellerResource,
        \Lof\MarketPermissions\Api\Data\SellerInterfaceFactory $dataSellerFactory,
        \Lof\MarketPermissions\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->sellerFactory = $sellerFactory;
        $this->saveHandlerPool = $saveHandlerPool;
        $this->sellerResource = $sellerResource;
        $this->dataSellerFactory = $dataSellerFactory;
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Checks if provided data for a seller is correct, saves the seller entity and executes additional save handlers
     * from the pool.
     *
     * @param \Lof\MarketPermissions\Api\Data\SellerInterface $seller
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Lof\MarketPermissions\Api\Data\SellerInterface $seller)
    {
//        $this->processAddress($seller);
//        $this->processSalesRepresentative($seller);
//        $sellerId = $seller->getSellerId();
//        $sellerData = $this->extensibleDataObjectConverter->toNestedArray(
//            $seller,
//            [],
//            \Lof\MarketPermissions\Api\Data\SellerInterface::class
//        );
//        $sellerModel = $this->sellerFactory->create()->setData($sellerData);
//        $this->saveValidatorPool->execute($seller, $initialSeller);
        try {
            $this->sellerResource->save($seller);
//            $initialSeller = $this->getInitialSeller($sellerModel->getSellerId());
//            $this->saveHandlerPool->execute($seller, $initialSeller);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save seller'),
                $e
            );
        }

        return $seller;
    }

    /**
     * @param $sellerModel
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function getDataModel($sellerModel)
    {
        $sellerData = $sellerModel->getData();

        $sellerDataObject = $this->dataSellerFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sellerDataObject,
            $sellerData,
            \Lof\MarketPermissions\Api\Data\SellerInterface::class
        );

        return $sellerDataObject;
    }

    /**
     * Get initial seller.
     *
     * @param int|null $sellerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    private function getInitialSeller($sellerId)
    {
        $seller = $this->sellerFactory->create();
        try {
            $this->sellerResource->load($seller, $sellerId);
        } catch (\Exception $e) {
            //Do nothing, just leave the object blank.
        }

        return $seller;
    }

//    /**
//     * Set default sales representative (admin user responsible for seller) if it is not set.
//     *
//     * @param \Lof\MarketPermissions\Api\Data\SellerInterface $seller
//     * @return void
//     */
//    private function processSalesRepresentative(\Lof\MarketPermissions\Api\Data\SellerInterface $seller)
//    {
//        if (!$seller->getSalesRepresentativeId()) {
//            /** @var \Magento\User\Model\ResourceModel\User\Collection $userCollection */
//            $userCollection = $this->userCollectionFactory->create();
//            $seller->setSalesRepresentativeId($userCollection->setPageSize(1)->getFirstItem()->getId());
//        }
//    }

    /**
     * Prepare seller address.
     *
     * @param \Lof\MarketPermissions\Api\Data\SellerInterface $seller
     * @return void
     */
    private function processAddress(\Lof\MarketPermissions\Api\Data\SellerInterface $seller)
    {
        $street = $seller->getStreet();
        if (is_array($street) && count($street)) {
            $seller->setStreet(trim(implode("\n", $street)));
        }
    }
}
