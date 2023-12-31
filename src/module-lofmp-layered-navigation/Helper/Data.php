<?php

namespace Lofmp\LayeredNavigation\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_objectManager;
    protected $_storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getConfig($key, $store = null)
    {
        if (!$store) {
            $store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
        }
        $store = $this->_storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            'lofmp_layerednavigation/' . $key,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * Is enabled
     * 
     * @return int|bool
     */
    public function isEnabled()
    {
        return (int)$this->getConfig("general/enabled");
    }
}
