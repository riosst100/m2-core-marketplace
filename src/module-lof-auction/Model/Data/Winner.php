<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Auction\Model\Data;

use Lof\Auction\Api\Data\WinnerInterface;

class Winner extends \Magento\Framework\Api\AbstractExtensibleObject implements WinnerInterface
{

    /**
     * {@inheritdoc}
     */
    public function getShop()
    {
        return $this->_get(self::SHOP);
    }

    /**
     * {@inheritdoc}
     */
    public function setShop($shop)
    {
        return $this->setData(self::SHOP, $shop);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeForBuy()
    {
        return $this->_get(self::TIME_FOR_BUY);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeForBuy($time_for_buy)
    {
        return $this->setData(self::TIME_FOR_BUY, $time_for_buy);
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
        \Lof\Auction\Api\Data\WinnerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

