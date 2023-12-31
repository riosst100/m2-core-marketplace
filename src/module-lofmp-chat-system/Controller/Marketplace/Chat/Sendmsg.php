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

use Lof\MarketPlace\Model\SalesFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\ChatSystem\Helper\Data;
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
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Display Hello on screen
 */
class Sendmsg extends Action
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
    private $_message;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $_customerSession;

    /**
     *
     * @var SalesFactory
     */
    protected $sellerFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param ChatMessage $message
     * @param ForwardFactory $resultForwardFactory
     * @param TypeListInterface $cacheTypeList
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory,
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        ChatMessage $message,
        SellerFactory $sellerFactory,
        ForwardFactory $resultForwardFactory,
        TypeListInterface $cacheTypeList,
        Session $customerSession
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_helper              = $helper;
        $this->_message             = $message;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->sellerFactory        = $sellerFactory;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_customerSession     = $customerSession;
        $this->_request             = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return Page
     */
    public function execute()
    {
        if (!$this->_helper->isEnabled()) {
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
        $customer = $this->_customerSession;
        $seller = $this->sellerFactory->create()
            ->load($customer->getId(), 'customer_id');

        $responseData = $this->_request->getPostValue();
        if (!empty($responseData['body_msg'])) {
            $chat = $this->_objectManager
                ->create('Lofmp\ChatSystem\Model\Chat')
                ->load($responseData['chat_id']);
            if ($chat->getData('seller_id') == $seller->getId()) {
                $data['chat_id'] = $responseData['chat_id'];
                $data['seller_id'] = $seller->getId();
                $data['customer_email'] = $chat->getData('customer_email');
                $data['customer_name'] = $chat->getData('customer_name');
                $data['body_msg'] = $responseData['body_msg'];
                $message = $this->_message;
                $data = $this->_helper->xss_clean_array($data);
                try {
                    $message->setData($data)->save();
                    $number_message = $chat->getData('number_message') + 1;
                    $chat
                        ->setData('is_read', 3)
                        ->setData('number_message', $number_message)
                        ->setData('is_read', 1)
                        ->setData('answered', 1)
                        ->setData('status', 1)
                        ->setData('ip', $this->_helper->getIp())
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
}
