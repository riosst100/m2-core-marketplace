<?php

namespace Lofmp\Quickrfq\Block\Adminhtml\Rfq;

use Lof\MarketPlace\Model\Seller;
use Lof\Quickrfq\Model\Quickrfq;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class CustomerAddress
 * @package Lofmp\Quickrfq\Block\Adminhtml\Rfq
 */
class SellerInfo extends Template
{
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Seller
     */
    private $seller;

    /**
     * ProductInfo constructor.
     * @param Template\Context $context
     * @param Customer $customer
     * @param Quickrfq $quickrfq
     * @param Seller $seller
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Customer $customer,
        Quickrfq $quickrfq,
        Seller $seller,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->quickrfq = $quickrfq;
        $this->seller = $seller;
        $this->customer = $customer;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return Quickrfq
     */
    public function getQuote()
    {
        $quoteId =  $this->getRequest()->getParam('quickrfq_id');
        return $this->quickrfq->load($quoteId);
    }


    /**
     * @return Seller
     * @throws NoSuchEntityException
     */
    public function getSeller()
    {
        $productId = $this->getQuote()->getProductId();
        $product = $this->productRepository->getById($productId);
        $sellerId = $product->getSellerId();
        return $this->seller->load($sellerId);
    }
}
