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

namespace Lofmp\SellerBadge\Block\Adminhtml\Seller\Edit\Tab;

class BadgeTab extends \Lofmp\SellerBadge\Block\AbstractBadge implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Lofmp_SellerBadge::seller/tab/badge.phtml';

    /**
     * @return bool
     */
    public function canDisplay()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Badges');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Badges');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return $this->canDisplay();
    }

    /**
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
