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

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

class TransportPlugin extends \Laminas\Mail\Transport\Smtp
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
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
     * @var mixed|string
     */
    protected $sender_email = "";

    /**
     * @var \Lof\SmtpEmail\Model\Store
     */
    protected $storeModel;

    /**
     * construct
     *
     * @param \Lof\SmtpEmail\Model\Store $storeModel
     * @param \Lof\SmtpEmail\Helper\Data $dataHelper
     * @param \Lof\SmtpEmail\Logger\Logger $logger
     * @param \Lof\SmtpEmail\Model\Emaillog $emaillog
     * @param \Lof\SmtpEmail\Model\Emaildebug $emaildebug
     */
    public function __construct(
        \Lof\SmtpEmail\Model\Store $storeModel,
        \Lof\SmtpEmail\Helper\Data $dataHelper,
        \Lof\SmtpEmail\Logger\Logger $logger,
        \Lof\SmtpEmail\Model\Emaillog $emaillog,
        \Lof\SmtpEmail\Model\Emaildebug $emaildebug
    ) {
        $this->_helper = $dataHelper;
        $this->_emaillog = $emaillog;
        $this->_emaildebug = $emaildebug;
        $this->_logger = $logger;
        $this->storeModel = $storeModel;

        $options = new SmtpOptions();
        $this->setOptions($options);
    }

    /**
     * Set store model
     *
     * @param \Lof\SmtpEmail\Model\Store $storeModel
     * @return $this
     */
    public function setStoreModel(\Lof\SmtpEmail\Model\Store $storeModel)
    {
        $this->storeModel = $storeModel;
        return $this;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param \Closure $proceed
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Mail_Exception
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        \Closure $proceed
    ) {
        if ($this->_helper->getConfig('general_settings/enable_smtp_email') == 1) {
            if (method_exists($subject, 'getStoreId')) {
                $this->storeModel->setStoreId($subject->getStoreId());
            }
            $message = $subject->getMessage();
            $this->sendSmtpMessage($message);
        } else {
            $proceed();
        }
    }

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Mail_Exception
     */
    public function sendSmtpMessage(\Magento\Framework\Mail\MessageInterface $message)
    {
        $dataHelper = $this->_helper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());
        if ($message instanceof \Magento\Framework\Mail\Message) {
            $message = Message::fromString($message->getRawMessage());
            $message->getHeaders()->setEncoding('utf-8');
            $body = $message->getBody();
            if (is_object($body) && $body->isMultiPart()) {
                $message->setBody($body->getPartContent("0"));
            }
        }

        //Set reply-to path
        $setReturnPath = $dataHelper->getConfigSetReturnPath();

        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $message->getFrom();
                break;
            case 2:
                $returnPathEmail = $dataHelper->getConfigReturnPathEmail();
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null && $dataHelper->getConfigSetReturnPath()) {
            foreach ($returnPathEmail as $address) {
                $message->setSender($address);
            }
        }

        if ($message->getReplyTo() === null && $dataHelper->getConfigSetReplyTo()) {
            foreach ($returnPathEmail as $address) {
                $message->setReplyTo($address);
            }
        }

        if ($returnPathEmail !== null && $dataHelper->getConfigSetFrom()) {
            foreach ($returnPathEmail as $address) {
                $message->setFrom($address);
            }
        }

        if (!$message->getFrom()->count()) {
            $result = $this->storeModel->getFrom();

            $message->setFrom($result['email'], $result['name']);
        }


        if((int)$dataHelper->getConfig('trans_email/same_smtp') == 0) {
            $from = $message->getFrom()->current()->getEmail();

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

        //set config
        $options   = new SmtpOptions([
            'name' => $dataHelper->getProviderName(),
            'host' => $dataHelper->getConfigSmtpHost(),
            'port' => $dataHelper->getConfigPort(),
        ]);

        $connectionConfig = [];

        $auth = strtolower($dataHelper->getConfigAuth());
        if ($auth != 'none') {
            $options->setConnectionClass($auth);
            $connectionConfig = [
                'username' => $username,
                'password' => $password
            ];
        }

        $ssl = $dataHelper->getConfigSsl();
        if ($ssl != '' && $ssl != 'none') {
            $connectionConfig['ssl'] = $ssl;
        }

        if (!empty($connectionConfig)) {
            $options->setConnectionConfig($connectionConfig);
        }

        $this->_logger->debug($this->_emaillog->isBlacklist($message));

        $this->_emaildebug->messageDebug(__('Ready to send email'));
        if($this->_helper->getConfig('general_settings/enable_smtp_email') == 1) {
            try {
                if($this->_helper->getConfig('general_settings/enable_email_log') == 1) {

                    $emaillogId = $this->_emaillog->messageLog($message, $this->sender_email);

                    if($this->_emaillog->isBlacklist($message)) {
                        $this->_emaildebug->messageDebug(__('Email sent blacklist'));
                        $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_BLACKLIST);
                    } elseif($this->_emaillog->isBlockip()){
                        $this->_emaildebug->messageDebug(__('Your email block ip'));
                        $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_BLOCKIP);
                    } else {
                        if($this->_emaillog->checkSpam($message)) {
                            $this->_emaildebug->messageDebug(__('Your email is spam'));
                            $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_SPAM);
                        } else {
                            $this->_transport = new SmtpTransport();
                            $this->_transport->setOptions($options);
                            $this->_transport->send($message);
                            $this->_emaildebug->messageDebug(__('Email sent successfully'));
                            $this->_emaillog->updateStatus($emaillogId,Emaillog::STATUS_SENT);
                             return $this->_transport;
                        }
                    }
                } else {
                    $this->_transport = new SmtpTransport();
                    $this->_transport->setOptions($options);
                    $this->_transport->send($message);
                    $this->_emaildebug->messageDebug(__('Email sent successfully'));
                    return $this->_transport;
                }
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
            }
        }
    }

    /**
     * @param string $host
     * @param array $config
     * @return void
     */
    public function initialize($host = '127.0.0.1', array $config = [])
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }
        $this->_host = $host;
        $this->_config = $config;
    }
}
