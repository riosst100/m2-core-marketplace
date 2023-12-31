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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Block;

use Exception;

/**
 * Class AuctionDetail
 * @package Lof\Auction\Block
 */
class AuctionDetail extends Product
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        $template = $this->getTemplate();
        if (!$template) {
            $this->setTemplate('Lof_Auction::auction-info.phtml');
        }
        return parent::_toHtml();
    }
}
