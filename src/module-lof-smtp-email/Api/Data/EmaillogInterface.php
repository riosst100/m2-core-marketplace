<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface EmaillogInterface
{

    const RECIPIENT_EMAIL = 'recipient_email';
    const STATUS = 'status';
    const EMAILLOG_ID = 'emaillog_id';
    const CREATED_AT = 'created_at';
    const SUBJECT = 'subject';
    const BODY = 'body';
    const SENDER_EMAIL = 'sender_email';

    /**
     * Get emaillog_id
     * @return string|null
     */
    public function getEmaillogId();

    /**
     * Set emaillog_id
     * @param string $emaillogId
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setEmaillogId($emaillogId);

    /**
     * Get subject
     * @return string|null
     */
    public function getSubject();

    /**
     * Set subject
     * @param string $subject
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setSubject($subject);

    /**
     * Get body
     * @return string|null
     */
    public function getBody();

    /**
     * Set body
     * @param string $body
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setBody($body);

    /**
     * Get recipient_email
     * @return string|null
     */
    public function getRecipientEmail();

    /**
     * Set recipient_email
     * @param string $recipientEmail
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get sender_email
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * Set sender_email
     * @param string $senderEmail
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\SmtpEmail\Emaillog\Api\Data\EmaillogInterface
     */
    public function setCreatedAt($createdAt);
}

