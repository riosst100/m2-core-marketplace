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

use Lofmp\ChatSystem\Helper\Data;
use Lofmp\ChatSystem\Model\Chat;
use Lofmp\ChatSystem\Model\ChatMessage;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Display Hello on screen
 */
class Msglog extends Action
{
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
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param ChatMessage $message
     * @param Chat $chat
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param TypeListInterface $cacheTypeList
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        ChatMessage $message,
        Chat $chat,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        TypeListInterface $cacheTypeList,
        Session $customerSession
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_helper              = $helper;
        $this->_message             = $message;
        $this->_chat                = $chat;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry        = $registry;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_customerSession     = $customerSession;
        $this->_request             = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $seller_id = $this->_request->getParam("seller_id");
        $session_id = session_id();

        if ($this->_customerSession->getCustomer()->getEmail()) {
            $chat = $this->_chat->getCollection()->addFieldToFilter('customer_email', $this->_customerSession->getCustomer()->getEmail())
            ->addFieldToFilter('seller_id', $seller_id);
            $chat_id = $chat->getFirstItem()->getData('chat_id');
            $message = $this->_message->getCollection()->addFieldToFilter('chat_id', $chat_id);
        } else {
            $chat = $this->_chat->getCollection()->addFieldToFilter('session_id', $session_id)
            ->addFieldToFilter('seller_id', $seller_id);
            $chat_id = $chat->getFirstItem()->getData('chat_id');
            $message = $this->_message->getCollection()->addFieldToFilter('chat_id', $chat_id);
        }

        $count = count($message);
        $int=0;
        foreach ($message as $key => $_message) {
            $int++;
            $_message['body_msg'] = $this->_helper->xss_clean($_message['body_msg']);
            $_message['seller_name'] = $this->_helper->xss_clean($_message['seller_name']);
            $_message['created_at'] = strtotime($_message['created_at']);

            if ((!$_message['seller_id']) && (!$_message['user_id'])) {
                print '<div class="msg-user">
                        <p>'.$_message['body_msg'].'</p>
                        <div class="info-msg-user">
                            '.__("You").', '.date('d-m-Y g:i a', $_message['created_at']).'
                        </div>
                    </div> ';
            } else {
                print '<div class="msg">
                    <p>'.$_message['body_msg'].'</p>
                    <div class="info-msg">
                        '.date('d-m-Y g:i a', $_message['created_at']).'
                    </div>
                </div>';
                if ($count == $int) {
                    echo "
                    <script>require(['jquery'],function($) { $('.chat-message-counter').css('display','inline'); });</script>
                    ";
                }
            }
        }
        exit;
    }
}
