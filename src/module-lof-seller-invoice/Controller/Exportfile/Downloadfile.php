<?php


namespace Lof\SellerInvoice\Controller\Exportfile;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;


class Downloadfile extends \Lof\SellerInvoice\Controller\AbstractIndex
{

    protected $resultPageFactory;
    protected $_objectManager;
    protected $_orderRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\SellerInvoice\Model\Invoice\Pdf\Invoice $pdf,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_orderRepository  = $orderRepository;
        $this->_objectManager    = $objectManager;
        $this->_pdf              = $pdf;
        $this->layoutFactory     = $layoutFactory;
        $this->_fileFactory      = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $id       = $this->getRequest()->getParam('order_id');
        $sellerId = $this->getRequest()->getParam('seller_id');
        if ($id) {
            try {
                // init model and update status
                $model = $this->_objectManager->create('Magento\Sales\Model\Order');
                $model->load($id);
                $mageOrderId = $model->getId(); 
                $mageOrder = $this->_orderRepository->get($mageOrderId);
               
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
                $mediaRootDir = $mediaDirectory->getAbsolutePath();
                $orderIncrement = $model->getIncrementId();

                $fileName = __('Order_#');
                $fileName .= $orderIncrement. '_' . $sellerId .'.pdf';
                if($model && $mageOrder){
                    $return = $this->_pdf->generatePdf($model, $mageOrder, $sellerId);
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
}