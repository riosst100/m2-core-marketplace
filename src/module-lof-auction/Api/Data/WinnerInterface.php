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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Api\Data;

/**
 * Interface WinnerInterface
 * @package Lof\Auction\Api\Data
 */
interface WinnerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SHOP = 'shop';
    const PRICE = 'price';
    const TIME_FOR_BUY = 'time_for_buy';
    const WINNER_MSG = 'winner_msg';

    /**
     * Get shop
     *
     * @return string|null
     */
    public function getShop();

    /**
     * Set shop
     *
     * @param string|null
     * @return $this
     */
    public function setShop($shop);

    /**
     * Set price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param float
     * @return $this
     */
    public function setPrice($price);

    /**
     * Set time_for_buy
     *
     * @return int
     */
    public function getTimeForBuy();

    /**
     * Set time_for_buy
     *
     * @param int
     * @return $this
     */
    public function setTimeForBuy($time_for_buy);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Auction\Api\Data\WinnerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Auction\Api\Data\WinnerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\WinnerExtensionInterface $extensionAttributes
    );
}
