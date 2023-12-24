<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface BlacklistInterface
{

    const EMAIL = 'email';
    const BLACKLIST_ID = 'blacklist_id';
    const CREATED_AT = 'created_at';

    /**
     * Get blacklist_id
     * @return string|null
     */
    public function getBlacklistId();

    /**
     * Set blacklist_id
     * @param string $blacklistId
     * @return \Lof\SmtpEmail\Blacklist\Api\Data\BlacklistInterface
     */
    public function setBlacklistId($blacklistId);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\SmtpEmail\Blacklist\Api\Data\BlacklistInterface
     */
    public function setEmail($email);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\SmtpEmail\Blacklist\Api\Data\BlacklistInterface
     */
    public function setCreatedAt($createdAt);
}

