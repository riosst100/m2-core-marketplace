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
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Controller\Adminhtml\StoreLocator;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    protected $_moduleHelper;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\ObjectManagerInterface
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Backend\Helper\Js
     * @param \Lofmp\StoreLocator\Helper\Data $_helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone,
        \Lofmp\StoreLocator\Helper\Data $_helper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->_stdTimezone = $_stdTimezone;
        $this->_moduleHelper = $_helper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_StoreLocator::storelocator_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue(); 

        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];

        // Categories checkboxes
        if( isset($data['selectedCategories']) ) {

            if (is_array($data['selectedCategories'])){
                $data['category'] = $data['selectedCategories'];
            } else {
                unset($data['selectedCategories']);
            }
        }

        // tags checkboxes
        if( isset($data['selectedTags']) ) {
         
            if (is_array($data['selectedTags'])){
                $data['tag'] = $data['selectedTags'];
            } else {
                unset($data['selectedTags']);
            }
        }
   

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Lofmp\StoreLocator\Model\StoreLocator');

            $id = $this->getRequest()->getParam('storelocator_id');
            if ($id) {
                $model->load($id);
            }

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/storelocator/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete']) && file_exists($imagePath)){
                unlink($imagePath);
                $data['image'] = '';
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
                
                $data['image'] = $image;
            }
            $data['create_time'] = $dateTimeNow;
            
            // upload marker icon 
       
            $model->setData($data);
            try {
                $model->save();

                //Rewrite url for store locator item
                $request_path = isset($data['seo_url'])?$data['seo_url']:"";
                if($request_path) {
                    $target_path = "storelocator/index/details/id/".$model->getId();
                    $entity_id = $model->getId();
                    $stores = (isset($data['stores']) && $data['stores'])?$data['stores']:0;

                    if(!$stores || in_array(0, $stores)) {
                        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
                        $all_stores = $storeManager->getStores(false);
                        $tmp = [];
                        foreach($all_stores as $store_id1=>$store) {
                            $tmp[] = $store_id1;
                        }
                        $stores = $tmp;
                        
                    }
                    
                    if($stores) {
                        foreach($stores as $store_id) {
                            $this->_moduleHelper->generateRewriteUrl($target_path, $request_path, $entity_id, $store_id);
                        }
                    }
                }
                //End rewrite url for store locator item

                $this->messageManager->addSuccess(__('You saved this StoreLocator.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the storelocator.'));
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'image')
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );

            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/storelocator/';
            try {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                    );
                return $mediaFolder.$result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
            }
        }
        return;
    }

}