<?php

namespace CoreMarketplace\MarketPlace\Controller\Seller;

use Lof\MarketPlace\Helper\Data;

class Becomeseller extends \Lof\MarketPlace\Controller\Seller\Becomeseller
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Becomeseller constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $helperData
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        parent::__construct(
            $context,
            $customerSession,
            $sellerFactory,
            $resultPageFactory,
            $helperData
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();
        $becomeSellerEnable = $this->helperData->getConfig("general_settings/allow_customer_become_seller");

        if ($customerSession->isLoggedIn() && $status != 1 && $becomeSellerEnable) {
            $this->_redirect('marketplace/sellerverification/index');
        } elseif ($customerSession->isLoggedIn() && $status == 1) {
            $this->_redirect('marketplace/catalog/seller');
        } else {
            $this->_redirect('lofmarketplace/seller/login');
        }
    }
}
