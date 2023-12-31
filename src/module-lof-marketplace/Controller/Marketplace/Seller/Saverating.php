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

namespace Lof\MarketPlace\Controller\Marketplace\Seller;

use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Saverating extends \Magento\Framework\App\Action\Action
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
     *
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
     * Saverating constructor.
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
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
    }

    /**
     * @param $route
     * @param $params
     * @return string|null
     */
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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $sellerModel = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $sellerModel->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $id = $data['rating_id'];
                $rating = $objectManager->get(\Lof\MarketPlace\Model\Rating::class)->load($id);
                try {
                    if ($data['status'] != $rating->getData('status')) {
                        $rating->setData('status', $data['status'])->save();

                        $this->_eventManager->dispatch('seller_save_rating', [
                            'object' => $this,
                            'seller' => $sellerModel,
                            'rating' => $rating,
                            'data' => $data
                        ]);
                    }

                    $data['namestore'] = $this->helper->getStoreName();
                    $data['urllogin'] = $this->helper->getBaseStoreUrl().'customer/account/login';

                    $this->_redirect($data['currUrl']);
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
