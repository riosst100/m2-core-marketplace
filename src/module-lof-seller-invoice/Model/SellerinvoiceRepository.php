<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lof\SellerInvoice\Model;

use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Lof\SellerInvoice\Api\Data\SellerinvoiceInterface;
use Lof\SellerInvoice\Api\Data\SellerinvoiceInterfaceFactory;
use Lof\SellerInvoice\Api\Data\SellerinvoiceSearchResultsInterfaceFactory;
use Lof\SellerInvoice\Api\SellerinvoiceRepositoryInterface;
use Lof\SellerInvoice\Model\ResourceModel\Sellerinvoice as ResourceSellerinvoice;
use Lof\SellerInvoice\Model\ResourceModel\Sellerinvoice\CollectionFactory as SellerinvoiceCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPlace\Helper\Data;
use Lof\SellerInvoice\Api\Data\DataInvoiceInterfaceFactory;

class SellerinvoiceRepository implements SellerinvoiceRepositoryInterface
{

    /**
     * @var ResourceSellerinvoice
     */
    protected $resource;

    /**
     * @var SellerinvoiceCollectionFactory
     */
    protected $sellerinvoiceCollectionFactory;

    /**
     * @var SellerinvoiceInterfaceFactory
     */
    protected $sellerinvoiceFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Sellerinvoice
     */
    protected $searchResultsFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Lof\MarketPlace\Model\InvoiceFactory
     */
    protected $_sellerInvoice;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var DataInvoiceInterfaceFactory
     */
    protected $dataInvoiceFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @param ResourceSellerinvoice $resource
     * @param SellerinvoiceInterfaceFactory $sellerinvoiceFactory
     * @param SellerinvoiceCollectionFactory $sellerinvoiceCollectionFactory
     * @param SellerinvoiceSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoice
     * @param \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf
     * @param Data $helper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param DataInvoiceInterfaceFactory $dataInvoiceFactory
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        ResourceSellerinvoice $resource,
        SellerinvoiceInterfaceFactory $sellerinvoiceFactory,
        SellerinvoiceCollectionFactory $sellerinvoiceCollectionFactory,
        SellerinvoiceSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Magento\Sales\Model\Order\InvoiceFactory $invoice,
        DataInvoiceInterfaceFactory $dataInvoiceFactory,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Data $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf,
        SellerCollectionFactory $sellerCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->resource = $resource;
        $this->sellerinvoiceFactory = $sellerinvoiceFactory;
        $this->sellerinvoiceCollectionFactory = $sellerinvoiceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->_invoice = $invoice;
        $this->dataInvoiceFactory = $dataInvoiceFactory;
        $this->_order = $order;
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->_pdf              = $pdf;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->_fileFactory      = $fileFactory;
    }

    /**
     * @inheritDoc
     */
    public function sellerGetListInvoice(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);

        if ($seller & $seller->getId()) {
            $collection = $this->sellerinvoiceCollectionFactory->create();

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
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @param int $customerId
     * @param int $orderId
     * @return \Lof\SellerInvoice\Api\Data\DataInvoiceInterface
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetInvoice(
        int $customerId,
        int $orderId
    ) {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller & $seller->getId()) {
                $foundItem = $this->sellerinvoiceCollectionFactory->create()
                    ->addFieldToFilter("seller_order_id", $orderId)
                    ->addFieldToFilter("seller_id", $seller)
                    ->getFirstItem();

                $order = $this->getOrder($orderId);
                $customer = $this->helper->getCustomerById($order->getCustomerId());
                $addresscustomer = $this->customerFactory->create()
                                         ->getAddressCollection()
                                        ->addFieldToFilter('parent_id',$order->getCustomerId())
                                        ->getData();

                $shippingaddress = $addresscustomer['0']['city'].','.$addresscustomer['0']['street'].','.$addresscustomer['0']['postcode'].','.$addresscustomer['0']['country_id'];
                $phone = $addresscustomer['0']['telephone'];
                $shippingMethod = $order->getData('shipping_method');

                $orderItems = $this->orderRepository->get($orderId);
                $item_data = [];
                $count = 0;
                 foreach ($orderItems->getAllVisibleItems() as $item) {
                     $item_data[$count]['product_id'] = $item->getProductId();
                     $item_data[$count]['sku'] = $item->getSku();
                 }
                $modelData = $this->dataInvoiceFactory->create();
                $this->resource->load($modelData,$foundItem->getInvoiceId());
                $modelData->setCustomerId($order->getCustomerId());
                $modelData->setShippingAddress($shippingaddress);
                $modelData->setShippingMethod($shippingMethod);
                $modelData->setPhone($phone);
                $modelData->setSellerEmail($seller->getEmail());
                $modelData->setSellerName($seller->getName());
                $modelData->setProductId($item_data);
                $modelData->setCustomerEmail($customer->getEmail());
                $modelData->setCustomerName($customer->getName());
            return $modelData;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @param $orderId
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder($orderId)
    {
        return $this->_order->create()->load($orderId);
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        if (!isset($this->_sellers[$customerId])) {
            $sellerCollection = $this->sellerCollectionFactory->create();
            $this->_sellers[$customerId] = $sellerCollection
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                ->getFirstItem();
        }
        return $this->_sellers[$customerId];
    }
}
