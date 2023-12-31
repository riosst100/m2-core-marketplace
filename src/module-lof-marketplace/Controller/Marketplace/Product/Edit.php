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

use Magento\Framework\Registry;
use Magento\Store\Model\StoreFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var ProductRepositoryInterface|mixed|null
     */
    private $productRepository;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreFactory|mixed|null
     */
    private $storeFactory;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param StoreFactory|null $storeFactory
     * @param ProductRepositoryInterface|null $productRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Url $frontendUrl,
        Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        StoreFactory $storeFactory = null,
        ProductRepositoryInterface $productRepository = null
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ProductRepositoryInterface::class);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->registry = $registry;
        $this->_actionFlag = $context->getActionFlag();
        $this->session = $customerSession;
        $this->helper = $helper;
        $this->_url = $context->getUrl();
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
     * Product edit form
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        if (!$customerSession->isLoggedIn()) {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        $productId = (int)$this->getRequest()->getParam('id');
        $sellerId = $this->helper->getSellerId();

        $storeId = $this->getRequest()->getParam('store');
        if (!$storeId) {
            $storeId = 0;
        }

        try {
            $product = $this->productRepository->getById($productId, true, $storeId);

            if ($productId && !$product->getId()) {
                $this->messageManager->addErrorMessage(__('This product no longer exists.'));
                return $this->_redirect('catalog/product');
            }

            if (!$product->getSellerId() || $product->getSellerId() != $sellerId) {
                $this->messageManager->addErrorMessage(__('Something went wrong!'));
                return $this->_redirect('catalog/product');
            }

            $productType = $product->getTypeId();
            if ($productType && $productType == 'configurable') {
                $this->getRequest()->setParams(['type' => $productType]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('This product no longer exists.'));
            return $this->_redirect('catalog/product');
        }

        $store = $this->storeFactory->create();
        $store->load(0);
        $this->registry->register('product', $product);
        $this->registry->register('current_product', $product);
        $this->registry->register('current_store', $store);
        $resultPage = $this->resultPageFactory->create();
        $title = $resultPage->getConfig()->getTitle();
        $title->prepend(__("Catalog"));
        $title->prepend(__("Manage Products"));
        $title->prepend($product->getName());

        if (!$this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->isSingleStoreMode()
            &&
            ($switchBlock = $resultPage->getLayout()->getBlock('store_switcher'))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->_url->getUrl(
                        'catalog/product/*/*',
                        ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
                    )
                );
        }

        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId(0);
        }
        return $resultPage;
    }
}
