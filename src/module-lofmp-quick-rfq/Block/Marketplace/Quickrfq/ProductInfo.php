<?php

namespace Lofmp\Quickrfq\Block\Marketplace\Quickrfq;

use Lof\Quickrfq\Model\Quickrfq;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\UrlInterface;

/**
 * Class ProductInfo
 * @package Lofmp\Quickrfq\Block\Marketplace\Quickrfq
 */
class ProductInfo extends Template
{
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Product
     */
    private $product;
    /**
     * @var Data
     */
    private $_pricingHelper;
    /**
     * @var UrlInterface
     */
    private $_urlInterface;

    /**
     * ProductInfo constructor.
     * @param Template\Context $context
     * @param Quickrfq $quickrfq
     * @param Product $product
     * @param Data $pricingHelper
     * @param UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Quickrfq $quickrfq,
        Product $product,
        Data $pricingHelper,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->product = $product;
        $this->quickrfq = $quickrfq;
        $this->_pricingHelper = $pricingHelper;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    public function getProduct()
    {
        $productId = $this->getQuote()->getProductId();
        return $this->product->load($productId);
    }

    /**
     * @return Quickrfq
     */
    public function getQuote()
    {
        $quickrfqId = $this->getRequest()->getParam('quickrfq_id');
        return $this->quickrfq->load($quickrfqId);
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        $quote =  $this->getQuote();
        return $quote->getPricePerProduct() * $quote->getQuantity();
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getProductPriceHtml($price)
    {
        return $this->_pricingHelper->currency($price, true, false);
    }

    /**
     * @return float|int
     */
    public function getAdminTotalPrice()
    {
        $quote =  $this->getQuote();
        return $quote->getAdminPrice() * $quote->getAdminQuantity();
    }

    /**
     * get Cart Expiry date
     * @return string|null
     */
    public function getCartExpiry()
    {
        $quote = $this->getQuote();
        return "";
    }

    /**
     * @param $date
     * @param $format
     * @return false|string
     */
    public function formatTheDate($date, $format)
    {
        $date_time = strtotime($date);
        return date($format, $date_time);
    }

    /**
     * @return string|null
     */
    public function getUpdateFormLink()
    {
        return $this->_urlInterface->getUrl('*/*/save', [ 'quickrfq_id' => $this->getRequest()->getParam('quickrfq_id') ]);
    }

    /**
     * Get edit product link
     * @return string
     */
    public function getEditProductUrl($productId) 
    {
        return $this->_urlInterface->getUrl('catalog/product/edit', [ 'id' => (int)$productId ]);
    }

}
