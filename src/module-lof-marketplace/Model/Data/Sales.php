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

namespace Lof\MarketPlace\Model\Data;

use Lof\MarketPlace\Api\Data\SalesInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class Sales extends \Magento\Framework\Api\AbstractExtensibleObject implements SalesInterface
{
    const KEY_SELLER_ID = 'seller_id';
    const KEY_ORDER_ID = 'order_id';

    /**
     * @return mixed|string|null
     */
    public function getSellerId()
    {
        return $this->_get(self::KEY_SELLER_ID);
    }

    /**
     * @param $sellerId
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|Sales
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::KEY_SELLER_ID, $sellerId);
    }

    /**
     * @return mixed|string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::KEY_ORDER_ID);
    }

    /**
     * @param string $order_id
     * @return Sales|string|null
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::KEY_ORDER_ID, $order_id);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Lof\Marketplace\Api\Data\SalesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
