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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Api\Data;

interface FlatRateShippingInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LOFMPSHIPPING_ID = 'lofmpshipping_id';
    /**#@-*/

    /**
     * Get FlatRateShipping ID
     *
     * @return int|null
     */
    public function getLofmpshippingId();

    /**
     * Set FlatRateShipping ID
     *
     * @param int $id
     * @return \Lofmp\FlatRateShipping\Api\Data\FlatRateShippingInterface
     */
    public function setLofmpshippingId($id);
}
