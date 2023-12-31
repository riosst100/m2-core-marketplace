<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_ChatSystem
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\ChatSystem\Controller\Chat;

use Lof\MarketPlace\Model\Seller;
use Lofmp\ChatSystem\Model\ChatFactory;
use Lofmp\ChatSystem\Helper\Data;
use Lofmp\ChatSystem\Model\Chat;
use Lofmp\ChatSystem\Model\ChatMessage;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Sendmsg extends Action
{
    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var ChatMessage
     */
    protected $_message;

    /**
     * @var Chat
     */
    protected $_chat;

    /**
     * @var ChatFactory
     */
    protected $_chatModelFactory;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var Registry
     */
    private $_coreRegistry;
    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Sendmsg constructor.
     *
     * @param Chat $chat
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param ChatMessage $message
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param TypeListInterface $cacheTypeList
     * @param Session $customerSession
     * @param ChatFactory $chatModelFactory
     * @param RemoteAddress $remoteAddress
     * @param Http $httpRequest
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Chat $chat,
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        ChatMessage $message,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        TypeListInterface $cacheTypeList,
        Session $customerSession,
        ChatFactory $chatModelFactory,
        RemoteAddress $remoteAddress,
        Http $httpRequest,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->_chat = $chat;
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_message = $message;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_customerSession = $customerSession;
        $this->_request = $context->getRequest();
        $this->_chatModelFactory = $chatModelFactory;
        $this->httpRequest = $httpRequest;
        $this->remoteAddress = $remoteAddress;
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->_request->getPostValue();
        $data['is_read'] = 1;
        $data['session_id'] = session_id();
        $data['body_msg'] = $params['body_msg'];
        $data['current_url'] = $params['current_url'];
        $seller_id = $params['seller_id'];

        $objectManager = ObjectManager::getInstance();
        $seller = $objectManager->create(Seller::class)->load($seller_id);
        $customer = $this->httpContext;

        if (!empty($data) && !empty($data['body_msg'])) {
            $isNewChatThread = false;
            if ($customer->getValue('customer_email')) {
                $chat_id = $this->_helper->getChatId($seller_id, $customer->getValue('customer_email'));
                if (!$chat_id) {
                    $this->_helper->setChatId($customer, $seller);
                    $chat_id = $this->_helper->getChatId($seller_id, $customer->getValue('customer_email'));
                    $isNewChatThread = true;
                }
                $data['chat_id'] = $chat_id;
                $data['customer_id'] = (int)$customer->getValue('customer_id');
                $data['customer_email'] = $customer->getValue('customer_email');
                $data['customer_name'] = $customer->getValue('customer_name');
            } else {
                $chat_id = $this->_helper->getGuestChatId($seller_id, $data['session_id']);
                if (!$chat_id) {
                    $this->_helper->setGuestChatId($data['session_id'], $seller);
                    $chat_id = $this->_helper->getGuestChatId($seller_id, $data['session_id']);
                    $isNewChatThread = true;
                }
                $data['chat_id'] = $chat_id;
            }

            if (empty($data['customer_name'])) {
                $data['customer_name'] = __('Guest');
            }
            $data = $this->_helper->xss_clean_array($data);
            $message = $this->_message;
            try {
                $message
                    ->setData($data)
                    ->save();

                $chat = $this->_chatModelFactory->create()->load($data['chat_id']);
                $number_message = $chat->getData('number_message') + 1;

                $enable_auto_assign_user = $this->_helper->getConfig('system/enable_auto_assign_user');
                $admin_user_id = $this->_helper->getConfig('system/admin_user_id');
                if ($enable_auto_assign_user && $admin_user_id) {
                    $data["user_id"] = (int)$admin_user_id;
                } else {
                    $data["user_id"] = 0;
                }

                $this->_eventManager->dispatch(
                    'lof_sellerchatsystem_new_message',
                    ['object' => $this, 'request' => $this->getRequest(), 'isNew' => $isNewChatThread, "data" => $data]
                );

                $chat
                    ->setData('user_id', (int)$data["user_id"])
                    ->setData('is_read', 1)
                    ->setData('answered', 1)
                    ->setData('status', 1)
                    ->setData('number_message', $number_message)
                    ->setData('current_url', $data['current_url'])
                    ->setData('ip', $this->_helper->getIp())
                    ->setData('session_id', $data['session_id'])
                    ->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                return;
            }
        }
    }
}
