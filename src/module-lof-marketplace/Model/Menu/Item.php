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

namespace Lof\MarketPlace\Model\Menu;

class Item extends \Magento\Backend\Model\Menu\Item
{
    /**
     * @var mixed|null
     */
    protected $_icon_class;

    /**
     * @var \Lof\MarketPlace\Model\UrlInterface
     */
    protected $urlInterface;

    /**
     * System event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Menu item target
     *
     * @var string|null
     */
    private $target;

    /**
     * Item constructor.
     *
     * @param \Magento\Backend\Model\Menu\Item\Validator $validator
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Lof\MarketPlace\Model\UrlInterface $urlInterface
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
            $data
        );

        $this->_eventManager = $eventManager;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Retrieve icon class
     *
     * @return string
     */
    public function getIconClass()
    {
        return $this->_icon_class;
    }

    /**
     * Check whether item is allowed to the user
     *
     * @return bool
     */
    public function isAllowed()
    {
        // @phpstan-ignore-next-line
        $result = new \Magento\FrameWork\DataObject(['is_allowed' => true]);
        return $result->getIsAllowed();
    }

    /**
     * Retrieve menu item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ((bool)$this->_action) {
            return $this->urlInterface->getUrl(
                (string)$this->_action,
                ['_cache_secret_key' => true]
            );
        }
        return '#';
    }

    public function __wakeup()
    {
        // @phpstan-ignore-next-line
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->urlInterface = $objectManager->get(\Lof\MarketPlace\Model\UrlInterface::class);
        $this->_eventManager = $objectManager->get(\Magento\Framework\Event\ManagerInterface::class);
    }

    /**
     * @return mixed
     */
    public function __sleep()
    {
        // @phpstan-ignore-next-line
        $result = parent::__sleep();
        $result[] = '_icon_class';
        return $result;
    }

    /**
     * Get menu item data represented as an array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'parent_id' => $this->_parentId,
            'icon' => $this->_icon_class,
            'module' => $this->_moduleName,
            'sort_index' => $this->_sortIndex,
            'dependsOnConfig' => $this->_dependsOnConfig,
            'id' => $this->_id,
            'resource' => $this->_resource,
            'path' => $this->_path,
            'action' => $this->_action,
            'dependsOnModule' => $this->_dependsOnModule,
            'toolTip' => $this->_tooltip,
            'title' => $this->_title,
            'target' => $this->target,
            'sub_menu' => isset($this->_submenu) ? $this->_submenu->toArray() : null
        ];
    }

    /**
     * Populate the menu item with data from array
     *
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        $this->_parentId = $this->_getArgument($data, 'parent_id');
        $this->_icon_class = $this->_getArgument($data, 'icon');
        $this->_moduleName = $this->_getArgument($data, 'module', 'Magento_Backend');
        $this->_sortIndex = $this->_getArgument($data, 'sort_index');
        $this->_dependsOnConfig = $this->_getArgument($data, 'dependsOnConfig');
        $this->_id = $this->_getArgument($data, 'id');
        $this->_resource = $this->_getArgument($data, 'resource');
        $this->_path = $this->_getArgument($data, 'path', '');
        $this->_action = $this->_getArgument($data, 'action');
        $this->_dependsOnModule = $this->_getArgument($data, 'dependsOnModule');
        $this->_tooltip = $this->_getArgument($data, 'toolTip');
        $this->_title = $this->_getArgument($data, 'title');
        $this->target = $this->_getArgument($data, 'target');
        $this->_submenu = null;
        if (isset($data['sub_menu'])) {
            $menu = $this->_menuFactory->create();
            $menu->populateFromArray($data['sub_menu']);
            $this->_submenu = $menu;
        }
    }
}
