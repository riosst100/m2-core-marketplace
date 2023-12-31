<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{

    /**
     * @var array|mixed
     */
    protected $_sellerOrderTotals = [
        "subtotal" => 0,
        "tax" => 0,
        "discount" => 0,
        "grand_total" => 0
    ];

    /**
     * @var int
     */
    protected $_currentSellerId = 0;

    /**
     * @var \Lof\MarketPlace\Model\OrderitemsFactory
     */
    protected $_orderItemsFactory = null;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_appEmulation = null;

    /**
     * get current seller id
     *
     * @return int
     */
    public function getCurrentSellerId()
    {
        return $this->_currentSellerId;
    }

    /**
     * set current seller id
     *
     * @param int $sellerId
     * @return $this
     */
    public function setCurrentSellerId($sellerId)
    {
        $this->_currentSellerId = $sellerId;
        return $this;
    }


    /**
     * set current seller id
     *
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @return $this
     */
    public function setAppEmulation($emulation)
    {
        $this->_appEmulation = $emulation;
        return $this;
    }

    /**
     * get app emulation
     *
     * @return \Magento\Store\Model\App\Emulation
     */
    public function getAppEmulation()
    {
        return $this->_appEmulation;
    }

    /**
     * get order item
     *
     * @return \Lof\MarketPlace\Model\Orderitems
     */
    public function getOrderItems()
    {
        return $this->_orderItemsFactory->create();
    }

    /**
     * set order items factory
     *
     * @param \Lof\MarketPlace\Model\OrderitemsFactory $orderItemsFactory
     * @return $this
     */
    public function setOrderItems($orderItemsFactory)
    {
        $this->_orderItemsFactory = $orderItemsFactory;
        return $this;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $currentSellerId = $this->getCurrentSellerId();

        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->getAppEmulation()->startEnvironmentEmulation(
                    $invoice->getStoreId(),
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    true
                );
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            $shippingHandling = $order->getShippingAmount();
            $subtotal = $tax = $discount = 0;
            $grand_total = $shippingHandling;
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                $orderitems = $this->getOrderItems();
                $sellerOrderItem = $orderitems->load($item->getOrderItemId(), 'order_item_id');
                $sellerId = 0;
                if ($sellerOrderItem && $sellerOrderItem->getSellerId()) {
                    $sellerId = $sellerOrderItem->getSellerId();
                }
                if ($sellerId == $currentSellerId) {
                    /* Draw item */
                    $this->_drawItem($item, $page, $order);
                    $page = end($pdf->pages);

                    /* Calculate seller totals */
                    $discount_amount = $item->getData('discount_amount');
                    $tax_amount = $item->getData('tax_amount');
                    $total = $item->getData('base_row_total');
                    $row_total = $total + $tax_amount - $discount_amount;
                    $subtotal = $subtotal + $total;
                    $tax = $tax + $tax_amount;
                    $discount = $discount + $discount_amount;
                    $grand_total = $grand_total + $row_total;
                }
            }
            $this->_sellerOrderTotals = [
                "subtotal" => $subtotal,
                "tax" => $tax,
                "discount" => $discount,
                "grand_total" => $grand_total
            ];
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->getAppEmulation()->stopEnvironmentEmulation();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                $totalTitle = $total->getTitle();
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalAmount = $this->mappingTotalAmount($totalTitle, $totalData['amount']);
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalAmount,
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }

    /**
     * mapping total amount
     *
     * @param string $totalTitle
     * @param float|int $totalAmount
     * @return float|int
     */
    protected function mappingTotalAmount($totalTitle, $totalAmount)
    {
        $mappingFields = [
            "Subtotal" => "subtotal",
            "Discount" => "discount",
            "Tax" => "tax",
            "Grand Total" => "grand_total"
        ];
        if (isset($mappingFields[$totalTitle])) {
            $fieldKey = $mappingFields[$totalTitle];
            $totalAmount = isset($this->_sellerOrderTotals[$fieldKey]) ? $this->_sellerOrderTotals[$fieldKey] : $totalAmount;
        }
        return $totalAmount;
    }
}
