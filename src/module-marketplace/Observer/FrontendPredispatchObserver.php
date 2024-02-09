<?php

namespace CoreMarketplace\MarketPlace\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class FrontendPredispatchObserver implements ObserverInterface
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var AbstractAction|null
     */
    private $action;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \CoreMarketplace\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * PredispatchObserver constructor.
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \CoreMarketplace\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \CoreMarketplace\MarketPlace\Helper\Data $helper
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->session = $customerSession;
        $this->actionFlag = $actionFlag;
        $this->state = $state;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
    }

    

    /**
     * @param Observer $observer
     * @return PredispatchObserver|void
     */
    public function execute(Observer $observer)
    {
        $controllerAction = $observer->getData('controller_action');
        $this->action = $controllerAction;

        $area = $this->state->getAreaCode();
        if ($area === 'frontend') {
            $customerSession = $this->session;
            $customerId = $customerSession->getId();
            $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
            $status = $seller->getStatus();

            $routeName = $this->action->getRequest()->getRouteName();
            $controllerName = $this->action->getRequest()->getControllerName();
            $actionName = $this->action->getRequest()->getActionName();

            /* Auto set store based on seller country */
            $sellerCountryID = $seller->getCountryId() ? strtolower($seller->getCountryId()) : null;

            $this->helper->setStoreBySellerCountry($sellerCountryID);
            /* Auto set store based on seller country */

            $allowedUrl = [
                'paypal/express/getTokenData',
                'customer/section/load',
                'checkout/index/index',
                'checkout/cart/add',
                'checkout/onepage/success',
                'lofmpmembership/buy/index',
                'paypal/express/onAuthorization',
                'paypal/express/cancel',
                '/seller/login',
                'lofmarketplace/seller/login',
                'lofmarketplace/seller/loginPost'
            ];

            $currentUrl = $routeName.'/'.$controllerName.'/'.$actionName;

            if ($currentUrl == "customer/account/logoutSuccess") {
                return $this->_redirectUrl($this->helper->getPwaBaseUrl());
            }

            if ($currentUrl == "checkout/onepage/success") {
                $this->messageManager->addSuccess(__('Membership plan upgraded successfully'));
            }

            if (!in_array($currentUrl, $allowedUrl)) {
                return $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
            }
        }

        return $this;
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->action->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->action->getResponse();
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
}
