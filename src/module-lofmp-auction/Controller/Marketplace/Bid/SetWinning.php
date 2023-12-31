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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Controller\Marketplace\Bid;

use Lof\Auction\Helper\ProcessWinner;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Lof\Auction\Model\AmountFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class SetWinning
 * Lofmp\Auction\Controller\Marketplace\Bid
 */
class SetWinning extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Url
     */
    protected $_customerUrl;
    /**
     * @var ProcessWinner
     */
    protected $_processWinnerHelper;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var AmountFactory
     */
    private $amount;
    /**
     * @var AutoAuctionFactory
     */
    private $autoAmount;
    /**
     * @var MixAmountFactory
     */
    private $mixAmount;
    /**
     * @var SellerFactory
     */
    private $sellerFactory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var ProductFactory
     */
    private $auctionFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param ProcessWinner $processWinner
     * @param CustomerRepositoryInterface $customerRepository
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param MixAmountFactory $mixAmountFactory
     * @param SellerFactory $sellerFactory
     * @param ProductFactory $auctionFactory
     */

    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        ProcessWinner $processWinner,
        CustomerRepositoryInterface $customerRepository,
        AmountFactory $amountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        MixAmountFactory $mixAmountFactory,
        SellerFactory $sellerFactory,
        ProductFactory $auctionFactory
    ) {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->_processWinnerHelper = $processWinner;
        $this->customerRepository = $customerRepository;
        $this->amount = $amountFactory;
        $this->autoAmount = $autoAuctionFactory;
        $this->mixAmount = $mixAmountFactory;
        $this->sellerFactory = $sellerFactory;
        $this->auctionFactory = $auctionFactory;
    }

    /**
     * Retrieve customer session object.
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $urlModel = $this->_customerUrl;
        $loginUrl = $urlModel->getLoginUrl();
        if (!$this->session->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Default Product rate
     *
     * @return Redirect
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $id = $this->getRequest()->getParam('id');
            $mix = $this->mixAmount->create()->load($id);
            if($mix->getCustomerId() == $customerId){
                return $resultRedirect->setPath('*/*/');
            }
            if ($this->validate($mix->getAuctionId(), $seller->getData('seller_id'))) {
                return $resultRedirect->setPath('*/*/');
            }
            $bidId = $mix->getBidId();
            if ($mix->getData('is_auto')) {
                $model = $this->autoAmount->create()->load($bidId);
                $auctionType = "auto";
            } else {
                $model = $this->amount->create()->load($bidId);
                $auctionType = "manual";
            }
            if ($model->getEntityId()) {
                //find Winner when auction end time
                $status = $this->_processWinnerHelper->setWinnerManual($model, $auctionType);
                if ($status) {
                    $mix->setWinningStatus(1)->save();
                    $customer = $this->customerRepository->getById($model->getCustomerId());
                    $this->messageManager->addSuccess(
                        __(
                            'You have set %1 to winner this auction id "%2".',
                            $customer->getFirstname().' '.$customer->getLastname(),
                            $model->getAuctionId()
                        )
                    );
                } else {
                    $this->messageManager->addWarning(
                        __(
                            'We can not set auction winning this bid %1 at momment.',
                            $model->getId()
                        )
                    );
                }
            }
            return $resultRedirect->setPath('*/*/');
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_customerUrl->getUrl($route, $params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @param $auctionId
     * @param $sellerId
     * @return bool
     */
    public function validate($auctionId, $sellerId) {
        $auction = $this->auctionFactory->create()->load($auctionId);
        if ($auction->getSellerId() != $sellerId) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to set winning this bid'));
            return true;
        } else {
            return false;
        }
    }
}
