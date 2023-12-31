<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Slider
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Slider\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Lofmp\Slider\Helper
 */
class Data extends AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lofmp\Slider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var mixed|array|false
     */
    protected $_currentSlider = false;

    /**
     * Construct helper data
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param \Lofmp\Slider\Model\SliderFactory $sliderFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Lofmp\Slider\Model\SliderFactory $sliderFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->sliderFactory = $sliderFactory;
        parent::__construct($context);
    }

    /**
     * Get config
     *
     * @param string $path
     * @param int|string|null $storeId
     * @return mixed
     */
    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue("lofmpslider/" . $path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get current seller sliders
     *
     * @param mixed|Object|null $seller
     * @return mixed|array|false
     */
    public function getSlider($seller = null)
    {
        if (!$this->_currentSlider && $seller && $seller->getId()) {
            $slider = $this->sliderFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('is_active', 1)
                        ->addFieldToFilter('seller_id', $seller->getId())
                        ->getFirstItem();
            $this->_currentSlider =  $slider->getData();
        }
        return $this->_currentSlider;
    }
}
