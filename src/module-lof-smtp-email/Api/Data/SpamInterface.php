<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface SpamInterface
{

    const SPAM_ID = 'spam_id';
    const NAME = 'name';
    const PATTERN = 'pattern';
    const SCOPE = 'scope';
    const IS_ACTIVE = 'is_active';

    /**
     * Get spam_id
     * @return string|null
     */
    public function getSpamId();

    /**
     * Set spam_id
     * @param string $spamId
     * @return \Lof\SmtpEmail\Spam\Api\Data\SpamInterface
     */
    public function setSpamId($spamId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\SmtpEmail\Spam\Api\Data\SpamInterface
     */
    public function setName($name);

    /**
     * Get pattern
     * @return string|null
     */
    public function getPattern();

    /**
     * Set pattern
     * @param string $pattern
     * @return \Lof\SmtpEmail\Spam\Api\Data\SpamInterface
     */
    public function setPattern($pattern);

    /**
     * Get scope
     * @return string|null
     */
    public function getScope();

    /**
     * Set scope
     * @param string $scope
     * @return \Lof\SmtpEmail\Spam\Api\Data\SpamInterface
     */
    public function setScope($scope);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Lof\SmtpEmail\Spam\Api\Data\SpamInterface
     */
    public function setIsActive($isActive);
}

