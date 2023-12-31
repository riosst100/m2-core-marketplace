<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FeaturedProducts\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class ConfigData extends AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    const PATH_GENERAL_SETTING = 'featured_products_config/general/';

    const PATH_OWLCAROUSEL_SETTING = 'featured_products_config/owl_carousel_setting/';

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(Context $context,
                                ObjectManagerInterface $objectManager,
                                StoreManagerInterface $storeManager)
    {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->getConfigValue(self::PATH_GENERAL_SETTING . $field, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getOwlCarouselConfig($field, $storeId = null)
    {
        return $this->getConfigValue(self::PATH_OWLCAROUSEL_SETTING . $field, $storeId);
    }

    /**
     * @return bool
     */
    public function isEnable(){
        $status = $this->getGeneralConfig('enable');
        return $status == 0 ? false : true;
    }
}