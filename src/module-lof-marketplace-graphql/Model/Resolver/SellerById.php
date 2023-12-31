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

namespace Lof\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class SellerById
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
class SellerById extends AbstractSellerQuery implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->_labelFlag = 1;
        $this->validateArgs($args);
        $isGetProducts = isset($args['get_products']) ? (bool)$args['get_products'] : false;
        $isGetOtherInfo = isset($args['get_other_info']) ? (bool)$args['get_other_info'] : false;
        $sellerData = $this->_sellerRepository->get((int)$args['seller_id'], $isGetOtherInfo, $isGetProducts);
        $data = $sellerData ? $sellerData->__toArray() : [];
        $data["model"] = $sellerData;

        return $data;
    }
}
