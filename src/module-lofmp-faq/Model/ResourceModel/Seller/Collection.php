<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Model\ResourceModel\Seller;

use Lof\MarketPlace\Model\ResourceModel\Seller\Collection as SellerCollection;

class Collection extends SellerCollection
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['enable_seller_table' => $this->getTable('lofmp_faq_enable_seller')],
            'main_table.seller_id = enable_seller_table.seller_id',
            ['faq_status' => 'COALESCE(enable_seller_table.status, 0)']
        );
    }
}