<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Customer;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Deletemsg extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\Sender
     */

    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $messageFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetailFactory
     */
    protected $messageDetailFactory;

    /**
     * Savemsg constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Model\MessageFactory $messageFactory
     * @param SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param CustomerFactory $customerFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\MessageDetailFactory $messageDetailFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\MessageFactory $messageFactory,
        SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        CustomerFactory $customerFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\MessageDetailFactory $messageDetailFactory
    ) {
        $this->helper = $helper;
        $this->messageFactory = $messageFactory;
        $this->sellerFactory = $sellerFactory;
        $this->sender = $sender;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerFactory = $customerFactory;
        $this->messageDetailFactory = $messageDetailFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();

        if ($customerSession->isLoggedIn()) {
            $message = $this->messageFactory->create();
            $id = $this->getRequest()->getParam('message_id');
            if ($id) {
                $message->load($id);
            }
            if ($message->getId() && ($message->getSenderId() === $customerId)) {
                $message->delete();
                $messageDetailModel = $this->messageDetailFactory->create();
                try {
                    $collection = $messageDetailModel->getCollection()->addFieldToFilter('message_id', $id);
                    foreach ($collection as $_item) {
                        $_item->delete();
                    }
                    $this->messageManager->addSuccessMessage(__('Deleted messages with ID %1 sucessfully!', $id));
                    $this->_redirect('lofmarketplace/customer/message');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while deleting the message.')
                    );
                }
            }
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a account to access'));
            $this->_redirect('account/customer/login');
        }
    }
}
