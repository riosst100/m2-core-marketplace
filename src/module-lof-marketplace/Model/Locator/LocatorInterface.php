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

namespace Lof\MarketPlace\Model\Locator;

use Lof\MarketPlace\Api\Data\ProductInterface;
use Magento\Store\Api\Data\StoreInterface;

interface LocatorInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @return array
     */
    public function getWebsiteIds();

    /**
     * @return string
     */
    public function getBaseCurrencyCode();
}
