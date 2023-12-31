<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category  Landofcoder
 * @package   Lof_Setup
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Setup\Controller\Adminhtml\Export;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\Setup\Helper\Export
     */
    protected $_exportHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Backend\App\Action\Context        $context           
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory 
     * @param \Lof\Setup\Helper\Export                   $exportHelper        
     * @param \Magento\Framework\Filesystem              $filesystem        
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Setup\Helper\Export $exportHelper,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_exportHelper     = $exportHelper;
        $this->_filesystem       = $filesystem;
    }
     /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Setup::export');

    }//end _isAllowed()
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $content = [];
        $params['isdowload'] = true;

        if($params) {
            if(isset($params['modules'])) {
                $content = array_merge($content, $this->_exportHelper->exportModules($params));
            }
            if(isset($params['cmspages'])) {
                $content = array_merge($content, $this->_exportHelper->exportCmsPages($params));
            }
            if(isset($params['cmsblocks'])) {
                $content = array_merge($content, $this->_exportHelper->exportStaticBlocks($params));
            }
            if(isset($params['widgets'])) {
                $content = array_merge($content, $this->_exportHelper->exportWidgets($params));
            }
        }

        $fileName = trim($params['file_name']).$params['file_extension'];
        $fileName = str_replace(" ", "-", $fileName);
        if(!empty($content) && isset($params['isdowload']) && $params['isdowload'] ) {
            $content['created_at'] = date("m/d/Y h:i:s a");
            $content = \Zend_Json::encode($content);
            $this->_sendUploadResponse($fileName, $content);
        }

        if(!empty($content) && isset($params['isdowload']) && !$params['isdowload'] && isset($params['folder'])) {
            $folder = $params['folder'];
            $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP);
            $file = null;
            $content['created_at'] = date("m/d/Y h:i:s a");
            $content = \Zend_Json::encode($content);
            $filePath = "design/frontend/{$folder}/backup/".$fileName;
            try{
                $dir->writeFile($filePath, $content);
                $backupFilePath = $dir->getAbsolutePath($filePath);
                $this->messageManager->addSuccess(__('Successfully exported to file "%1"', $backupFilePath));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not save export file "%1".<br/>"%2"', $filePath, $e->getMessage()));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $this->_response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', strlen($content))
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true);
        $this->_response->setBody($content);
        $this->_response->sendResponse();
        return $this->_response;
    }
}
