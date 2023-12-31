<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_ChatSystem
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\ChatSystem\Block\Chat;

use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller;
use Lof\MarketPlace\Model\Seller as SellerModel;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Lofmp\ChatSystem\Helper\Url;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Form;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Chat extends Template
{
    /**
     * @var int
     */
    private $_username = -1;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $_customerSession;
    /**
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $chat;

    /**
     *
     * @var \Lofmp\ChatSystem\Helper\Data
     */
    protected $helper;
    /**
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var Data
     */
    protected $marketHelper;

    /**
     * @var Seller
     */
    protected $sellerHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var int|null
     */
    protected $currentSellerId = null;

    /**
     * Chat constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param Session $customerSession
     * @param Data $marketHelper
     * @param Seller $sellerHelper
     * @param Url $customerUrl
     * @param \Lofmp\ChatSystem\Helper\Data $helper
     * @param \Lofmp\ChatSystem\Model\Chat $chat
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param CollectionFactory $sellerCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        Session $customerSession,
        Data $marketHelper,
        Seller $sellerHelper,
        Url $customerUrl,
        \Lofmp\ChatSystem\Helper\Data $helper,
        \Lofmp\ChatSystem\Model\Chat $chat,
        \Magento\Framework\App\Http\Context $httpContext,
        CollectionFactory $sellerCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->marketHelper = $marketHelper;
        $this->sellerHelper = $sellerHelper;
        $this->helper = $helper;
        $this->chat = $chat;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->httpContext = $httpContext;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * get logged in seller id
     *
     * @return int
     */
    public function getLoggedinSellerId()
    {
        if ($this->currentSellerId == null) {
            if ($this->_customerSession->isLoggedIn() ) {
                $customerId = $this->_customerSession->getId();
                $seller = $this->sellerCollectionFactory->create()
                                ->addFieldToFilter("customer_id", $customerId)
                                ->addFieldToFilter("status", SellerModel::STATUS_ENABLED)
                                ->getFirstItem();
                $this->currentSellerId = $seller && $seller->getId() ? $seller->getId() : 0;
            } else {
                $this->currentSellerId = 0;
            }
        }
        return $this->currentSellerId;
    }

    /**
     * Get current seller
     */
    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }

    /**
     * get current product
     */
    public function getProduct()
    {
        $curPro = $this->_coreRegistry->registry('current_product');
        return $curPro;
    }

    /**
     * Is login
     *
     * @return bool
     */
    public function isLogin()
    {
        return (bool)$this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * get current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Get customer
     *
     * @return \Magento\Framework\App\Http\Context
     */
    public function getCustomer()
    {
        return $this->httpContext;
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerUrl->getLoginPostUrl();
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_customerUrl->getForgotPasswordUrl();
    }

    /**
     * get registrer url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    }

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return ( bool )!$this->_scopeConfig->getValue(
            Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
