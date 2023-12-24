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
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SmtpEmail\Controller\Adminhtml\Emaillog;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
class Exportcsv extends \Lof\SmtpEmail\Controller\Adminhtml\Emaillog
{
    protected $_layout;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
    }


     public function execute()
    {
         /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // init model and delete
            $collection = $this->_objectManager->create('Lof\SmtpEmail\Model\Emaillog')->getCollection();
            $params = [];
            foreach ($collection as $key => $model) {
                $params[] = $model->getData();
             }
             $name = 'list_email';
            $file = 'export/smtpemail/'. $name . '.csv';

            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            $headers = array('Recipient Email','Sender Email','Subject','Body','Status','Created At');
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $rowData = $fields;
                foreach($row as $v){
                    $rowData['recipient_email'] = $row['recipient_email'];
                    $rowData['sender_email'] = $row['sender_email'];
                    $rowData['subject'] = $row['subject'];
                    $rowData['body'] = strip_tags($row['body']);
                    $rowData['status'] = $row['status'];
                    $rowData['created_at'] = $row['created_at'];

                }
                $stream->writeCsv($rowData);
            }
            $stream->unlock();
            $stream->close();
            $file = [
                'type' => 'filename',
                'value' => $file,
                'rm' => true  // can delete file after use
            ];
             // display success message
            $this->messageManager->addSuccess(__('You export email to csv success.'));
            return $this->fileFactory->create($name . '.csv', $file, 'var');

        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // go back to edit form
            return $resultRedirect->setPath('*/*/index');
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a emaillog to exportcsv.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

        /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_SmtpEmail::emaillog_exportcsv');
    }
}
