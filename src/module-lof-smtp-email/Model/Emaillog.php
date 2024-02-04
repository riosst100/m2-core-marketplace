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

use Lof\SmtpEmail\Api\Data\EmaillogInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Emaillog extends AbstractModel implements EmaillogInterface
{
    /**
     * @var string
     */
    const STATUS_PENDING = 'Pending';
    /**
     * @var string
     */
    const STATUS_SENT = 'Sent';
    /**
     * @var string
     */
    const STATUS_FAILED = 'Failed';
    /**
     * @var string
     */
    const STATUS_BLACKLIST = 'Backlist';
    /**
     * @var string
     */
    const STATUS_BLOCKIP = 'Blockip';
    /**
     * @var string
     */
    const STATUS_SPAM = 'Spam';

    /**
     * @var \Lof\SmtpEmail\Model\Blacklist
     */
    protected $blacklist;

    /**
     * @var \Lof\SmtpEmail\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Lof\SmtpEmail\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\SmtpEmail\Model\Blockip
     */
    protected $blockip;

    /**
     * @var \Lof\SmtpEmail\Model\Spam
     */
    protected $spam;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Lof\SmtpEmail\Logger\Logger $logger,
        \Lof\SmtpEmail\Model\Blacklist $blacklist,
        \Lof\SmtpEmail\Model\Blockip $blockip,
        \Lof\SmtpEmail\Helper\Data $helper,
        \Lof\SmtpEmail\Model\Spam $spam,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ObjectManagerInterface $objectManager,
        DateTime $coreDate
    ) {
        $this->spam = $spam;
        $this->blockip = $blockip;
        $this->_logger = $logger;
        $this->objectManager = $objectManager;
        $this->coreDate = $coreDate;
        $this->blacklist = $blacklist;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        parent::__construct($context,$registry);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\SmtpEmail\Model\ResourceModel\Emaillog::class);
    }

    /**
     * @inheritDoc
     */
    public function getEmaillogId()
    {
        return $this->getData(self::EMAILLOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEmaillogId($emaillogId)
    {
        return $this->setData(self::EMAILLOG_ID, $emaillogId);
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->getData(self::BODY);
    }

    /**
     * @inheritDoc
     */
    public function setBody($body)
    {
        return $this->setData(self::BODY, $body);
    }

    /**
     * @inheritDoc
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * @inheritDoc
     */
    public function getSenderEmail()
    {
        return $this->getData(self::SENDER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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

    /**
     * @return mixed
     */
    public static function getStatusEmail()
    {
        return [
            self::STATUS_PENDING      => __('Pending'),
            self::STATUS_SENT       => __('Sent'),
            self::STATUS_FAILED        => __('Failed'),
            self::STATUS_BLACKLIST        => __('Blacklist'),
            self::STATUS_BLOCKIP      => __('Block'),
            self::STATUS_SPAM        => __('Spam')
        ];
    }

    /**
     * check spam
     *
     * @param mixed $message
     * @return bool
     */
    public function checkSpam($message)
    {
        $replyTo = $message->getReplyTo()->current();
        $replyToEmail = $replyTo ? $replyTo->getEmail() : "";
        $recipient = $message->getTo()->current()->getEmail();
        $spamCollection = $this->spam->getCollection()->addFieldToFilter('is_active', 1);
        $flag = false;
        foreach ($spamCollection as $key => $spam) {
            switch ($spam->getData('scope')) {
                case 'email':
                    $subject = $recipient. " ".$replyToEmail;
                    break;
                case 'subject':
                    $subject = $message->getSubject();
                    break;
                case 'body':
                default:
                    $subject = $message->getBody();
                    break;
            }

            $matches = [];
            $subject = strip_tags($subject);
            try {
                if(strripos($spam->getPattern(),'/') > 0) {
                    preg_match($spam->getPattern(), $subject, $matches);
                } else {
                    preg_match('/'.$spam->getPattern().'/', $subject, $matches);
                }

                if (count($matches) > 0) {
                    $flag = true;
                    break;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError('Warning: preg_match():Delimiter must not be alphanumeric or backslash. Please contact with admin check pattern text spam');
            }
        }
        return $flag;
    }

    /**
     * message log
     *
     * @param mixed $message
     * @param string $sender_email
     * @return int|mixed
     */
    public function messageLog($message, $sender_email)
    {
        $recipient = $message->getTo()->current()->getEmail();
        if (!$sender_email && $message->getFrom()->current()) {
            $sender_email = $message->getFrom()->current()->getEmail();
        }
        $body = (quoted_printable_decode($message->getBodyText()));
        $this->setData([
            'created_at'        => date('Y-m-d H:i:s'),
            'subject'           => $message->getSubject(),
            'body'              => $body,
            'recipient_email'   => $recipient,
            'status'            => self::STATUS_PENDING,
            'sender_email'      => $sender_email
        ]);
        $this->save();
        return $this->getEmaillogId();
    }

    /**
     * Update status
     *
     * @param int|string $emaillogId
     * @param int|string $status
     * @return void
     */
    public function updateStatus($emaillogId, $status)
    {
        $this->load($emaillogId);
        if ($this->getId()) {
            $this->setStatus($status)->save();
        }
    }

    /**
     * is black list
     *
     * @param mixed $message
     * @return bool
     */
    public function isBlacklist($message)
    {
        $replyTo = $message->getReplyTo()->current();
        $replyToEmail = $replyTo ? $replyTo->getEmail() : "";
        $recipient = $message->getTo()->current()->getEmail();
        $flag = false;
        foreach ($this->blacklist->getCollection() as $key => $_blacklist) {
            if($_blacklist->getEmail() == $recipient || ($replyToEmail && $_blacklist->getEmail() == $replyToEmail)) {
                $flag =  true;
                break;
            }
        }
        return $flag;
    }

    /**
     * is blocked ip address
     *
     * @return bool
     */
    public function isBlockip()
    {
        $flag = false;
         foreach ($this->blockip->getCollection() as $key => $_blockip) {
            if($_blockip->getIp() == $this->helper->getIp()) {
                $flag =  true;
                break;
            }
        }
        return $flag;
    }

    /**
     * clear log
     *
     * @return void
     */
    public function clearLog()
    {
        $keep_email = $this->helper->getConfig('clear/log');
        $time = time() - $keep_email*24*60*60;
        $time = date('Y-m-d H:i:s',$time);
        if($keep_email > 0) {
            $collection=$this->getCollection()->addFieldToFilter('created_at',['lt' => $time]);
            foreach ($collection as $key => $_collection) {
                $_collection->delete();
            }
        }
    }
}
