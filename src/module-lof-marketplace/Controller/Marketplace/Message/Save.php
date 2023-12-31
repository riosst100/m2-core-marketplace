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

namespace Lof\MarketPlace\Controller\Marketplace\Message;

use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Helper\Url
     */
    protected $url;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Lof\MarketPlace\Model\MessageAdminFactory
     */
    protected $messageAdminFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Helper\Url $url
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\MessageAdminFactory $messageAdminFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Url $url,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\MessageAdminFactory $messageAdminFactory
    ) {
        parent::__construct($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->url = $url;
        $this->helper = $helper;
        $this->sender = $sender;
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        $this->messageAdminFactory = $messageAdminFactory;
    }

    public function getFrontendUrl($route, $params)
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($route = '', $params = [])
    {
        $this->getResponse()->setRedirect($this->getFrontendUrl($route, $params));
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $sellerModel = $customerId ? $this->sellerFactory->create()->load($customerId, 'customer_id') : null;
        $sellerId = $sellerModel ? $sellerModel->getId() : 0;
        $status = $sellerModel ? $sellerModel->getStatus() : 0;

        if ($customerSession->isLoggedIn() && $status == 1) {
            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $data['seller_id'] = $this->helper->getSellerId();
                $message = $this->messageAdminFactory->create();
                try {
                    $data = $this->helper->xss_clean_array($data);
                    $message->setData($data);
                    $message->save();
                    $data['namestore'] = $this->helper->getStoreName();
                    $data['urllogin'] = $this->helper->getBaseStoreUrl().'customer/account/login';
                    $data['content'] = isset($data['description']) ? $data['description'] : '';

                    if ($this->helper->getConfig('email_settings/enable_send_email')) {
                        $this->sender->sellerNewMessage($data);
                    }

                    $this->_eventManager->dispatch(
                        'marketplace_seller_new_message',
                        ['object' => $this, 'data' => $data, 'seller_id' => $sellerId, 'seller' => $sellerModel]
                    );
                    $this->messageManager->addSuccessMessage('send contact success');

                    $this->_redirect('catalog/message/admin');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the seller.'));
                }
            }
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl('lofmarketplace/seller/becomeseller');
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl('lofmarketplace/seller/login');
        }
    }
}
