<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Api\Data;

use Lofmp\Rma\Api;

/**
 * @method Api\Data\RmaSearchResultsInterface getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
 */
interface RmaFrontendInterface extends RmaInterface
{
    const KEY_ITEMS = 'items';

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getSellerId();

    /**
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * @return string
     */
    public function getReturnAddress();

    /**
     * @param string $address
     * @return $this
     */
    public function setReturnAddress($address);

    /**
     * @return \Lofmp\Rma\Api\Data\ItemInterface[]
     */
    public function getItems();

    /**
     * @param \Lofmp\Rma\Api\Data\ItemInterface[]|array|mixed $items
     * @return $this
     */
    public function setItems($items);
}
