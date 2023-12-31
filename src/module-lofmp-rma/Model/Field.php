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

class Field extends \Magento\Framework\Model\AbstractModel implements \Lofmp\Rma\Api\Data\FieldInterface, IdentityInterface
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->context = $context;
        $this->registry = $registry;
        $this->resourceCollection = $resourceCollection;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Field');
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::KEY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        if ($this->getType() == 'select') {
            $data = $this->getData(self::KEY_VALUES);
            $rows = explode("\n", $data);
            $values = [];
            foreach ($rows as $row) {
                if (trim($row)) {
                    $keyValue = explode(' | ', $row);
                  
                    $values[$keyValue[0]] = $keyValue[1];
                }
            }
            return $values;
        } else {
            return $this->getData(self::KEY_VALUES);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setValues($values)
    {
        return $this->setData(self::KEY_VALUES, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
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
    public function getIsRequiredStaff()
    {
        return $this->getData(self::KEY_IS_REQUIRED_STAFF);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequiredStaff($isRequiredStaff)
    {
        return $this->setData(self::KEY_IS_REQUIRED_STAFF, $isRequiredStaff);
    }

    /**
     * {@inheritdoc}
     */
    public function IsCustomerRequired()
    {
        return $this->getData(self::KEY_IS_REQUIRED_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequiredCustomer($isRequiredCustomer)
    {
        return $this->setData(self::KEY_IS_REQUIRED_CUSTOMER, $isRequiredCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleCustomer()
    {
        return $this->getData(self::KEY_IS_VISIBLE_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleCustomer($isVisibleCustomer)
    {
        return $this->setData(self::KEY_IS_VISIBLE_CUSTOMER, $isVisibleCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsEditableCustomer()
    {
        return $this->getData(self::KEY_IS_EDITABLE_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEditableCustomer($isEditableCustomer)
    {
        return $this->setData(self::KEY_IS_EDITABLE_CUSTOMER, $isEditableCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleCustomerStatus()
    {
        return $this->getData(self::KEY_VISIBLE_CUSTOMER_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibleCustomerStatus($visibleCustomerStatus)
    {
        return $this->setData(self::KEY_VISIBLE_CUSTOMER_STATUS, $visibleCustomerStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsShowInConfirmShipping()
    {
        return $this->getData(self::KEY_IS_SHOW_IN_CONFIRM_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsShowInConfirmShipping($isShowInConfirmShipping)
    {
        return $this->setData(self::KEY_IS_SHOW_IN_CONFIRM_SHIPPING, $isShowInConfirmShipping);
    }

    const CACHE_TAG = 'rma_field';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_field';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_field';

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
    public function afterCommitCallback()
    {
        $this->getResource()->afterCommitCallback($this);

        return parent::afterCommitCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);

        return parent::afterDeleteCommit();
    }
}
