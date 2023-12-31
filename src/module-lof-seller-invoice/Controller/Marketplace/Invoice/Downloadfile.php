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
 * @package    Lof_SellerInvoice
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\SellerInvoice\Controller\Marketplace\Invoice;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\LayoutFactory;

class Downloadfile extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $_invoice;

    /**
     * @var \Lof\MarketPlace\Model\InvoiceFactory
     */
    protected $_sellerInvoice;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Lof\MarketPlace\Model\OrderFactory
     */
    protected $_sellerOrder;

    protected $_objectManager;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoice
     * @param \Lof\MarketPlace\Model\InvoiceFactory $sellerInvoice
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param \Lof\MarketPlace\Model\OrderFactory $sellerOrder
     * @param \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param LayoutFactory $layoutFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $invoice,
        \Lof\MarketPlace\Model\InvoiceFactory $sellerInvoice,
        \Magento\Sales\Model\OrderFactory $order,
        \Lof\MarketPlace\Model\OrderFactory $sellerOrder,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_invoice = $invoice;
        $this->_sellerInvoice = $sellerInvoice;
        $this->_order = $order;
        $this->_sellerOrder = $sellerOrder;
        $this->invoiceRepository  = $invoiceRepository;
        $this->_objectManager    = $objectManager;
        $this->_pdf              = $pdf;
        $this->layoutFactory     = $layoutFactory;
        $this->_fileFactory      = $fileFactory;
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
    protected function _redirectUrl($url)
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
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $sellerId = $seller->getId();
        $status = $seller->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $invoice = $this->getInvoice($sellerId);
            if (!$invoice->getId()) {
                $this->messageManager->addErrorMessage(__('Invoice capturing error'));
                return $this->_redirect('catalog/sales');
            }

            $sellerInvoice = $this->getSellerInvoice();
            if ($sellerId != $sellerInvoice->getSellerId()) {
                $this->messageManager->addErrorMessage(__('Permission denied.'));
                return $this->_redirect('catalog/sales');
            }

            $mageInvoice_id = $invoice->getId();
            $mageInvoice = $this->invoiceRepository->get($mageInvoice_id);
            $this->getRequest()->setParam("order_id", $invoice->getOrderId());
            //$order = $this->getOrder();
            //$sellerOrder = $this->getSellerOrder();
            //$mageOrder = $this->_orderRepository->get($order->getId());
            //$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
            //$mediaRootDir = $mediaDirectory->getAbsolutePath();
            $orderIncrement = $invoice->getIncrementId();

            $fileName = __('Invoice_#');
            $fileName .= $orderIncrement. '_' . $sellerId .'.pdf';

            $return = $this->_pdf->generatePdf($invoice, $mageInvoice, $sellerId);
            $pdf_output = isset($return['output'])?$return['output']:'';
            return $this->_fileFactory->create(
                $fileName,
                $pdf_output,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        return null;
    }

    /**
     * @param int $sellerId
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice($sellerId = 0)
    {
        $invoiceId = $this->getInvoiceId();
        if (!$invoiceId) {
            $orderId = $this->getRequest()->getParam("order_id");
            $collection = $this->_sellerInvoice->create()->getCollection()
                                            ->addFieldToFilter("seller_id", $sellerId)
                                            ->addFieldToFilter("seller_order_id", $orderId);
            if ($foundItem = $collection->getFirstItem()) {
                $invoiceId = $foundItem->getInvoiceId();
                $this->getRequest()->setParam("id", $invoiceId);
            }
        }
        return $this->_invoice->create()->load((int)$invoiceId);
    }

    /**
     * @return \Lof\MarketPlace\Model\Invoice
     */
    public function getSellerInvoice()
    {
        return $this->_sellerInvoice->create()->load($this->getInvoiceId(), 'invoice_id');
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order->create()->load($this->getInvoice()->getOrderId());
    }

    /**
     * @return \Lof\MarketPlace\Model\Order
     */
    public function getSellerOrder()
    {
        return $this->_sellerOrder->create()->load($this->getInvoice()->getOrderId(), 'order_id');
    }

    /**
     * @return int
     */
    public function getInvoiceId()
    {
        return (int)$this->getRequest()->getParam('id');
    }
}
