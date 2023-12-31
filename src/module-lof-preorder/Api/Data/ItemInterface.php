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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Api\Data;

interface ItemInterface
{

    const ID = 'id';
    const ITEM_ID = 'item_id';
    const PREORDER_PERCENT = 'preorder_percent';

    /**
     * Set item_id
     * @param int $item_id
     * @return \Lof\PreOrder\Api\Data\ItemInterface
     */
    public function setItemId($item_id);

    /**
     * Get item_id
     * @return int|null
     */
    public function getItemId();

    /**
     * Set preorder_percent
     * @param float $preorder_percent
     * @return \Lof\PreOrder\Api\Data\ItemInterface
     */
    public function setPreorderPercent($preorder_percent);

    /**
     * Get preorder_percent
     * @return float|null
     */
    public function getPreorderPercent();
}
