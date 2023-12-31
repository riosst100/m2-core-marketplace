<?php


namespace Lof\SellerInvoice\Controller\Adminhtml\Invoice;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;


class Exportfiles extends \Magento\Backend\App\Action
{

    protected $_layout;
    protected $layoutFactory;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf,
        \Magento\Framework\Registry $coreRegistry,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
        $this->invoiceRepository = $invoiceRepository;
        $this->_file = $file;
        $this->_fileFactory = $fileFactory;
        $this->_pdf = $pdf;
        $this->_coreRegistry          = $coreRegistry;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $id = $this->getRequest()->getParam('invoice_id');
        $sellerId = $this->getRequest()->getParam('seller_id');
        if ($id) {
            try {
                // init model and update status
                $model = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice');
                $model->load($id);
                $mageInvoice_id = $model->getId();
                $mageInvoice = $this->invoiceRepository->get($mageInvoice_id);
               
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
                $mediaRootDir = $mediaDirectory->getAbsolutePath();
                $invoice_increment = $model->getIncrementId();

            	$fileName = __('Invoice_#');
                $fileName .= $invoice_increment. '_' . $sellerId .'.pdf';
                if($model && $mageInvoice){
                    $return = $this->_pdf->generatePdf($model, $mageInvoice, $sellerId);
                    $pdf_output = isset($return['output'])?$return['output']:'';
                    return $this->_fileFactory->create(
                        $fileName,
                        $pdf_output,
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                } else {
                    $this->messageManager->addError(__("Error: Not Found Invoice!"));
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/');
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find the invoice.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }


    public function getSellerIds($invoiceId)
    {
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerModel = $objectManager->get("Lof\MarketPlace\Model\Invoice")->getCollection();
        $sellers = $sellerModel->addFieldToFilter("invoice_id",$invoiceId)->load();
        $sellerIds = [];
        foreach ($sellers as $seller) {
            array_push($sellerIds, $seller->getData("seller_id"));
        }
        return $sellerIds;
    }
}