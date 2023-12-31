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

namespace Lof\MarketPlace\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Session extends \Magento\Customer\Model\Session
{
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerModel;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_sessionFactory;

    /**
     * current logged in seller id
     *
     * @var int
     */
    protected $_sellerId;

    /**
     * flag check seller account is approved or not, default = true
     *
     * @var bool
     */
    protected $_approvedSellerAccount = true;

    /**
     * Session constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param Share $configShare
     * @param \Magento\Framework\Url\Helper\Data $coreUrl
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param ResourceCustomer $customerResource
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Customer\Model\SessionFactory
     * @throws \Magento\Framework\Exception\SessionException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Customer\Model\Url $customerUrl,
        ResourceCustomer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        CustomerRepositoryInterface $customerRepository,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\App\Response\Http $response,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\SessionFactory $sessionFactory
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $configShare,
            $coreUrl,
            $customerUrl,
            $customerResource,
            $customerFactory,
            $urlFactory,
            $session,
            $eventManager,
            $httpContext,
            $customerRepository,
            $groupManagement,
            $response
        );

        $this->_sellerFactory = $sellerFactory;
        $this->_sessionFactory = $sessionFactory;
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSeller()
    {
        $customerId = $this->getCustomerSession()->getCustomerId();

        if ($this->_sellerModel === null && $customerId && $this->_approvedSellerAccount) {
            $this->_sellerModel = $this->_sellerFactory->create()->load((int)$customerId, 'customer_id');
            if (Seller::STATUS_ENABLED != (int)$this->_sellerModel->getStatus()) {
                $this->_sellerModel = null;
                $this->_approvedSellerAccount = false;
            }
        }

        return $this->_sellerModel;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerId()
    {
        if (empty($this->_sellerId)) {
            $sellerModel = $this->getSeller();
            $this->_sellerId = $sellerModel ? (int)$sellerModel->getId() : 0;
        }

        return $this->_sellerId;
    }

    /**
     * @return \Magento\Framework\Session\Generic
     */
    public function getCustomerSession()
    {
        if (!$this->_session->getCustomerId()) {
            $this->_session = $this->_sessionFactory->create();
        }
        return $this->_session;
    }
}
