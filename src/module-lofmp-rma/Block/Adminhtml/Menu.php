<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */




namespace Lofmp\Rma\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;

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
                'rma' => [
                    'title' => __('Manage RMA'),
                    'url' => $this->getUrl('*/rma/rma'),
                    'resource' => 'Lofmp_Rma::rma_rma',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New RMA'),
                            'url'      => $this->getUrl('*/rma/add'),
                            'resource' => 'Lofmp_Rma::rma_edit',
                           ]
                       ]
                ],
                'status' => [
                    'title' => __('Manage Statuses'),
                    'url' => $this->getUrl('*/status/index'),
                    'resource' => 'Lofmp_Rma::rma_dictionary_status',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Status'),
                            'url'      => $this->getUrl('*/status/add'),
                            'resource' => 'Lofmp_Rma::status_edit',
                           ]
                       ]
                ],
                 'reason' => [
                    'title' => __('Manage Reasons'),
                    'url' => $this->getUrl('*/reason/index'),
                    'resource' => 'Lofmp_Rma::rma_dictionary_reason',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Reason'),
                            'url'      => $this->getUrl('*/reason/add'),
                            'resource' => 'Lofmp_Rma::reason_edit',
                           ]
                       ]
                ],
                'resolution' => [
                    'title' => __('Manage Resolutions'),
                    'url' => $this->getUrl('*/resolution/index'),
                    'resource' => 'Lofmp_Rma::rma_dictionary_resolution',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Reason'),
                            'url'      => $this->getUrl('*/resolution/add'),
                            'resource' => 'Lofmp_Rma::resolution_edit',
                           ]
                       ]
                ],
                'condition' => [
                    'title' => __('Manage Conditions'),
                    'url' => $this->getUrl('*/Condition/index'),
                    'resource' => 'Lofmp_Rma::rma_dictionary_condition',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Condition'),
                            'url'      => $this->getUrl('*/condition/add'),
                            'resource' => 'Lofmp_Rma::condition_edit',
                           ]
                       ]
                ],
                'rule' => [
                    'title' => __('Manage Rules'),
                    'url' => $this->getUrl('*/rule/index'),
                    'resource' => 'Lofmp_Rma::rma_rule',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Rules'),
                            'url'      => $this->getUrl('*/rule/add'),
                            'resource' => 'Lofmp_Rma::rule_edit',
                           ]
                       ]
                ],
                'report' => [
                    'title' => __('Manage Reports'),
                    'url' => $this->getUrl('*/report/view'),
                    'resource' => 'Lofmp_Rma::rma_report',
                    'child'    => [
                        'newAction' => [
                            'title'    => __('New Rules'),
                            'url'      => $this->getUrl('*/rule/add'),
                            'resource' => 'Lofmp_Rma::rule_edit',
                           ]
                       ]
                ],
                'system_config' => [
                    'title' => __('RMA Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'rma']),
                    'resource' => 'Lofmp_Rma::config_rma'
                ],
                'readme' => [
                    'title' => __('Guide'),
                    'url' => 'http://guide.landofcoder.com/rma/',
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
        return $items['rma'];
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
