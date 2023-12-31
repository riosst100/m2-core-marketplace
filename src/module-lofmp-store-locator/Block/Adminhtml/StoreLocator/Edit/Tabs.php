<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('storelocator_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('StoreLocator Information'));

        $this->addTab(
                'main_section',
                [
                    'label' => __('General Infomration'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Main')->toHtml()
                ]
            );
         $this->addTab(
                'gmap_section',
                [
                    'label' => __('Gmap Location'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Gmap')->toHtml()
                ]
            );

         $this->addTab(
                'categories_section',
                [
                    'label' => __('Categories'),
                    //'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Categories')->toHtml()
                    'url' => $this->getUrl('storelocator/storelocator/categories', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
         $this->addTab(
                'tags_section',
                [
                    'label' => __('Tags'),
                    //'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Tags')->toHtml()
                    'url' => $this->getUrl('storelocator/storelocator/tags', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
        $this->addTab(
                'images_section',
                [
                    'label' => __('Gallery Images'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Image')->toHtml()
                ]
            );
        $this->addTab(
                'open_section',
                [
                    'label' => __('Opening Time'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Open')->toHtml()
                ]
            );

        $this->addTab(
                'tag_section',
                [
                    'label' => __('Social'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Social')->toHtml()
                ]
            );
        $this->addTab(
                'tag_seo',
                [
                    'label' => __('SEO'),
                    'content' => $this->getLayout()->createBlock('Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Seo')->toHtml()
                ]
            );

        $this->setActiveTab("storelocator_tabs_main_section");
    }
}
