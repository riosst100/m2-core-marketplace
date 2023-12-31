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
 * @package    Lof_FollowUpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SellerInvoice\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;
class OrderStatusChanged implements ObserverInterface
{
    protected $helper;


    protected $sender;

    protected $request;

    protected $sellerProduct;

    protected $_objectManager;

    protected $invoiceRepository;

    protected $invoiceEmail;

    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Lof\SellerInvoice\Helper\Mail $Mail,
        \Lof\MarketPlace\Model\Sender $sender
    ) {
        $this->sender         = $sender;
        $this->request        = $request;
        $this->helper         = $helper;
        $this->invoiceEmail   = $Mail;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function getObject()
    {
        if(!$this->_objectManager){
           $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }

    /**
     * execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId   = $this->request->getParam('order_id');
        $id        = $observer->getEvent()->getInvoice()->getId();
        $model     = $this->getObject()->create('Magento\Sales\Model\Order\Invoice');
        $model->load($id);
        $mageInvoice_id = $model->getId();
        $mageInvoice    = $this->invoiceRepository->get($mageInvoice_id);
        $pdfModel  = $this->getObject()->create('\Lof\SellerInvoice\Model\Invoice\Pdf\Invoice');
        $sellerIds = $this->getSellerIds($orderId);
        if ($sellerIds) {
            foreach ($sellerIds as $sellerId) {
                $file  = $pdfModel->generatePdf($model, $mageInvoice, $sellerId);
                $this->invoiceEmail->sendNotificationNewInvoiceEmail($mageInvoice, $model, $file);
            }
            $messageManager = $this->getObject()->get("Magento\Framework\Message\ManagerInterface");
            $messageManager->addSuccess(__('Send seller invoice file success!'));
        }
    }

    /**
     * get seller ids
     *
     * @param int $orderId
     * @return array
     */
    public function getSellerIds($orderId)
    {
        $orderModel = $this->getObject()->create('Lof\MarketPlace\Model\Order');
        $sellerData = $orderModel->getCollection()->addFieldToFilter("order_id", $orderId);
        $sellerIds  = [] ;
        foreach ($sellerData as $val) {
            $sellerIds[] = $val["seller_id"];
        }
        return $sellerIds;
    }
}
