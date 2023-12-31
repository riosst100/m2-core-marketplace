<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api\Data;

interface SubmitChatInterface
{
    const MESSAGE = 'message';

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return $this
     */
    public function setIp($ip);

    /**
     * Get os
     * @return string|null
     */
    public function getOs();

    /**
     * Set os
     * @param string $os
     * @return $this
     */
    public function setOs($os);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return $this
     */
    public function setCountry($country);

    /**
     * Get current_url
     * @return string|null
     */
    public function getCurrentUrl();

    /**
     * Set current_url
     * @param string $currentUrl
     * @return $this
     */
    public function setCurrentUrl($currentUrl);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

}

