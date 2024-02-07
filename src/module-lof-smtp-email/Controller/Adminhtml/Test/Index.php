<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
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

namespace Lof\SmtpEmail\Controller\Adminhtml\Test;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\SmtpEmail\Helper\Data;
use Laminas\Mail\Transport\Smtp;
use Laminas\Validator\EmailAddress;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var \Lof\SmtpEmail\Model\Emaildebug
     */
    protected $_emaildebug;

     /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;

    /**
     * @var \Lof\SmtpEmail\Model\Config\Source\Providers
     */
    protected $_providers;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Lof\SmtpEmail\Helper\Data $dataHelper
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Lof\SmtpEmail\Model\Emaildebug $emaildebug
     * @param \Lof\SmtpEmail\Model\Config\Source\Providers $providers
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Lof\SmtpEmail\Helper\Data $dataHelper,
        \Magento\Framework\Mail\MessageInterface $message,
        \Lof\SmtpEmail\Model\Emaildebug $emaildebug,
        \Lof\SmtpEmail\Model\Config\Source\Providers $providers
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_emaildebug = $emaildebug;
        $this->_message = $message;
        $this->_providers = $providers;
        parent::__construct($context);

    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        $request = $this->getRequest();
        $store_id = $request->getParam('store', null);

        $name = 'Landofcoder SMTP Pro Test Email';
        $username = $request->getPost('username');
        $port = $request->getPost('port');
        $password = $request->getPost('password');

        //if default view
        //see https://github.com/magento/magento2/issues/3019
        if(!$request->getParam('store', false)){
            if(empty($username) || empty($password)){
                $this->getResponse()->setBody(__('Please enter a valid username/password'));
                return;
            }
        }

        //if password mask (6 stars)
        $password = ($password == '******') ? $this->_dataHelper->getConfigPassword($store_id) : $password;

        $to = $request->getPost('email') ? $request->getPost('email') : $username;

        //SMTP server configuration
        $smtpHost = $request->getPost('smtphost');
        $auth = strtolower($request->getPost('auth'));
        $providerId = (int)$request->getPost('provider');
        $providerName = $this->_providers->getProviderName($providerId);

        $options = [
            'name' => $providerName,
            'host' => $smtpHost,
            'port' => $port,
        ];

        if ($auth != 'none') {
            $options['connection_class'] = $auth;
            $options['connection_config'] = [
                'username' => $username,
                'password' => $password
            ];
        }

        $ssl = $request->getPost('ssl');
        if ($ssl != 'none' && $ssl) {
            $options['connection_config']['ssl'] = $ssl;
        }

        $smtpOptions = new \Laminas\Mail\Transport\SmtpOptions($options);
        $transport = new \Laminas\Mail\Transport\Smtp($smtpOptions);

        $from = trim($request->getPost('email'));

        // Validate email
        $validator = new EmailAddress();
        $from = $validator->isValid($from) ? $from : $username;

        // Create mail message
        $message = new \Laminas\Mail\Message();
        $message->setFrom($from, $name);
        $message->addTo($to, $to);
        $message->setSubject('Hello from Landofcoder SMTP (1 of 2)');
        $message->setBody('Thank you for choosing Landofcoder\'s extension.');

        $result = __('Sent... Please check your email') . ' ' . $to;

        // Send email
        try {
            $transport->send($message);
        } catch (\Exception $e) {
            $result = __($e->getMessage());
        }

        $this->getResponse()->setBody($this->makeClickableLinks($result));
    }

    /**
     * Make link clickable
     * @param string $s
     * @return string
     */
    public function makeClickableLinks($s)
    {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
    }

    /**
     * @param string|null $key
     * @return array|mixed|string
     */
    public function getConfig($key = null)
    {
        $request = $this->getRequest();
        if ($key === null) {
            return $request->getPost();
        } else {
            return $request->getPost($key);
        }
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_SmtpEmail');
    }
}
