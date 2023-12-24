<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

declare(strict_types=1);

namespace Lof\SmtpEmail\Model;

use Lof\SmtpEmail\Api\Data\SpamInterface;
use Magento\Framework\Model\AbstractModel;

class Spam extends AbstractModel implements SpamInterface
{
    /**
     * @var int
     */
    const STATUS_ENABLED = 1;

    /**
     * @var int
     */
    const STATUS_DISABLED = 0;
    /**
     * Initialize resource model
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @return void
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context,
    	\Magento\Framework\Registry $registry
    ) {
    	parent::__construct($context,$registry);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\SmtpEmail\Model\ResourceModel\Spam::class);
    }

    /**
     * @inheritDoc
     */
    public function getSpamId()
    {
        return $this->getData(self::SPAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSpamId($spamId)
    {
        return $this->setData(self::SPAM_ID, $spamId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getPattern()
    {
        return $this->getData(self::PATTERN);
    }

    /**
     * @inheritDoc
     */
    public function setPattern($pattern)
    {
        return $this->setData(self::PATTERN, $pattern);
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return $this->getData(self::SCOPE);
    }

    /**
     * @inheritDoc
     */
    public function setScope($scope)
    {
        return $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get available statues
     *
     * @return mixed|array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Get scope
     *
     * @return mixed
     */
    public function getScopes()
    {
        return [
            'email' => __('Email'),
            'subject' => __('Subject'),
            'body' => __('Body')
        ];
    }
}
