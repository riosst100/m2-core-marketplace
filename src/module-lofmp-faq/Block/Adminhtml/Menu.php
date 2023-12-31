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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Faq\Block\Adminhtml;

use Lof\All\Model\Config;

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
        parent::__construct($context);
    }//end __construct()


    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                      'question'    => [
                                     'title'    => __('Manage Questions'),
                                     'url'      => $this->getUrl('*/question/index'),
                                     'resource' => 'Lofmp_Faq::question',
                                     'child'    => [
                                            'newAction' => [
                                                            'title'    => __('New Question'),
                                                            'url'      => $this->getUrl('*/question/new'),
                                                            'resource' => 'Lofmp_Faq::question_edit',
                                                           ],
                                     ],
                                    ],
                       'category' => [
                                     'title'    => __('Manage Categories'),
                                     'url'      => $this->getUrl('*/category/index'),
                                     'resource' => 'Lofmp_Faq::category',
                                     'child'    => [
                                                    'newAction' => [
                                                        'title'    => __('New Category'),
                                                        'url'      => $this->getUrl('*/category/newAction'),
                                                        'resource' => 'Lofmp_Faq::category_edit',
                                                       ]
                                                   ]
                                    ],
                      'settings' => [
                                     'title'    => __('Settings'),
                                     'url'      => $this->getUrl('adminhtml/system_config/edit/section/lofmpfaq'),
                                     'resource' => 'Lofmp_Faq::config_faq',
                                    ],
                      'readme'   => [
                                     'title'     => __('Guide'),
                                     'url'       => Config::FAQ_GUIDE,
                                     'attr'      => ['target' => '_blank'],
                                     'separator' => true,
                                    ],
                      'support'  => [
                                     'title' => __('Get Support'),
                                     'url'   => Config::LANDOFCODER_TICKET,
                                     'attr'  => ['target' => '_blank'],
                                    ],
                     ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }

            $this->items = $items;
        }//end if

        return $this->items;
    }//end getMenuItems()


    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items          = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }
        return $items['page'];
    }//end getCurrentItem()


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
    }//end renderAttributes()


    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }//end isCurrent()
}//end class
