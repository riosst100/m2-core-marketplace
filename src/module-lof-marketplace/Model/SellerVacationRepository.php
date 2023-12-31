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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\SellerVacationRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class SellerVacationRepository implements SellerVacationRepositoryInterface
{
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var VacationFactory
     */
    protected $vacationFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * SellerVacationRepository constructor.
     * @param SellerFactory $sellerFactory
     * @param VacationFactory $vacationFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        SellerFactory $sellerFactory,
        VacationFactory $vacationFactory,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->vacationFactory = $vacationFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getSellerVacation(string $sellerUrl)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        if ($seller && $seller->getId()) {
            $collection = $this->vacationFactory->create()->getCollection()
                            ->addFieldToFilter('seller_id', $seller->getId())
                            ->addFieldToFilter('status', Vacation::STATUS_ENABLED);
            $collection->getSelect()
                        ->where('from_date <= curdate() AND to_date > curdate()');
            $vacation = $collection->getFirstItem();
            return $vacation;
        } else {
            throw new NoSuchEntityException(__('Not found Seller for url "%1".', $sellerUrl));
        }
    }

    /**
     * @inheritdoc
     */
    public function getSellerVacationById(int $sellerId)
    {
        $seller = $this->getSellerById($sellerId);
        if ($seller && $seller->getId()) {
            $collection = $this->vacationFactory->create()->getCollection()
                            ->addFieldToFilter('seller_id', $seller->getId())
                            ->addFieldToFilter('status', Vacation::STATUS_ENABLED);
            $collection->getSelect()
                        ->where('from_date <= curdate() AND to_date > curdate()');
            $vacation = $collection->getFirstItem();
            return $vacation;
        } else {
            throw new NoSuchEntityException(__('Not found Seller "%1".', $sellerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function putSellerVacation(int $customerId, \Lof\MarketPlace\Api\Data\SellerVacationInterface $vacation)
    {
        if (!$vacation) {
            throw new CouldNotSaveException(__(
                'Could not save the vacation: missing submit data.'
            ));
        }
        $seller = $this->getSellerById($customerId);
        if ($seller && $seller->getId()) {
            $vacationData = $this->vacationFactory->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', ['eq' => $seller->getId()])
                ->getFirstItem()
                ->getData();
            if (empty($vacationData)) {
                $data["seller_id"] = $seller->getId();
                $data["status"] = $vacation->getStatus();
                $data["vacation_message"] = $vacation->getVacationMessage();
                $data["from_date"] = $vacation->getFromDate();
                $data["to_date"] = $vacation->getToDate();
                $data["text_add_cart"] = $vacation->getTextAddCart();
                try {
                    $vacationReturn = $this->vacationFactory->create()
                                    ->setData($data)
                                    ->save();
                    return $vacationReturn;
                } catch (\Exception $e) {
                    throw new CouldNotSaveException(__(
                        'Could not save the vacation: some issues, please try again.'
                    ));
                }
            } else {
                throw new NoSuchEntityException(__('Vacation of seller is existed'));
            }
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $customerId)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $vacationModel = $this->vacationFactory->create()->getCollection()
                ->addFieldToFilter('seller_id', $seller->getId())
                ->getFirstItem();
            if ($vacationModel && $vacationModel->getId()) {
                return $vacationModel;
            } else {
                return null;
            }
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
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

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }

    /**
     * get seller by sellerId
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerById(int $sellerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('seller_id', ['eq' => $sellerId])
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }
}
