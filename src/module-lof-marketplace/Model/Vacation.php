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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\SellerVacationInterface;

class Vacation extends \Magento\Framework\Model\AbstractModel implements SellerVacationInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_vacation';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Vacation::class);
    }

    /**
     * @return int
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @param string $seller_id
     * @return Vacation|string|null
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * @return mixed|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $created_at
     * @return Vacation|string|null
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @return mixed|string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string $updated_at
     * @return Vacation|string|null
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * @return mixed|string|null
     */
    public function getVacationId()
    {
        return $this->getData(self::VACATION_ID);
    }

    /**
     * @param string $vacation_id
     * @return Vacation|string|null
     */
    public function setVacationId($vacation_id)
    {
        return $this->setData(self::VACATION_ID, $vacation_id);
    }

    /**
     * @return mixed|string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return Vacation|string|null
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return mixed|string|null
     */
    public function getVacationMessage()
    {
        return $this->getData(self::VACATION_MESSAGE);
    }

    /**
     * @param string $vacation_message
     * @return Vacation|string|null
     */
    public function setVacationMessage($vacation_message)
    {
        return $this->setData(self::VACATION_MESSAGE, $vacation_message);
    }

    /**
     * @return mixed|string|null
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * @param string $from_date
     * @return Vacation|string|null
     */
    public function setFromDate($from_date)
    {
        return $this->setData(self::FROM_DATE, $from_date);
    }

    /**
     * @return mixed|string|null
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * @param string $to_date
     * @return Vacation|string|null
     */
    public function setToDate($to_date)
    {
        return $this->setData(self::TO_DATE, $to_date);
    }

    /**
     * @return mixed|string|null
     */
    public function getTextAddCart()
    {
        return $this->getData(self::TEXT_ADD_CART);
    }

    /**
     * @param string $text_add_cart
     * @return Vacation|string|null
     */
    public function setTextAddCart($text_add_cart)
    {
        return $this->setData(self::TEXT_ADD_CART, $text_add_cart);
    }

}
