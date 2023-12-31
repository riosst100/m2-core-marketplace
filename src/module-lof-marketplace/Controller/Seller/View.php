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

namespace Lof\MarketPlace\Controller\Seller;

use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\Seller $sellerModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Resolver $layerResolver
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\Seller $sellerModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Lof\MarketPlace\Helper\Data $sellerHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_sellerModel = $sellerModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_sellerHelper = $sellerHelper;
    }

    public function _initSeller()
    {
        $sellerId = (int)$this->getRequest()->getParam('seller_id', false);
        if (!$sellerId) {
            return false;
        }
        try {
            $seller = $this->_sellerModel->load($sellerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_seller', $seller);
        return $seller;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->_sellerHelper->getConfig('general_settings/enable')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $seller = $this->_initSeller();
        if ($seller) {
            $this->layerResolver->create('seller');
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($seller->getPageLayout()) {
                $page->getConfig()->setPageLayout($seller->getPageLayout());
            }
            if (($layoutUpdate = $seller->getLayoutUpdateXml()) && trim($layoutUpdate) != '') {
                $page->addUpdate($layoutUpdate);
            }
            $page->getConfig()->addBodyClass('page-products')
                ->addBodyClass('seller-' . $seller->getUrlKey());
            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        return null;
    }
}
