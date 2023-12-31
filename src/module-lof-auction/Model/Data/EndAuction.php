<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Auction\Model\Data;

use Lof\Auction\Api\Data\EndAuctionInterface;

class EndAuction extends \Magento\Framework\Api\AbstractExtensibleObject implements EndAuctionInterface
{

    /**
     * {@inheritdoc}
     */
    public function getWinner()
    {
        return $this->_get(self::WINNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setWinner($winner)
    {
        return $this->setData(self::WINNER, $winner);
    }

    /**
     * {@inheritdoc}
     */
    public function getWattingUser()
    {
        return $this->_get(self::WATTING_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function setWattingUser($waitting_user)
    {
        return $this->setData(self::WATTING_USER, $waitting_user);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\EndAuctionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

