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

use Lof\MarketPlace\Api\SalesInvoicesRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection as OrderItemCollection;
use Magento\Framework\Exception\NoSuchEntityException;

class SalesInvoicesRepository implements SalesInvoicesRepositoryInterface
{
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var OrderItemCollection
     */
    protected $orderItemCollection;

    /**
     * SalesInvoicesRepository constructor.
     * @param SellerFactory $sellerFactory
     * @param InvoiceFactory $invoiceFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param OrderItemCollection $orderItemCollection
     */
    public function __construct(
        SellerFactory $sellerFactory,
        InvoiceFactory $invoiceFactory,
        SellerCollectionFactory $sellerCollectionFactory,
        OrderItemCollection $orderItemCollection
    )
    {
        $this->sellerFactory = $sellerFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->orderItemCollection = $orderItemCollection;
    }

    /**
     * @inheritdoc
     */
    public function getSellerInvoices($customerId)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $res = [
                "code" => 405,
                "message" => "Get data failed"
            ];
            $data = $this->invoiceFactory->create()->getCollection()
                ->addFieldToFilter('seller_id', $seller->getId())
                ->getData();

            if ($data) {
                $res["code"] = 0;
                $res["message"] = "get data success!";
                $res["result"]["invoices"][] = $data;
            } else {
                $res["code"] = 0;
                $res["message"] = "get data success!";
                $res["result"]["invoices"] = [];
            }

            return $res;
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function getSellerInvoiceById($invoiceId, $customerId)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $invoiceData = $this->invoiceFactory->create()->getCollection()
                                ->addFieldToFilter('seller_id', ['eq' => $seller->getId()])
                                ->addFieldToFilter('invoice_id', ['eq' => (int)$invoiceId])
                                ->getFirstItem()
                                ->getData();

            if ($invoiceData) {
                try {
                    $products = $this->orderItemCollection
                        ->addFieldToFilter('seller_id', ['eq' => $seller->getId()])
                        ->addFieldToFilter('order_id', ['eq' => $invoiceData['seller_order_id']])
                        ->loadData()
                        ->getData();

                    $invoiceData["line_items"] = $products;
                    $res["code"] = 0;
                    $res["message"] = __("get data success!");
                    $res["result"]["invoice"] = $invoiceData;
                } catch (\Exception $e) {
                    // phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
                    throw new NoSuchEntityException(__('Can not get the invoice %1.', $e->getMessage()));
                }
            }

            return $res;
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet.'));
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
}
