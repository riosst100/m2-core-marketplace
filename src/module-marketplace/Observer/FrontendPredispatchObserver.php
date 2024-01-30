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
     * PredispatchObserver constructor.
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\State $state
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->session = $customerSession;
        $this->actionFlag = $actionFlag;
        $this->state = $state;
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

            // $routeName = $this->action->getRequest()->getRouteName();
            // if ($customerSession->isLoggedIn() && $seller) {
            //     if ($routeName != "marketplace" && $routeName != "lofmpmembership") {
            //         $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
            //     }
            // } else {
            //     if ($routeName != "lofmarketplace") {
            //         $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
            //     }
            // }
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
