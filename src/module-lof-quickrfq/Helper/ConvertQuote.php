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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\QuoteFactory;

/**
 * Class ConvertQuote
 * @package Lof\Quickrfq\Helper
 */
class ConvertQuote extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var CartItemRepositoryInterface
     */
    protected $cartItemRepository;

    /**
     * @var CartItemInterfaceFactory
     */
    protected $cartitemDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var SerializerInterface
     */
    public $serializer;

    /**
     * @var CouponManagementInterface
     */
    protected $couponManagement;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Data $helperData
     * @param CartManagementInterface $cartManagement
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param CartItemInterfaceFactory $cartitemDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SerializerInterface $serializer
     * @param CouponManagementInterface $couponManagement
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        Context $context,
        Data $helperData,
        CartManagementInterface $cartManagement,
        CartItemRepositoryInterface $cartItemRepository,
        CartItemInterfaceFactory $cartitemDataFactory,
        DataObjectHelper $dataObjectHelper,
        SerializerInterface $serializer,
        CouponManagementInterface $couponManagement,
        QuoteFactory $quoteFactory
    ) {
        parent::__construct($context);

        $this->helperData = $helperData;
        $this->cartManagement = $cartManagement;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartitemDataFactory = $cartitemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer;
        $this->couponManagement = $couponManagement;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * processCreateCart
     * 
     * Step 1: Follow API: /V1/customers/{customerId}/carts create empty cart for customer Id
     * Step 2: Follow API: /V1/carts/{quoteId}/items add cart items by
     * Step 3: Save Cart info into table "lof_quickrfq_cart"
     * 
     * @param \Lof\Quickrfq\Model\Quickrfq|null $quoteModel
     * @return int|string|null $cartId
     */
    public function processCreateCart( $quoteModel = null )
    {
       $cartId = "";
       if ($quoteModel && $quoteModel->getId()) {
           //create cart for this quote.
           //Step 1: Create Cart For Customer and Store Id
           $cartId = $this->cartManagement->createEmptyCart();
           if ($quoteModel->getCustomerId()) {
                $this->cartManagement->assignCustomer($cartId, $quoteModel->getCustomerId(), $quoteModel->getStoreId());
           }
           //Step 2: Add product items to Cart
           if ($cartId) {
                $attributes = $quoteModel->getAttributes();
                //$buyInfoRequest = $quoteModel->getBuyInfoRequest();
                $productId = $quoteModel->getProductId();
                $productData = $quoteModel->getProduct((int)$productId);

                $customPrice = $quoteModel->getAdminPrice()?(float)$quoteModel->getAdminPrice():(float)$quoteModel->getPricePerProduct();

                $cartItemData = [
                    'sku' => $productData->getSku(),
                    'qty' => $quoteModel->getAdminQuantity()?(int)$quoteModel->getAdminQuantity():(int)$quoteModel->getQuantity(),
                    'name' => $productData->getName(),
                    'price' => $customPrice,
                    'product_type' => $productData->getTypeId(),
                    'quote_id' => $cartId
                ];

                //Comming soon feature - TODO
                if ($attributes) {
                    //$attributesData = $this->serializer->unserialize( $attributes );
                    //convert productOptions array to \Magento\Quote\Api\Data\ProductOptionInterface
                    $productOption = null;
                    $cartItemData["product_option"] = $productOption;
                }

                $cartItemDataObject = $this->getCartItemDataModel( $cartItemData );
                $cartItem = $this->cartItemRepository->save( $cartItemDataObject );

                //Step 3: Add Custom item price
                if ($cartItem) {
                    $mageQuoteModel = $this->quoteFactory->create()->load((int)$cartId);
                    $quoteItem = $mageQuoteModel->getItemById($cartItem->getId());
                    $old_price = $quoteItem->getPrice();
                    if ($old_price !== $customPrice) {
                        $quoteItem->setCustomPrice($customPrice);
                        $quoteItem->setOriginalCustomPrice($customPrice);
                        $quoteItem->setOriginalPrice($old_price);
                    }
                    $quoteItem->setDescription(__("Added item from Quick RFQ ID %1", $quoteModel->getQuickrfqId()));
                    $quoteItem->save();
                    
                    $mageQuoteModel->collectTotals();
                }
                //Step 4: Auto Apply coupon code if admin added
                if ($cartItem && $quoteModel->getCouponCode() && $this->helperData->getConfig("quote_process/auto_apply_coupon")) {
                    $this->couponManagement->set($cartId, $quoteModel->getCouponCode());
                }
           }
       }
       return $cartId;
    }

    /**
     * Retrieve cart item object with cart item array data
     * @param array|null $cartItem
     * @return CartItemInterface
     */
    public function getCartItemDataModel( $cartItem = [])
    {
        $cartitemDataObject = $this->cartitemDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $cartitemDataObject,
            $cartItem,
            CartItemInterface::class
        );
        
        return $cartitemDataObject;
    }
}