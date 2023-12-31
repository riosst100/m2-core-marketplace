<?php

namespace Lof\SellerInvoice\Model\Invoice\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;

class Invoice extends \Magento\Framework\DataObject
{
    protected $_layout;
    protected $layoutFactory;
    protected $_coreRegistry;
    protected $_objectManager = null;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\SellerInvoice\Helper\Pdf $pdf,
        \Magento\Framework\Registry $coreRegistry,
        LayoutFactory $layoutFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->layoutFactory = $layoutFactory;
        $this->_file = $file;
        $this->_fileFactory = $fileFactory;
        $this->_pdf = $pdf;
        $this->_coreRegistry          = $coreRegistry;
    }
    public function getObjectManager(){
        if(!$this->_objectManager){
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }
    public function generatePdf($invoice, $mageInvoice, $sellerId, $generate_file = false){
        $return = [];
        if($invoice && $mageInvoice){
            $mediaDirectory = $this->getObjectManager()->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
            $mediaRootDir = $mediaDirectory->getAbsolutePath();
            $invoice_increment = $invoice->getIncrementId();
            $fileName = __('invoice_#');
            $fileName .= $invoice_increment. '_' . $sellerId .'.pdf';
            $pdfFilePath = $mediaRootDir . $fileName;
            if ($this->_file->isExists($pdfFilePath))  {
                $this->_file->deleteFile($pdfFilePath);
            }
            $content_html = $this->getHtmlForPdf($invoice, $mageInvoice, $sellerId);
            $this->_pdf->setData($content_html);
            $pdf_output = $this->_pdf->renderOutput($fileName);
            if($generate_file) {
                $this->_fileFactory->create(
                            $fileName,
                            $pdf_output,
                            DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
            }
            $return["output"] = $pdf_output;
            $return['filename'] = $fileName;
        }
        return $return;
    }

    public function getHtmlForPdf($invoice, $mageInvoice, $sellerId)
    {
        $block = $this->layoutFactory->create()->createBlock('Lof\SellerInvoice\Block\Adminhtml\Invoice\InvoicePdf');
        $block->setData("seller_id", $sellerId);
        $block->setTemplate('Lof_SellerInvoice::invoice/pdf.phtml');

        $data = [
            'invoice' => $invoice,
            'seller_id' => $sellerId,
            'mage_invoice' => $mageInvoice
        ];
        $block->setData($data);
        return $block->toHtml();
    }
}
