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

namespace Lof\MarketPlace\Api\Data;

interface GroupInterface
{
    const ID = 'id';
    const GROUP_ID = 'group_id';
    const NAME = 'name';
    const URL_KEY = 'url_key';
    const POSITION = 'position';
    const STATUS = 'status';
    const SHOW_IN_SIDEBAR = 'shown_in_sidebar';
    const LIMIT_PRODUCT = '	limit_product';
    const CAN_ADD_PRODUCT = 'can_add_product';
    const CAN_USE_SHIPPING = 'can_use_shipping';
    const CAN_USE_MESSAGE = 'can_use_message';
    const CAN_USE_VACATION = 'can_use_vacation';
    const CAN_USE_WITHDRAWAL = 'can_use_withdrawal';

    /**
     * Get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $group_id
     * @return $this
     */
    public function setGroupId($group_id);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $url_key
     * @return $this
     */
    public function setUrlKey($url_key);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get position
     * @return int|null
     */
    public function getPosition();

    /**
     * Set position
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get show_in_sidebar
     * @return int|null
     */
    public function getShowInSidebar();

    /**
     * Set show_in_sidebar
     * @param int $show_in_sidebar
     * @return $this
     */
    public function setShowInSidebar($show_in_sidebar);

    /**
     * Get limit_product
     * @return int|null
     */
    public function getLimitProduct();

    /**
     * Set limit_product
     * @param int $limit_product
     * @return $this
     */
    public function setLimitProduct($limit_product);

    /**
     * Get can_add_product
     * @return int|null
     */
    public function getCanAddProduct();

    /**
     * Set can_add_product
     * @param int $can_add_product
     * @return $this
     */
    public function setCanAddProduct($can_add_product);

    /**
     * Get can_use_shipping
     * @return int|null
     */
    public function getCanUseShiping();

    /**
     * Set can_use_shipping
     * @param int $can_use_shipping
     * @return $this
     */
    public function setCanUseShiping($can_use_shipping);

    /**
     * Get can_use_message
     * @return int|null
     */
    public function getCanUseMessage();

    /**
     * Set can_use_message
     * @param int $can_use_message
     * @return $this
     */
    public function setCanUseMessage($can_use_message);

    /**
     * Get can_use_vacation
     * @return int|null
     */
    public function getCanUseVacation();

    /**
     * Set can_use_vacation
     * @param int $can_use_vacation
     * @return $this
     */
    public function setCanUseVacation($can_use_vacation);

    /**
     * Get can_use_withdrawal
     * @return int|null
     */
    public function getCanUseWithdrawal();

    /**
     * Set can_use_withdrawal
     * @param int $can_use_withdrawal
     * @return $this
     */
    public function setCanUseWithdrawal($can_use_withdrawal);
}
