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

namespace Lof\MarketPlace\Block\Seller\Product\Helper\Form\Gallery;

class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getRequest()->getControllerModule() === 'Lof_MarketPlace') {
            $this->addChild('uploader', \Lof\MarketPlace\Block\Seller\Product\Helper\Form\Gallery\Uploader::class);

            $this->getUploader()->getConfig()->setUrl(
                $this->_urlBuilder->getUrl('catalog/product_gallery/upload')
            )->setFileField(
                'image'
            )->setFilters(
                [
                    'images' => [
                        'label' => __('Images (.gif, .jpg, .png)'),
                        'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                    ],
                ]
            );

            $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);

            return \Magento\Framework\View\Element\AbstractBlock::_prepareLayout();
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getRequest()->getControllerModule() === 'Lof_MarketPlace') {
            return 'Lof_MarketPlace::catalog/product/helper/gallery.phtml';
        }

        return parent::getTemplate();
    }
}
