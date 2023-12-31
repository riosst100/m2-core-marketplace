<?php
/**
 * LandofCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandofCoder
 * @package    Lofmp_PreOrder
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PreOrder\Helper;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Data extends \Lof\PreOrder\Helper\Data
{
    /**
     * Get config allow seller manager
     * @return int|boolean|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function allowSellerManage($storeId = null)
    {
        return (int)$this->getConfig("settings/allow_seller_manage");
    }

    /**
     * @return bool|int|null
     */
    public function showPreorderOption()
    {
        return $this->allowSellerManage();
    }
}
