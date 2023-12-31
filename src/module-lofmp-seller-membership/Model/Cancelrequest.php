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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Model;

use Lofmp\SellerMembership\Api\Data\CancelrequestInterface;
use Magento\Framework\Model\AbstractModel;

class Cancelrequest extends AbstractModel implements CancelrequestInterface
{
    const PENDING = 0;
    const APPROVED = 1;
    const CHECKING = 2;
    const DECLIDED = 3;

    /**
     * Cancelrequest constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Lofmp\SellerMembership\Model\ResourceModel\Cancelrequest::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipId()
    {
        return $this->getData(self::MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipId($membership_id)
    {
        return $this->setData(self::MEMBERSHIP_ID, $membership_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerComment()
    {
        return $this->getData(self::CUSTOMER_COMMENT);
    }

    /**
     * {@inheriq qtdoc}
     */
    public function setCustomerComment($customer_comment)
    {
        return $this->setData(self::CUSTOMER_COMMENT, $customer_comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminComment()
    {
        return $this->getData(self::ADMIN_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminComment($admin_comment)
    {
        return $this->setData(self::ADMIN_COMMENT, $admin_comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($creation_time)
    {
        return $this->setData(self::CREATION_TIME, $creation_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration()
    {
        return $this->getData(self::DURATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDuration($duration)
    {
        return $this->setData(self::DURATION, $duration);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
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
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * Convert object data from Array
     * @param mixed $data_array
     * @return \Lofmp\SellerMembership\Model\Cancelrequest
     */
    public function convertFromArray($data_array = [])
    {
        if (isset($data_array["membership_id"])) {
            $this->setMembershipId($data_array["membership_id"]);
        }
        if (isset($data_array["customer_comment"])) {
            $this->setCustomerComment($data_array["customer_comment"]);
        }
        if (isset($data_array["name"])) {
            $this->setName($data_array["name"]);
        }
        if (isset($data_array["duration"])) {
            $this->setDuration($data_array["duration"]);
        }
        if (isset($data_array["price"])) {
            $this->setPrice($data_array["price"]);
        }
        if (isset($data_array["product_id"])) {
            $this->setProductId($data_array["product_id"]);
        }
        return $this;
    }
}
