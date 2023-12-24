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

use Lof\SmtpEmail\Api\Data\BlockipInterface;
use Magento\Framework\Model\AbstractModel;


class Blockip extends AbstractModel implements BlockipInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\SmtpEmail\Model\ResourceModel\Blockip::class);
    }

    /**
     * @inheritDoc
     */
    public function getBlockipId()
    {
        return $this->getData(self::BLOCKIP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setBlockipId($blockipId)
    {
        return $this->setData(self::BLOCKIP_ID, $blockipId);
    }

    /**
     * @inheritDoc
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * @inheritDoc
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
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
