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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\PreOrder\Model\Plugin;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Helper\Data;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

/**
 * Quote item quantity validator.
 *
 * @api
 * @since 100.0.2
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @deprecated 2.3.0 Replaced with Multi Source Inventory
 * @link https://devdocs.magento.com/guides/v2.3/inventory/index.html
 * @link https://devdocs.magento.com/guides/v2.3/inventory/catalog-inventory-replacements.html
 */
class QuantityValidator extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator
{
     /**
      * @var QuantityValidator\Initializer\Option
      */
    protected $optionInitializer;

    /**
     * @var QuantityValidator\Initializer\StockItem
     */
    protected $stockItemInitializer;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockState;
    /**
     * @var \Lof\Preorder\Helper\Data
     */
    protected $helper;
    /**
     * @param Option $optionInitializer
     * @param StockItem $stockItemInitializer
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @return void
     */
    public function __construct(
        Option $optionInitializer,
        StockItem $stockItemInitializer,
        StockRegistryInterface $stockRegistry,
        \Lof\PreOrder\Helper\Data $helper,
        StockStateInterface $stockState
    ) {
        $this->helper = $helper;
        $this->optionInitializer = $optionInitializer;
        $this->stockItemInitializer = $stockItemInitializer;
        $this->stockRegistry = $stockRegistry;
        $this->stockState = $stockState;
    }
     /**
      * Check product inventory data when quote item quantity declaring
      *
      * @param \Magento\Framework\Event\Observer $observer
      *
      * @return void
      * @throws \Magento\Framework\Exception\LocalizedException
      * @SuppressWarnings(PHPMD.CyclomaticComplexity)
      * @SuppressWarnings(PHPMD.NPathComplexity)
      * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
      */
    public function validate(Observer $observer)
    {
        /* @var $quoteItem Item */
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote()
        ) {
            return;
        }
        $product = $quoteItem->getProduct();
        $qty = $quoteItem->getQty();

        /* @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        if (!$stockItem instanceof StockItemInterface) {
            throw new LocalizedException(__('The Product stock item is invalid. Verify the stock item and try again.'));
        }

        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            foreach ($options as $option) {
                $this->optionInitializer->initialize($option, $quoteItem, $qty);
            }
        } else {
            $this->stockItemInitializer->initialize($stockItem, $quoteItem, $qty);
        }

        if ($quoteItem->getQuote()->getIsSuperMode()) {
            return;
        }

        /* @var \Magento\CatalogInventory\Api\Data\StockStatusInterface $stockStatus */
        $stockStatus = $this->stockRegistry->getStockStatus($product->getId(), $product->getStore()->getWebsiteId());

        /* @var \Magento\CatalogInventory\Api\Data\StockStatusInterface $parentStockStatus */
        $parentStockStatus = false;

        /**
         * Check if product in stock. For composite products check base (parent) item stock status
         */
        if ($quoteItem->getParentItem()) {
            $product = $quoteItem->getParentItem()->getProduct();
            $parentStockStatus = $this->stockRegistry->getStockStatus(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
        }

        if ($stockStatus && !$this->helper->isPreorder($product->getId()) && $product->getTypeId() != 'configurable') {
            if ($stockStatus->getStockStatus() === Stock::STOCK_OUT_OF_STOCK
                    || $parentStockStatus && $parentStockStatus->getStockStatus() == Stock::STOCK_OUT_OF_STOCK
            ) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    Data::ERROR_QTY,
                    __('This product is out of stock.')
                );
                $quoteItem->getQuote()->addErrorInfo(
                    'stock',
                    'cataloginventory',
                    Data::ERROR_QTY,
                    __('Some of the products are out of stock.')
                );
                return;
            } else {
                // Delete error from item and its quote, if it was set due to item out of stock
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
            }
        }

        /**
         * Check item for options
         */
        if ($options) {
            $qty = $product->getTypeInstance()->prepareQuoteItemQty($qty, $product);
            $quoteItem->setData('qty', $qty);
            if ($stockStatus) {
                $this->checkOptionsQtyIncrements($quoteItem, $options);
            }

            // variable to keep track if we have previously encountered an error in one of the options
            $removeError = true;
            foreach ($options as $option) {
                $result = $option->getStockStateResult();
                if ($result->getHasError()) {
                    $option->setHasError(true);
                    //Setting this to false, so no error statuses are cleared
                    $removeError = false;
                    $this->addErrorInfoToQuote($result, $quoteItem, $removeError);
                }
            }
            if ($removeError) {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
            }
        } else {
            if ($quoteItem->getParentItem() === null) {
                $result = $quoteItem->getStockStateResult();
                if ($result->getHasError()) {
                    $this->addErrorInfoToQuote($result, $quoteItem);
                } else {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
                }
            }
        }
    }
     /**
      * Add error information to Quote Item
      *
      * @param \Magento\Framework\DataObject $result
      * @param Item $quoteItem
      * @return void
      */
    private function addErrorInfoToQuote($result, $quoteItem)
    {
        $quoteItem->addErrorInfo(
            'cataloginventory',
            Data::ERROR_QTY,
            $result->getMessage()
        );

        $quoteItem->getQuote()->addErrorInfo(
            $result->getQuoteMessageIndex(),
            'cataloginventory',
            Data::ERROR_QTY,
            $result->getQuoteMessage()
        );
    }
     /**
      * Verifies product options quantity increments.
      *
      * @param Item $quoteItem
      * @param array $options
      * @return void
      */
    private function checkOptionsQtyIncrements(Item $quoteItem, array $options): void
    {
        $removeErrors = true;
        foreach ($options as $option) {
            $result = $this->stockState->checkQtyIncrements(
                $option->getProduct()->getId(),
                $quoteItem->getData('qty'),
                $option->getProduct()->getStore()->getWebsiteId()
            );
            if ($result->getHasError()) {
                $quoteItem->getQuote()->addErrorInfo(
                    $result->getQuoteMessageIndex(),
                    'cataloginventory',
                    Data::ERROR_QTY_INCREMENTS,
                    $result->getQuoteMessage()
                );

                $removeErrors = false;
            }
        }

        if ($removeErrors) {
            // Delete error from item and its quote, if it was set due to qty problems
            $this->_removeErrorsFromQuoteAndItem(
                $quoteItem,
                Data::ERROR_QTY_INCREMENTS
            );
        }
    }
}
