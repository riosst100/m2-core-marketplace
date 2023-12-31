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
 * @package    Lof_SellerCommunity
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\SellerCommunity\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class PreviewButton
 */
class PreviewButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getObjectId()) {
            $data = [
                'label' => __('Preview'),
                'class' => 'preview',
                'on_click' => 'window.open(\'' . $this->getPreviewUrl() . '\');',
                'sort_order' => 35,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview', ['id' => $this->getObjectId()]);
    }
}
