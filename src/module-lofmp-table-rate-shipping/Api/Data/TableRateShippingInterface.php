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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Api\Data;

interface TableRateShippingInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LOFMPSHIPPING_ID = 'lofmpshipping_id';
    /**#@-*/

    /**
     * Get TableRateShipping ID
     *
     * @return int|null
     */
    public function getLofmpshippingId();

    /**
     * Set TableRateShipping ID
     *
     * @param int $id
     * @return \Lofmp\TableRateShipping\Api\Data\TableRateShippingInterface
     */
    public function setLofmpshippingId($id);
}
