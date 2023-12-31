<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Block\Adminhtml;

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
                'rule' => [
                    'title' => __('Manage Rules'),
                    'url' => $this->getUrl('*/rule/index'),
                    'resource' => 'Lofmp_CouponCode::rule',
                    'child' => [
                        'newAction' => [
                            'title' => __('New Rule'),
                            'url' => $this->getUrl('*/rule/newAction'),
                            'resource' => 'Lofmp_CouponCode::rule_edit',
                        ]
                    ]
                ],
                'coupon' => [
                    'title' => __('Manage Coupon Code'),
                    'url' => $this->getUrl('*/coupon/index'),
                    'resource' => 'Lofmp_CouponCode::coupon'
                ],
                'log' => [
                    'title' => __('Manage Coupon Logs'),
                    'url' => $this->getUrl('*/log/index'),
                    'resource' => 'Lofmp_CouponCode::log'
                ],
                'import' => [
                    'title' => __('Import Coupon Codes'),
                    'url' => $this->getUrl('*/import/index'),
                    'resource' => 'Lofmp_CouponCode::coupon_import'
                ],
                'generate' => [
                    'title' => __('Generate Coupon Code'),
                    'url' => $this->getUrl('*/generate/index'),
                    'resource' => 'Lofmp_CouponCode::generate'
                ],
                'report_sales' => [
                    'title' => __('Report'),
                    'url' => $this->getUrl('*/report_sales/coupon'),
                    'resource' => 'Lofmp_CouponCode::report'
                ],
                'settings' => [
                    'title'    => __('Settings'),
                    'url'      => $this->getUrl('adminhtml/system_config/edit/section/lofmpcouponcode'),
                    'resource' => 'Lofmp_CouponCode::config_couponcode',
                    ],
                'readme' => [
                    'title' => __('Guide'),
                    'url' => 'http://guide.landofcoder.com/lof-couponcode-magento2',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'https://landofcoder.ticksy.com',
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
        return $items['couponcode_rule'];
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
