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
 * Interface EndAuctionInterface
 * @package Lof\Auction\Api\Data
 */
interface EndAuctionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const WINNER = 'winner';
    const WATTING_USER = 'watting_user';

    /**
     * Get winner
     *
     * @return \Lof\Auction\Api\Data\WinnerInterface|null
     */
    public function getWinner();

    /**
     * Set winner
     *
     * @param \Lof\Auction\Api\Data\WinnerInterface|null
     * @return $this
     */
    public function setWinner($winner);

    /**
     * Set watting_user
     *
     * @return \Lof\Auction\Api\Data\WattingUserInterface|null
     */
    public function getWattingUser();

    /**
     * Set watting_user
     *
     * @param \Lof\Auction\Api\Data\WattingUserInterface|null
     * @return $this
     */
    public function setWattingUser($watting_user);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Auction\Api\Data\EndAuctionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Auction\Api\Data\EndAuctionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\EndAuctionExtensionInterface $extensionAttributes
    );
}
