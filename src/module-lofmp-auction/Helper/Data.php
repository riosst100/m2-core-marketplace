<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Auction\Helper;

use Lof\Auction\Helper\Data as AuctionHelperData;
/**
 * Class Data
 * @package Lofmp\Auction\Helper
 */
class Data extends AuctionHelperData
{
    public function isEnabledForSeller()
    {
        return $this->getConfig('general_settings/enable') && $this->getConfig('general_settings/allow_seller_manage') ? true : false;
    }
}