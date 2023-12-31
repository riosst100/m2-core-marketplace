<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\PriceComparison\Controller\Order;

use Magento\Framework\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Reorder extends Action\Action
{
    /**
     * @var OrderLoaderInterface
     */
    protected $_orderLoader;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param \Lofmp\PriceComparison\Helper\Data $helperData
     */
    public function __construct(
        Action\Context $context,
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Checkout\Model\Session $session,
        ProductRepositoryInterface $productRepository,
        \Lofmp\PriceComparison\Helper\Data $helperData
    ) {
        $this->_orderLoader = $orderLoader;
        $this->_coreRegistry = $registry;
        $this->_cart = $cartFactory;
        $this->_session = $session;
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * Action for reorder
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->helperData->isEnabled()) {
            $result = $this->_orderLoader->load($this->_request);
            if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
                return $result;
            }
            $cart = $this->_cart->create();
            $order = $this->_coreRegistry->registry('current_order');
            $redirect = $this->resultRedirectFactory->create();
            $orderItems = $order->getItemsCollection();
            
            foreach ($orderItems as $item) {
                try {
                    $this->processItem($item, $cart);
                } catch (LocalizedException $e) {
                    $msg = $e->getMessage();
                    if ($this->_session->getUseNotice(true)) {
                        $this->messageManager->addNotice($msg);
                    } else {
                        $this->messageManager->addError($msg);
                    }
                    return $redirect->setPath('sales/order/history');
                } catch (\Exception $e) {
                    $msg = 'We can\'t add this item to your shopping cart right now.';
                    $this->messageManager->addException($e, __($msg));
                    return $redirect->setPath('checkout/cart');
                }
            }
        }
        return $redirect->setPath('checkout/cart');
    }

    public function processItem($item, $cart)
    {
        $cart->addOrderItem($item);
        $cart->save();
        $info = $item->getProductOptionByCode('info_buyRequest');
        $product = $this->_initProduct($item->getProductId());
        $this->_eventManager->dispatch(
            'checkout_cart_add_product_complete',
            [
                'product' => $product,
                'product_id' => $item->getProductId(),
                'info' => $info,
                'request' => $this->getRequest(),
                'response' => $this->getResponse()
            ]
        );
    }

    /**
     * Initialize product instance from request data
     * @param int|null $productId
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct($productId = null)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
