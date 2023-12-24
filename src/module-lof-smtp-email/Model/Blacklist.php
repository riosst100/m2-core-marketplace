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

use Lof\SmtpEmail\Api\Data\BlacklistInterface;
use Magento\Framework\Model\AbstractModel;


class Blacklist extends AbstractModel implements BlacklistInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\SmtpEmail\Model\ResourceModel\Blacklist::class);
    }

    /**
     * @inheritDoc
     */
    public function getBlacklistId()
    {
        return $this->getData(self::BLACKLIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setBlacklistId($blacklistId)
    {
        return $this->setData(self::BLACKLIST_ID, $blacklistId);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
