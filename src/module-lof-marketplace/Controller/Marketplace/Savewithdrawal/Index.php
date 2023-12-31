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

namespace Lof\MarketPlace\Controller\Marketplace\Savewithdrawal;

use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Helper\Data;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends \Magento\Customer\Controller\AbstractAccount
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
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Lof\MarketPlace\Model\PaymentFactory
     */
    protected $_paymentFactory;

    /**
     * @var Data
     */
    protected $_sellerHelperData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\PaymentFactory $paymentFactory
     * @param Data $sellerHelperData
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\PaymentFactory $paymentFactory,
        Data $sellerHelperData
    ) {
        parent::__construct($context);

        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->_paymentFactory = $paymentFactory;
        $this->_sellerHelperData = $sellerHelperData;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface|void
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $sellerId = $seller ? $seller->getId() : 0;
        $status = $seller ? $seller->getStatus() : 0;

        if ($customerSession->isLoggedIn() && $status == 1) {
            if (!$this->_sellerHelperData->getConfig('seller_settings/allow_withdrawl')) {
                $this->_redirect('catalog/dashboard');
            } else {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $data = $this->getRequest()->getPostValue();
                $paymentId = isset($data["payment_id"]) ? (int)$data["payment_id"] : 0;
                $payment = $this->_paymentFactory->create()->load($paymentId);
                if ($data && $sellerId && $payment && $payment->getId()) {

                    if (($data['min_amount'] <= $data['amount'] && $data['amount'] <= $data['max_amount'])
                        && ($data['amount'] <= $data['balance'])
                    ) {
                        $data['status'] = 0;
                        $data['comment'] = strip_tags($data['comment']);
                        $data['comment'] = $data['comment'] ? addslashes($data['comment']) : "";
                        $data["fee_percent"] = (float)$payment->getFeePercent();
                        $data["seller_id"] = $sellerId;
                        if ($data['fee_by'] == 'all') {
                            $data['fee'] = (float)$data['fee'] + (float)$data['amount'] * (float)$data['fee_percent'] / 100;
                        } elseif ($data['fee_by'] == 'by_fixed') {
                            $data["fee"] = (float)$payment->getFee();
                        } else {
                            $data['fee'] = (float)$data['amount'] * (float)$data['fee_percent'] / 100;
                        }
                        $data['net_amount'] = (float)$data['amount'] - (float)$data['fee'];
                        $withdrawalModel = $objectManager->get(\Lof\MarketPlace\Model\Withdrawal::class);

                        $this->_eventManager->dispatch(
                            'marketplace_seller_prepare_save_withdrawl',
                            ['account_controller' => $this, 'seller_id' => $sellerId, 'request' => $this->getRequest()]
                        );

                        try {
                            $withdrawalModel->setData($data);
                            $withdrawalModel->save();

                            $this->_eventManager->dispatch(
                                'marketplace_seller_complete_save_withdrawl',
                                ['account_controller' => $this, 'seller_id' => $sellerId, 'withdrawal' => $withdrawalModel]
                            );
                            $this->messageManager->addSuccessMessage(__('Your withdrawal request has been created.'));
                            $this->_redirect('catalog/withdrawals');
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                            $this->_redirect('catalog/withdrawals/payment', ['id' => $paymentId]);
                        } catch (\RuntimeException $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                            $this->_redirect('catalog/withdrawals/payment', ['id' => $paymentId]);
                        } catch (\Exception $e) {
                            $this->messageManager->addExceptionMessage(
                                $e,
                                __('Something went wrong while saving the seller.')
                            );
                        }
                    } else {
                        $this->messageManager->addErrorMessage('Do not withdraw too much money in balance');
                        $this->_redirect('catalog/withdrawals/payment', ['id' => $paymentId]);
                    }
                }
            }
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
