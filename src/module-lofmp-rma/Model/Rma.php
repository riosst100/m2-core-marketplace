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



namespace Lofmp\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Rma extends \Magento\Framework\Model\AbstractModel implements \Lofmp\Rma\Api\Data\RmaInterface, IdentityInterface
{
    const CACHE_TAG = 'rma_rma';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_rma';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_rma';

    public function __construct(
        \Lofmp\Rma\Helper\Data $datahelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->orderFactory = $orderFactory;
        $this->datahelper = $datahelper;
    }


    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        parent::afterSave();
        if (!$this->getIncrementId()) {
            $this->setIncrementId($this->datahelper->generateIncrementId($this));
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Rma');
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaId()
    {
        return $this->getData(self::KEY_RMA_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRmaId($rmaId)
    {
        return $this->setData(self::KEY_RMA_ID, $rmaId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementId()
    {
        return $this->getData(self::KEY_INCREMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::KEY_INCREMENT_ID, $incrementId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::KEY_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::KEY_CUSTOMER_ID, $customerId);
    }

     /**
      * {@inheritdoc}
      */
    public function getSellerId()
    {
        return $this->getData(self::KEY_SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::KEY_SELLER_ID, $sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusId()
    {
        return $this->getData(self::KEY_STATUS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::KEY_STATUS_ID, $statusId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        if ($this->getData(self::KEY_STORE_ID)) {
            return $this->getData(self::KEY_STORE_ID);
        }

        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::KEY_STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingCode()
    {
        return $this->getData(self::KEY_TRACKING_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingCode($trackingCode)
    {
        return $this->setData(self::KEY_TRACKING_CODE, $trackingCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsResolved()
    {
        return $this->getData(self::KEY_IS_RESOLVED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsResolved($isResolved)
    {
        return $this->setData(self::KEY_IS_RESOLVED, $isResolved);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($date)
    {
        return $this->setData(self::KEY_CREATED_AT, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::KEY_UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($date)
    {
        return $this->setData(self::KEY_UPDATED_AT, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsGift()
    {
        return $this->getData(self::KEY_IS_GIFT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsGift($isGift)
    {
        return $this->setData(self::KEY_IS_GIFT, $isGift);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAdminRead()
    {
        return $this->getData(self::KEY_IS_ADMIN_READ);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAdminRead($isAdminRead)
    {
        return $this->setData(self::KEY_IS_ADMIN_READ, $isAdminRead);
    }


    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getData(self::KEY_USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($userId)
    {
        return $this->setData(self::KEY_USER_ID, $userId);
    }


    /**
     * {@inheritdoc}
     */
    public function getLastReplyName()
    {
        return $this->getData(self::KEY_LAST_REPLY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastReplyName($lastReplyName)
    {
        return $this->setData(self::KEY_LAST_REPLY_NAME, $lastReplyName);
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnAddress()
    {
        return $this->getData(self::KEY_RETURN_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnAddress($address)
    {
        return $this->setData(self::KEY_RETURN_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnAddressHtml()
    {
        return $this->getData(self::KEY_RETURN_ADDRESS_HTML);
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnAddressHtml($address_html)
    {
        return $this->setData(self::KEY_RETURN_ADDRESS_HTML, $address_html);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return self::MESSAGE_CODE.$this->getIncrementId();
    }
}
