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

namespace Lof\MarketPlace\Block;

use \Magento\Catalog\Block\Product\ProductList\Toolbar as SellerToolbar;

class Toolbar extends SellerToolbar
{
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('seller_list_toolbar_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );
            return $pagerBlock->toHtml();
        }

        return '';
    }
}
