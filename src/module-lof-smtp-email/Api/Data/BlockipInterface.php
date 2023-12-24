<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface BlockipInterface
{

    const BLOCKIP_ID = 'blockip_id';
    const CREATED_AT = 'created_at';
    const IP = 'ip';

    /**
     * Get blockip_id
     * @return string|null
     */
    public function getBlockipId();

    /**
     * Set blockip_id
     * @param string $blockipId
     * @return \Lof\SmtpEmail\Blockip\Api\Data\BlockipInterface
     */
    public function setBlockipId($blockipId);

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return \Lof\SmtpEmail\Blockip\Api\Data\BlockipInterface
     */
    public function setIp($ip);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\SmtpEmail\Blockip\Api\Data\BlockipInterface
     */
    public function setCreatedAt($createdAt);
}

