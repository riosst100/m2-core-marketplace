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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Block\Adminhtml\SellerBadge\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class FlushCacheButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Flush Cache'),
                'class' => 'flushcache',
                'on_click' => 'setLocation(\'' . $this->getFlushCacheUrl() . '\')',
                'sort_order' => 70,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getFlushCacheUrl()
    {
        return $this->getUrl('*/*/flushcache', ['badge_id' => $this->getModelId()]);
    }
}
