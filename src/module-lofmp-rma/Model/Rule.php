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

class Rule extends \Magento\Rule\Model\AbstractModel implements \Lofmp\Rma\Api\Data\RuleInterface, IdentityInterface
{
    public function __construct(
        \Lofmp\Rma\Model\Rule\Condition\CombineFactory $ruleConditionCombineFactory,
        \Lofmp\Rma\Model\Rule\CombineFactory $ruleActionCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ruleConditionCombineFactory = $ruleConditionCombineFactory;
        $this->ruleActionCollectionFactory = $ruleActionCollectionFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
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
    public function getEvent()
    {
        return $this->getData(self::KEY_EVENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEvent($event)
    {
        return $this->setData(self::KEY_EVENT, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailBody()
    {
        return $this->getData(self::KEY_EMAIL_BODY);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailBody($emailBody)
    {
        return $this->setData(self::KEY_EMAIL_BODY, $emailBody);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSubject()
    {
        return $this->getData(self::KEY_EMAIL_SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSubject($emailSubject)
    {
        return $this->setData(self::KEY_EMAIL_SUBJECT);
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
    public function getConditionsSerialized()
    {
        return $this->getData(self::KEY_CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::KEY_CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSendOwner()
    {
        return $this->getData(self::KEY_IS_SEND_OWNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSendOwner($isSendOwner)
    {
        return $this->setData(self::KEY_IS_SEND_OWNER, $isSendOwner);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSendDepartment()
    {
        return $this->getData(self::KEY_IS_SEND_DEPARTMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSendDepartment($isSendDepartment)
    {
        return $this->setData(self::KEY_IS_SEND_DEPARTMENT, $isSendDepartment);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSendUser()
    {
        return $this->getData(self::KEY_IS_SEND_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSendUser($isSendUser)
    {
        return $this->setData(self::KEY_IS_SEND_USER, $isSendUser);
    }

    /**
     * {@inheritdoc}
     */
    public function getOtherEmail()
    {
        return $this->getData(self::KEY_OTHER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setOtherEmail($otherEmail)
    {
        return $this->setData(self::KEY_OTHER_EMAIL);
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
    public function getIsStopProcessing()
    {
        return $this->getData(self::KEY_IS_STOP_PROCESSING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsStopProcessing($isStopProcessing)
    {
        return $this->setData(self::KEY_IS_STOP_PROCESSING, $isStopProcessing);
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
    public function getIsSendAttachment()
    {
        return $this->getData(self::KEY_IS_SEND_ATTACHMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSendAttachment($isSendAttachment)
    {
        return $this->setData(self::KEY_IS_SEND_ATTACHMENT, $isSendAttachment);
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) @fixme
     */
    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }


    const CACHE_TAG = 'rma_rule';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_rule';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_rule';

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
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Rule');
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsInstance()
    {
        return $this->ruleConditionCombineFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsInstance()
    {
        return $this->ruleActionCollectionFactory->create();
    }
}
