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
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Block\Cancelrequest;

class Send extends \Lofmp\SellerMembership\Block\Cancelrequest\Index
{
    /**
     * @return bool
     */
    public function showForm()
    {
        $cancelRequestPending = $this->getCancelRequestPending();
        if ($cancelRequestPending && count($cancelRequestPending)) {
            return false;
        }
        return true;
    }
}
