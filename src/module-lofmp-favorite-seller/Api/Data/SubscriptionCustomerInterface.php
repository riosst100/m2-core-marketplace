<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Api\Data;

interface SubscriptionCustomerInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const SELLER_ID     = 'seller_id';
    const CUSTOMER_ID   = 'customer_id';
    const CREATION_TIME = 'creation_time';
    const STATUS        = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Seller ID
     *
     * @return int|null
     */
    public  function getSellerId();

    /**
     * Get Customer ID
     *
     * @return int|null
     */
    public  function getCustomerId();

    /**
     * Get creation time
     *
     * @return date|null
     */
    public  function getCreationTime();

    /**
     * Get creation time
     *
     * @return int|null
     */
    public  function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface
     */
    public function setId($id);

    /**
     * Set Seller Id
     *
     * @param int $sellerId
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface
     */
    public function setSellerId($sellerId);

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface
     */
    public function setCustomerId($customerId);

    /**
     * Set Creation Time
     *
     * @param int $creationTime
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set Status
     *
     * @param int $status
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface
     */
    public function setStatus($status);
}
