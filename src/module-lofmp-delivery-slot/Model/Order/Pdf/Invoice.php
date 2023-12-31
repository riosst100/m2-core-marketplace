<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\DeliverySlot\Model\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Framework\Locale\ResolverInterface;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * @var ResolverInterface
     */
    protected $_localeResolver;

    public function __construct(
        \Lofmp\DeliverySlot\Model\DeliverySlots $deliverySlots,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        ResolverInterface $localeResolver
    ) {
        $this->deliverySlots = $deliverySlots;
        $this->_localeResolver = $localeResolver;
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $storeManager, $appEmulation);
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $pages = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($pages, $invoice->getStore());
            /* Add address */
            $this->insertAddress($pages, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $pages,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($pages, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($pages);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $pages, $order);
                $pages = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($pages, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }


        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $orders = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $orders = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $orders->getRealOrderId(), 35, $top -= 30, 'UTF-8');
            $top += 15;
        }

        $top -= 30;
        $page->drawText(
            __('Order Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $orders->getStore(),
                    $orders->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($orders->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($orders->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$orders->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($this->addressRenderer->format($orders->getShippingAddress(), 'pdf'));
            $shippingMethod = $orders->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$orders->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeights = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeights = max($addressesHeights, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeights);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $values) {
            if ($values !== '') {
                $text = [];
                foreach ($this->string->split($values, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$orders->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $values) {
                if ($values !== '') {
                    $text = [];
                    foreach ($this->string->split($values, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            //start of delivery
            if ($orders->getDeliverySlotId() != null && !empty($orders->getDeliverySlotId())) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 25);
                $this->y -= 15;
                $this->_setFontBold($page, 12);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $pages=$page;
                $page->drawText(__('Delivery Slot'), 35, $this->y, 'UTF-8');
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
                $this->_setFontRegular($page, 10);
                $pages='';
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y - 10, 570, $this->y - 100);
                $this->y -= 50;
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $yslot = $this->y + 20;
                $deliverySlotDatas= $this->deliverySlots->load($orders->getDeliverySlotId());
                $timeSlot=$deliverySlotDatas->getStartTime().' - '.$deliverySlotDatas->getEndTime();
                $page->drawText(strip_tags(trim("Shipping Delivery Date : " . $orders->getDeliveryDate())), 35, $yslot, 'UTF-8');
                $yslot -= 15;
                $page->drawText(strip_tags(trim("Shipping Delivery Slot : " . $timeSlot)), 35, $yslot, 'UTF-8');
                $yslot -= 15;
                $page->drawText(strip_tags(trim("Shipping Delivery Comment : " . $orders->getDeliveryComment())), 35, $yslot, 'UTF-8');
                $this->y -= 35;
            }
            //end of delivery

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);
            if ($orders->getDeliverySlotId() != null && !empty($orders->getDeliverySlotId())) {
                $this->y -= 15;
            } else {
                $this->y -= 15;
            }
            $pages='';

            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');
            $pages='';
            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        foreach ($payment as $values) {
            if (trim($values) != '') {
                //Printing "Payment Method" lines
                $values = preg_replace('/<br[^>]*>/i', "\n", $values);
                foreach ($this->string->split($values, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($orders->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_values) {
                $page->drawText(strip_tags(trim($_values)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . __(
                'Total Shipping Charges'
            ) . " " . $orders->formatPriceTxt(
                $orders->getShippingAmount()
            ) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;
            $pages='';

            $tracksArray = [];
            if ($shipment) {
                $tracksArray = $shipment->getAllTracks();
            }
            if (count($tracksArray)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracksArray as $trackdata) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($trackdata->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($trackdata->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($trackdata->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }
}
