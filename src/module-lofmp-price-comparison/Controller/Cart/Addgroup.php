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


namespace Lofmp\PriceComparison\Controller\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Cart as CustomerCart;

class Addgroup extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param \Lofmp\PriceComparison\Helper\Data $helperData
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Lofmp\PriceComparison\Helper\Data $helperData
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
    }
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if ($this->helperData->isEnabled()) {
            $itemIds = $this->getRequest()->getParam('order_items', []);
            if (is_array($itemIds)) {
                $items = $this->_objectManager
                                ->create('Magento\Sales\Model\Order\Item')
                                ->getCollection()
                                ->addIdFilter($itemIds)
                                ->load();
                foreach ($items as $item) {
                    try {
                        $this->processItem($item);
                    } catch (LocalizedException $e) {
                        $msg = $e->getMessage();
                        if ($this->_checkoutSession->getUseNotice(true)) {
                            $this->messageManager->addNotice($msg);
                        } else {
                            $this->messageManager->addError($msg);
                        }
                    } catch (\Exception $e) {
                        $msg = 'We can\'t add this item to your shopping cart right now.';
                        $this->messageManager->addException($e, __($msg));
                        $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                        return $this->_goBack();
                    }
                }
            }
            return $this->_goBack();
        } else {
            return parent::execute();
        }
    }

    public function processItem($item)
    {
        $this->cart->addOrderItem($item, 1);
        $this->cart->save();
        $info = $item->getProductOptionByCode('info_buyRequest');
        $product = $this->_initProduct($item->getProductId());
        /**
         * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
         */
        $this->_eventManager->dispatch(
            'checkout_cart_add_product_complete',
            ['product' => $product, 'product_id' => $item->getProductId(), 'info' => $info, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
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
