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
namespace Lofmp\ChatSystem\Controller\Marketplace\Chat;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Display Hello on screen
 */
class Msglog extends \Magento\Framework\App\Action\Action
{
    protected $_cacheTypeList;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lofmp\ChatSystem\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    protected $_message;

    protected $chat;
    protected $_chat;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketHelper;

    /**
     * @param Context                                             $context
     * @param \Magento\Store\Model\StoreManager                   $storeManager
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Lofmp\ChatSystem\Model\ChatMessage $message
     * @param \Lofmp\ChatSystem\Model\Chat $chat
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lofmp\ChatSystem\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        \Lofmp\ChatSystem\Model\ChatMessage $message,
        \Lofmp\ChatSystem\Model\Chat $chat,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Customer\Model\Session $customerSession
        ) {
        $this->_chat = $chat;
        $this->resultPageFactory    = $resultPageFactory;
        $this->_helper              = $helper;
        $this->marketHelper         = $marketHelper;
        $this->_message             = $message;
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
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_helper->isEnabled()) {
            exit;
        }
        $id = $this->getRequest()->getparam('chat_id');
        $chat = $this->_chat->getCollection()->addFieldToFilter('chat_id',$id)
        ->addFieldToFilter('seller_id',$this->marketHelper->getSellerId());
        $chat_id = $chat->getFirstItem()->getData('chat_id');
        $message = $this->_message->getCollection()->addFieldToFilter('chat_id',$chat_id);

        foreach ($message as $key => $_message) {
            $_message['body_msg'] = $this->_helper->xss_clean($_message['body_msg']);
            $_message['seller_name'] = $this->_helper->xss_clean($_message['seller_name']);
            $_message['created_at'] = strtotime($_message['created_at']);
            if ($_message['seller_id'])
            {
                echo '
                    <div class="msg-user">
                        <p>'.$_message['body_msg'].'</p>
                        <div class="info-msg-user">
                            '.__("You").', '.date('d-m-Y g:i a', $_message['created_at']).'
                        </div>
                    </div>

                ';
            } else if($_message['user_id']){
                echo '
                    <div class="msg-user">
                        <p>'.$_message['body_msg'].'</p>
                        <div class="info-msg-user">
                            '.$_message['user_name'].', '.date('d-m-Y g:i a', $_message['created_at']).'
                        </div>
                    </div>

                ';
            }else{
                echo '
                <div class="msg">
                    <p>'.$_message['body_msg'].'</p>
                    <div class="info-msg">';
                    if($_message['customer_name'] != " ") {
                        echo $_message['customer_name'].', '.date('d-m-Y g:i a', $_message['created_at']);
                    } else {
                        echo __('Guest'.', '.date('d-m-Y g:i a', $_message['created_at']));
                    }
                echo '</div>
                </div>
            ';
            }
        }
        exit;

    }
}
