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

class ReIndexButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Re-Index'),
                'class' => 'reindex',
                'on_click'=> 'setLocation(\'' . $this->getReindexUrl() . '\')',
                'sort_order' => 15,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getReindexUrl()
    {
        return $this->getUrl('lofmp_sellerbadge/sellerbadge/reindex', ['id' => $this->getModelId()]);
    }
}
