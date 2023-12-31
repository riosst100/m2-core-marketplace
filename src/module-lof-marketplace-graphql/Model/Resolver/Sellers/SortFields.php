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
 * @package    Lof_MarketplaceGraphQl
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketplaceGraphQl\Model\Resolver\Sellers;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

/**
 * Retrieves the sort fields data
 */
class SortFields implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $sortFieldsOptions = [
            ['label' => "position", 'value' => "position"],
            ['label' => "creation_time", 'value' => "creation_time"],
            ['label' => "update_time", 'value' => "update_time"],
            ['label' => "total_sold", 'value' => "total_sold"],
            ['label' => "product_count", 'value' => "product_count"],
            ['label' => "name", 'value' => "name"],
            ['label' => "shop_title", 'value' => "shop_title"],
            ['label' => "sale", 'value' => "sale"],
            ['label' => "country_id", 'value' => "country_id"],
            ['label' => "region", 'value' => "region"],
            ['label' => "status", 'value' => "status"]
        ];
        
        $data = [
            'default' => "position",
            'options' => $sortFieldsOptions,
        ];

        return $data;
    }
}
