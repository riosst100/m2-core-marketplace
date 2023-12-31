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

use Magento\Framework\Model\AbstractModel;

class Status extends AbstractModel implements \Lofmp\Rma\Api\Data\StatusInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Status');
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::KEY_SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsShowShipping()
    {
        return $this->getData(self::KEY_IS_SHOW_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsShowShipping($isShowShipping)
    {
        return $this->setData(self::KEY_IS_SHOW_SHIPPING, $isShowShipping);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerMessage()
    {
        return $this->decodeMessage($this->getData(self::KEY_CUSTOMER_MESSAGE));
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerMessage($customerMessage)
    {
        return $this->setData(self::KEY_CUSTOMER_MESSAGE, json_encode($customerMessage));
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminMessage()
    {
        return $this->decodeMessage($this->getData(self::KEY_ADMIN_MESSAGE));
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminMessage($adminMessage)
    {
        return $this->setData(self::KEY_ADMIN_MESSAGE, json_encode($adminMessage));
    }

    /**
     * {@inheritdoc}
     */
    public function getHistoryMessage()
    {
        return $this->decodeMessage($this->getData(self::KEY_HISTORY_MESSAGE));
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoryMessage($historyMessage)
    {
        return $this->setData(self::KEY_HISTORY_MESSAGE, json_encode($historyMessage));
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getData(self::KEY_IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::KEY_CODE, $code);
    }

    /**
     * Compatibility to old versions
     *
     * @param string $message
     * @return string
     */
    public function decodeMessage($message)
    {
        if ($decoded = json_decode($message, true)) {
            $message = $decoded;
        }
        if (is_array($message)) {
            return $message;
        } else {
            return [$message];
        }
    }
}
