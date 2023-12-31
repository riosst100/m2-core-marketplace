<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $timediscountFactory;
    /**
     * @param Magento\Catalog\Block\Product\Context   $context
     * @param Magento\Catalog\Model\Product           $product
     * @param CustomerSession                          $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceHelper
     * @param Magento\Framework\Pricing\Helper\Data   $priceHelper
     * @param \Lofmp\TimeDiscount\Model\ProductFactory $timediscount
     * @param Lofmp\TimeDiscount\Helper\Data              $helperData
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product $product,
        CustomerSession $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceHelper,
        \Lofmp\TimeDiscount\Model\ProductFactory $timediscount,
        \Lofmp\TimeDiscount\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->_product = $product;
        $this->timediscountFactory = $timediscount;
        $this->_customerSession = $customerSession;
        $this->_priceHelper = $priceHelper;
        $this->_helperData = $helperData;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get product time discount
     *
     * @return mixed|bool
     */
    public function getProduct()
    {
        if($this->_helperData->getConfig('general_settings/enable')) {
            $current_product = $this->_coreRegistry->registry('current_product');
            $currentProId = $current_product->getEntityId();
            $timediscount =  $this->timediscountFactory->create()->load($currentProId,'product_id');
            return $timediscount;
        }
        return false;
    }

    /**
     * get current product
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        $current_product = $this->_coreRegistry->registry('current_product');
        $currentProId = $current_product->getEntityId();
        $product =  $this->productRepository->getById($currentProId);
        return $product;
    }

    /**
     * get currency in format
     * @param float $price float
     * @return string
     *
     */
    public function formatPrice($price)
    {
        return $this->_priceHelper->convertAndFormat($price);
    }
}
