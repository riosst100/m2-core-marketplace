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

namespace Lof\MarketPlace\Model\Source\Shipping;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Methods extends \Lof\MarketPlace\Model\System\Config\Source\AbstractBlock
{
    const XML_PATH_LOFMP_SHIPPING_SHIPPING_METHODS = 'lofmp_shipping/shipping/methods';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketplaceHelperData;

    /**
     * Methods constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelperData
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory, $objectManager);
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_marketplaceHelperData = $marketplaceHelperData;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMethods()
    {
        $rates = $this->scopeConfig->getValue(
            self::XML_PATH_LOFMP_SHIPPING_SHIPPING_METHODS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_marketplaceHelperData->getStore()
        );

        $allowedMethods = [];
        if (is_array($rates) && count($rates) > 0) {
            foreach ($rates as $code => $method) {
                if ($this->_marketplaceHelperData->getStoreConfig(
                    $method['config_path'],
                    $this->_marketplaceHelperData->getStore()->getId()
                )) {
                    $allowedMethods[$code] = $rates[$code];
                }
            }
        }

        return $allowedMethods;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $methods = $this->getMethods();
        $options = [];
        if ($methods) {
            $methods = array_keys($methods);
            foreach ($methods as $method) {
                $method = strtolower(trim($method));
                $options[] = ['value' => $method, 'label' => __(ucfirst($method))];
            }
        }
        return $options;
    }
}
