<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Lof_All::menu.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                'storelocator' => [
                    'title' => __('Manage Store Locators'),
                    'url' => $this->getUrl('*/storelocator/index'),
                    'resource' => 'Lofmp_StoreLocator::storelocator',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Store'),
                            'url'      => $this->getUrl('*/storelocator/new'),
                            'resource' => 'Lofmp_StoreLocator::storelocator_edit',
                           ]
                       ]
                ],
                'category' => [
                    'title' => __('Manage Categories'),
                    'url' => $this->getUrl('*/category/index'),
                    'resource' => 'Lofmp_StoreLocator::category',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Category'),
                            'url'      => $this->getUrl('*/category/new'),
                            'resource' => 'Lofmp_StoreLocator::category_edit',
                           ]
                       ]
                ],
                'tag' => [
                    'title' => __('Manage Tags'),
                    'url' => $this->getUrl('*/tag/index'),
                    'resource' => 'Lofmp_StoreLocator::tag',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Tag'),
                            'url'      => $this->getUrl('*/tag/new'),
                            'resource' => 'Lofmp_StoreLocator::tag_edit',
                           ]
                       ]
                ],
                'system_config' => [
                    'title' => __('StoreLocator Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'storelocator']),
                    'resource' => 'Lofmp_StoreLocator::config_storelocator'
                ],
                'readme' => [
                    'title' => __('Guide'),
                    'url' => 'http://guide.landofcoder.com/storelocator/',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'https://venustheme.ticksy.com',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]
            ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }
        return $this->items;
    }

    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }
        return $items['storelocator'];
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }
        return $result;
    }

    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }
}
