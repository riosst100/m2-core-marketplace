<?php

namespace CoreMarketplace\MarketPlace\Block\Adminhtml\Seller\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Seller Information'));
    }

    /**
     * @return Tabs|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'general',
            [
                'label' => __('Seller Information'),
                'content' => $this->getLayout()
                    ->createBlock(\CoreMarketplace\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Main::class)->toHtml()
            ]
        );

        $this->addTab(
            'social',
            [
                'label' => __('Social Information'),
                'content' => $this->getLayout()
                    ->createBlock(\Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Social::class)->toHtml()
            ]
        );

        $this->addTab(
            'products',
            [
                'label' => __('Products'),
                'url' => $this->getUrl('lofmarketplace/*/products', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

        // $this->addTab(
        //     'design',
        //     [
        //         'label' => __('Design'),
        //         'content' => $this->getLayout()
        //             ->createBlock(\Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Design::class)->toHtml()
        //     ]
        // );

        $this->addTab(
            'meta',
            [
                'label' => __('Meta Data'),
                'content' => $this->getLayout()
                    ->createBlock(\Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Meta::class)->toHtml()
            ]
        );

        // $this->addTab(
        //     'extrafields',
        //     [
        //         'label' => __('Extra Fields'),
        //         'content' => $this->getLayout()
        //             ->createBlock(\Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Extra::class)->toHtml()
        //     ]
        // );
    }
}
