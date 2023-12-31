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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lofmp\SellerMembership\Controller\Marketplace\Membership;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @var \Lof\MarketPlace\Model\SalesFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lofmp\SellerMembership\Helper\Data $helper_membership
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\SellerMembership\Helper\Data $helper_membership,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->helper_membership = $helper_membership;
        $this->helper = $helper;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->membership = $membership;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
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
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $seller_id = $seller->getData('seller_id');
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $membership = $this->membership->getCollection()->addFieldToFilter('seller_id', $seller_id);
            if (count($membership->getData()) == 0) {
                $data = [];
                $group_id = $this->helper->getConfig('seller_settings/default_seller_group');
                $group = $this->_objectManager->create(\Lof\MarketPlace\Model\Group::class)->load($group_id);
                if ($group && $group->getGroupId()) {
                    $data['seller_id'] = $seller_id;
                    $data['group_id'] = $group_id;
                    $collection = $this->_productCollectionFactory->create()
                        ->addAttributeToFilter('seller_group', $group_id)->getFirstItem();
                    if ($collection->getData()) {
                        $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                            ->load($collection->getId());
                        if ($product && $product->getId()) {
                            $model = $this->_objectManager->create(\Lofmp\SellerMembership\Model\Membership::class);
                            $sellerDuration = $product->getSellerDuration();
                            $sellerDuration = isset($sellerDuration[0]) ? $sellerDuration[0] : ['membership_unit' => 'month', 'membership_price' => $product->getPrice(), 'membership_duration' => 1];
                            $data['name'] = $product->getName();
                            $data['price'] = $sellerDuration['membership_price'];
                            $unit = $sellerDuration['membership_unit'];
                            $data['duration'] = $sellerDuration['membership_duration'] . ' ' . $unit;
                            $expiration_date = $this->helper_membership->getExpirationDate(
                                $sellerDuration['membership_duration'],
                                $sellerDuration['membership_unit']
                            );
                            $time = time() + $expiration_date;
                            $date = date('Y-m-d h:i:s A', $time);
                            $data['expiration_date'] = $date;
                            $model->setData($data)->save();

                            $this->_eventManager->dispatch(
                                'controller_action_lofmp_seller_membership_default_save_after',
                                [
                                    'controller' => $this,
                                    'membership' => $model,
                                    'group' => $group,
                                    'product' => $product
                                ]
                            );
                        }
                    }
                }
            }

            //$this->_view->getPage()->getConfig()->getTitle()->prepend(__("Membership Plans"));

            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
