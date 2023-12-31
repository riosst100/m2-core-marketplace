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
 * @package    Lofmp_Permissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model\Menu;

class Item extends \Lof\MarketPlace\Model\Menu\Item
{

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    private $sellerContext;

    /**
     * Item constructor.
     * @param \Magento\Backend\Model\Menu\Item\Validator $validator
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Lof\MarketPlace\Model\UrlInterface $urlInterface
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Item\Validator $validator,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\UrlInterface $urlModel,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Lof\MarketPlace\Model\UrlInterface $urlInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        array $data = []
    ) {
        parent::__construct(
            $validator,
            $authorization,
            $scopeConfig,
            $menuFactory,
            $urlModel,
            $moduleList,
            $moduleManager,
            $urlInterface,
            $eventManager,
            $data
        );
        $this->sellerContext = $sellerContext;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        if (!$this->sellerContext->isModuleActive()) {
            return parent::isAllowed();
        }

        try {
            return $this->sellerContext->isResourceAllowed($this->_resource);
        } catch (\Exception $e) {

            return false;
        }
    }
}
