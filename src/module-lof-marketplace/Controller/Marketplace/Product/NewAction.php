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

namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewAction extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    const DEFAULT_ATTRIBUTE_SET_ID = 4; // default attribute set id = 4

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
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var
     */
    protected $productBuilder;

    /**
     * @var
     */
    protected $wysiwygConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var StoreFactory|mixed|null
     */
    protected $storeFactory;

    /**
     * NewAction constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\GroupFactory $groupFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param Registry $registry
     * @param ProductFactory $productFactory
     * @param StoreFactory|null $storeFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\GroupFactory $groupFactory,
        \Magento\Framework\Url $frontendUrl,
        Registry $registry,
        ProductFactory $productFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        StoreFactory $storeFactory = null
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->helper = $helper;
        $this->groupFactory = $groupFactory;
        $this->storeFactory = $storeFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreFactory::class);
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
     * Check available product types for seller
     *
     * @param string $typeId
     * @return bool|int
     */
    protected function checkAvailableType($typeId = "simple")
    {
        $allowBundle = $this->helper->getConfig("seller_settings/allow_add_bundle");
        $allowConfigurable = $this->helper->getConfig("seller_settings/allow_add_configurable");
        $allowDownloadable = $this->helper->getConfig("seller_settings/allow_add_downloadable");
        $allowVirtual = $this->helper->getConfig("seller_settings/allow_add_virtual");

        if ($typeId == "configurable" && !$allowConfigurable) {
            return false;
        }

        if ($typeId == "bundle" && !$allowBundle) {
            return false;
        }

        if ($typeId == "downloadable" && !$allowDownloadable) {
            return false;
        }

        if ($typeId == "virtual" && !$allowVirtual) {
            return false;
        }
        return true;
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $localeInterface = $objectManager->create(\Magento\Framework\Locale\ResolverInterface::class);
        $localeInterface->setLocale('en_US');
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            if ($this->helper->isEnableModule('Lofmp_SellerMembership')) {
                $group = $this->groupFactory->create()->load($seller->getData('group_id'), 'group_id');
                $limitProduct = $group->getData('limit_product');
                $productCount = $seller->getData('product_count');
                if (($limitProduct > 0 && $limitProduct <= $productCount) || $limitProduct == 0) {
                    $this->messageManager->addNoticeMessage(
                        __('Your product limit is %1, please upgrade the membership package.', $limitProduct)
                    );
                    $result = $this->_redirect('catalog/product/');
                    return $result;
                }
            }

            $request = $this->request;
            $typeId = $request->getParam('type');
            if (!$this->checkAvailableType($typeId)) {
                $this->messageManager->addNoticeMessage(
                    __('Sorry, we does not support product type at now. Please try to add new product again!')
                );
                $result = $this->_redirect('catalog/product/');
                return $result;
            }

            $this->_eventManager->dispatch(
                'lof_marketplace_catalog_product_new_action_before',
                ['controller' => $this, 'seller' => $seller]
            );
            $defaultAttributeSetId = (int)$this->helper->getConfig("seller_settings/default_attribute_set_id");
            $storeId = $request->getParam('store', 0);
            $attributeSetId = (int)$request->getParam('set');
            if ($attributeSetId == 0) {
                $attributeSetId = $defaultAttributeSetId ? $defaultAttributeSetId : self::DEFAULT_ATTRIBUTE_SET_ID; //default attribute set id = 4
            }

            $product = $this->createEmptyProduct($typeId, $attributeSetId, $storeId);
            $store = $this->storeFactory->create();
            $store->load($storeId);

            $this->registry->register('product', $product);
            $this->registry->register('current_product', $product);
            $this->registry->register('current_store', $store);
            $this->_eventManager->dispatch('catalog_product_new_action', ['product' => $product]);
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        return null;
    }

    /**
     * @param int $typeId
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function createEmptyProduct($typeId, $attributeSetId, $storeId): Product
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->productFactory->create();
        $product->setData('_edit_mode', true);

        if ($typeId !== null) {
            $product->setTypeId($typeId);
        }

        if ($storeId !== null) {
            $product->setStoreId($storeId);
        }

        if ($attributeSetId) {
            $product->setAttributeSetId($attributeSetId);
        }

        return $product;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $defaultAttributeSetId = (int)$this->helper->getConfig("seller_settings/default_attribute_set_id");
        $attributeSetId = (int)$request->getParam('set');
        if ($attributeSetId == 0) {
            $attributeSetId = $defaultAttributeSetId ? $defaultAttributeSetId : self::DEFAULT_ATTRIBUTE_SET_ID;
            $params = $request->getParams();
            $params["set"] = $attributeSetId;
            $request->setParams($params);
        }

        return parent::dispatch($request);
    }
}
