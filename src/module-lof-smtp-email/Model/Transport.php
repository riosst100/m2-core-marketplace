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
 * @package    Lof_Blog
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Model;

use Magento\Framework\Mail\MessageInterface;

abstract class Transport extends \Laminas\Mail\Transport\Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;

    /**
     * @var \Lof\SmtpEmail\Model\Emaillog
     */
    protected $_emaillog;

    /**
     * @var \Lof\SmtpEmail\Model\Emaildebug
     */
    protected $_emaildebug;

    /**
     * @var \Lof\SmtpEmail\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\SmtpEmail\Logger\Logger
     */
    protected $_logger;

    /**
     * @var string
     */
    protected $sender_email = "";

    /**
     * @param MessageInterface $message
     * @param \Lof\SmtpEmail\Helper\Data $dataHelper
     * @param \Lof\SmtpEmail\Logger\Logger $logger
     * @param \Lof\SmtpEmail\Model\Emaillog $emaillog
     * @param \Lof\SmtpEmail\Model\Emaildebug $emaildebug
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Framework\Mail\MessageInterface $message,
        \Lof\SmtpEmail\Helper\Data $dataHelper,
        \Lof\SmtpEmail\Logger\Logger $logger,
        \Lof\SmtpEmail\Model\Emaillog $emaillog,
        \Lof\SmtpEmail\Model\Emaildebug $emaildebug
    )
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $this->_helper = $dataHelper;
        $this->_message = $message;
        $this->_emaillog = $emaillog;
        $this->_emaildebug = $emaildebug;
        $this->_logger = $logger;

        $from = $message->getFrom();
        $smtpHost = $dataHelper->getConfigSmtpHost();

        if($dataHelper->getConfig('trans_email/same_smtp') == 0) {
            if($from == $dataHelper->getConfig('trans_email/general_contact_email')) {
                $username = $dataHelper->getConfig('trans_email/general_contact_email');
                $password = $dataHelper->getConfig('trans_email/general_contact_pass');
            }elseif ($from == $dataHelper->getConfig('trans_email/sales_representative_email')) {
                $username = $dataHelper->getConfig('trans_email/sales_representative_email');
                $password = $dataHelper->getConfig('trans_email/sales_representative_pass');
            }elseif ($from == $dataHelper->getConfig('trans_email/customer_support_email')) {
                $username = $dataHelper->getConfig('trans_email/customer_support_email');
                $password = $dataHelper->getConfig('trans_email/customer_support_pass');
            }elseif ($from == $dataHelper->getConfig('trans_email/custom_email_1_email')) {
                $username = $dataHelper->getConfig('trans_email/custom_email_1_email');
                $password = $dataHelper->getConfig('trans_email/custom_email_1_pass');
            }elseif ($from == $dataHelper->getConfig('trans_email/custom_email_2_email')) {
                $username = $dataHelper->getConfig('trans_email/custom_email_2_email');
                $password = $dataHelper->getConfig('trans_email/custom_email_2_pass');
            }else {
                $username = $dataHelper->getConfigUsername();
                $password = $dataHelper->getConfigPassword();
            }
        } else {
            $username = $dataHelper->getConfigUsername();
            $password = $dataHelper->getConfigPassword();
        }

        $this->sender_email = $username;

        $auth = strtolower($dataHelper->getConfigAuth());

        $smtpConf = array(
            'name' => $dataHelper->getProviderName(),
            'port' => $dataHelper->getConfigPort()
        );

        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $username;
            $smtpConf['password'] = $password;
        }

        $ssl = $dataHelper->getConfigSsl();
        if ($ssl != 'none' && $ssl) {
            $smtpConf['ssl'] = $ssl;
        }

        parent::__construct($smtpHost, $smtpConf);
    }

    /**
     * get sender email
     *
     * @return string
     */
    public function getSenderEmail()
    {
        if (!$this->sender_email) {
            $dataHelper = $this->_helper;
            $from = $this->_message->getFrom();
            if($dataHelper->getConfig('trans_email/same_smtp') == 0) {
                if($from == $dataHelper->getConfig('trans_email/general_contact_email')) {
                    $username = $dataHelper->getConfig('trans_email/general_contact_email');
                }elseif ($from == $dataHelper->getConfig('trans_email/sales_representative_email')) {
                    $username = $dataHelper->getConfig('trans_email/sales_representative_email');
                }elseif ($from == $dataHelper->getConfig('trans_email/customer_support_email')) {
                    $username = $dataHelper->getConfig('trans_email/customer_support_email');
                }elseif ($from == $dataHelper->getConfig('trans_email/custom_email_1_email')) {
                    $username = $dataHelper->getConfig('trans_email/custom_email_1_email');
                }elseif ($from == $dataHelper->getConfig('trans_email/custom_email_2_email')) {
                    $username = $dataHelper->getConfig('trans_email/custom_email_2_email');
                }else {
                    $username = $dataHelper->getConfigUsername();
                }
            } else {
                $username = $dataHelper->getConfigUsername();
            }

            $this->sender_email = $username;
        }
        return $this->sender_email;
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        $this->_logger->debug($this->_emaillog->isBlacklist($this->_message));
        $this->_emaildebug->messageDebug(__('Ready to send email'));
        if($this->_helper->getConfig('general_settings/enable_smtp_email') == 1) {
            try {

                if($this->_helper->getConfig('general_settings/enable_email_log') == 1) {

                    $emaillogId = $this->_emaillog->messageLog($this->_message, $this->getSenderEmail());

                    if($this->_emaillog->isBlacklist($this->_message)) {
                        $this->_emaildebug->messageDebug(__('Email sent blacklist'));
                        $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_BLACKLIST);
                    } elseif($this->_emaillog->isBlockip()){
                        $this->_emaildebug->messageDebug(__('Your email block ip'));
                        $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_BLOCKIP);
                    } else {
                        if($this->_emaillog->checkSpam($this->_message)) {
                            $this->_emaildebug->messageDebug(__('Your email is spam'));
                            $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_SPAM);
                        } else {
                            parent::send($this->_message);
                            $this->_emaildebug->messageDebug(__('Email sent successfully'));
                            $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_SENT);
                        }
                    }

                } else {
                    parent::send($this->_message);
                    $this->_emaildebug->messageDebug(__('Email sent successfully'));
                }
            } catch (\Exception $e) {
                 $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_FAILED);
                 $this->_emaildebug->messageDebug(__('Error sending email: %1', $e->getMessage()));
                throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
            }
       }
    }
}
