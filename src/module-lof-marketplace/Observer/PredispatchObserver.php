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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PredispatchObserver implements ObserverInterface
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
     * PredispatchObserver constructor.
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->session = $customerSession;
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param Observer $observer
     * @return PredispatchObserver|void
     */
    public function execute(Observer $observer)
    {
        $controllerAction = $observer->getData('controller_action');
        $this->action = $controllerAction;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get(\Magento\Framework\App\State::class);
        $area = $state->getAreaCode();

        if ($area === 'marketplace') {
            $customerSession = $this->session;
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();

            if ($customerSession->isLoggedIn() && $status == 0) {
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
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
