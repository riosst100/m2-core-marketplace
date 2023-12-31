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

namespace Lof\MarketPlace\Block\Seller\Product\Edit;

class NewVideo extends \Magento\ProductVideo\Block\Adminhtml\Product\Edit\NewVideo
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getRequest()->getControllerModule() === 'Lof_MarketPlace') {
            return 'Lof_MarketPlace::product/edit/slideout/form.phtml';
        }

        return parent::getTemplate();
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonEncoder->encode(
            [
                'saveVideoUrl' => $this->getUrl('catalog/product_gallery/upload'),
                'saveRemoteVideoUrl' => $this->getUrl('catalog/product_gallery/retrieveImage'),
                'htmlId' => $this->getHtmlId(),
                'youTubeApiKey' => $this->mediaHelper->getYouTubeApiKey(),
                'videoSelector' => $this->videoSelector
            ]
        );
    }

    /**
     * Get note for video url
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getNoteVideoUrl()
    {
        $result = __('YouTube and Vimeo supported.');
        if ($this->mediaHelper->getYouTubeApiKey() === null) {
            $result = __('Please contact Admin to request this feature.');
        }
        return $result;
    }
}
