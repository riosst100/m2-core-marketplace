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
namespace Lof\Setup\Controller\Adminhtml\Import;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Lof\Setup\Helper\Import
     */
    protected $_lofImport;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $_configResource;

    /**
     * @param \Magento\Backend\App\Action\Context                          $context
     * @param \Magento\Framework\View\Result\PageFactory                   $resultPageFactory
     * @param \Lof\Setup\Helper\Import                                     $lofImport
     * @param \Magento\Framework\Filesystem                                $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection                    $resource
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Setup\Helper\Import $lofImport,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_filesystem       = $filesystem;
        $this->_storeManager     = $storeManager;
        $this->_scopeConfig      = $scopeConfig;
        $this->_configResource   = $configResource;
        $this->_resource         = $resource;
        $this->_lofImport        = $lofImport;
        $this->mediaConfig       = $mediaConfig;
    }
     /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Setup::import');

    }//end _isAllowed()
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'data_import_file']
            );

            $fileContent = '';
            if($uploader) {
                $tmpDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::TMP);
                $savePath     = $tmpDirectory->getAbsolutePath('lof/import');
                $uploader->setAllowRenameFiles(true);
                $result       = $uploader->save($savePath);
                $fileContent  = file_get_contents($tmpDirectory->getAbsolutePath('lof/import/' . $result['file']));
            }else if(isset($data['folder'])){
                $folder = $data['folder'];
                if(isset($data[$folder])) {
                    $filePath = $data[$folder];
                }
                if($filePath!='') {
                    $fileContent = file_get_contents($filePath);
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Can't import data<br/> %1", $e->getMessage()));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $importData = \Laminas\Json\Json::decode($fileContent);

        $overwrite = false;
        if($data['overwrite_blocks']) {
            $overwrite = true;
        }

        $store = $this->_storeManager->getStore($data['store_id']);
        $connection = $this->_resource->getConnection();
        if(!empty($importData)) {
            try{
                foreach ($importData as $_module) {
                    if(isset($_module['tables'])) {
                        $tables = $_module['tables'];
                        foreach ($tables as $tablename => $rows) {
                            $exist = false;
                            $table_name = $this->_resource->getTableName($tablename);
                            $ignore_columns = [];
                            if($table_name == "cms_block"){
                                $ignore_columns[] = 'block_id';
                            }
                            if($table_name == "cms_page"){
                                $ignore_columns[] = 'page_id';
                            }
                            if(false !== strpos($table_name, "lof_") || false !== strpos($table_name, "lofmp_")){
                                $connection->query("SET FOREIGN_KEY_CHECKS=0;");

                                $check_query = "SHOW TABLES LIKE '".$table_name."'";
                                $total = $connection->fetchAll($check_query);
                                if(count($total) > 0) {
                                    $exist = true;
                                }

                                if(!$overwrite && $exist) {
                                    //$connection->query("TRUNCATE `".$table_name."`");
                                }
                            }
                            if($overwrite) {
                                // Overide CMS Page, Static Block
                                if($table_name == 'cms_page_store' ) {
                                    //$connection->query(" DELETE FROM ".$table_name." WHERE page_id = ".$rows['page_id']);
                                }
                                if($table_name == 'cms_block_store' ) {
                                    //$connection->query(" DELETE FROM ".$table_name." WHERE block_id = ".$rows['block_id']);
                                }
                            }
                            foreach ($rows as $row) {
                                if($exist) {
                                    $where = '';
                                    $query_data = $this->_lofImport->buildQueryImport($row, $table_name, $overwrite, $data['store_id'], $ignore_columns);
                                    $connection->query($query_data[0].$where, $query_data[1]);
                                }

                            }
                        }
                        $connection->query("SET FOREIGN_KEY_CHECKS=1;");
                    }
                    if(isset($_module['system_configs'])) {
                        foreach ($_module['system_configs'] as $_config) {
                            if(isset($_config['key'])) {
                                $result = $this->_scopeConfig->getValue(
                                    $_config['key'],
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                    $store
                                );
                                if($result != $_config['value']) {
                                    if((int)$data['store_id'] == 0) {
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "default", (int)$data['store_id']);
                                    }else{
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "stores", (int)$data['store_id']);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->messageManager->addSuccess(__("Import successfully"));
            }catch(\Exception $e){
                $this->messageManager->addSuccess(__("Can't import data<br/> %1", $e->getMessage()));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
