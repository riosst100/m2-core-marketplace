<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Slider\Controller\Marketplace\Slider;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class SaveSlider extends \Magento\Framework\App\Action\Action {

    protected $session;

    protected $resultPageFactory;

    protected $sellerFactory;

    protected $_fileSystem;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    protected $_actionFlag;

    protected $_mediaDirectory;

    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        $data = []
    ) {
        parent::__construct ($context,$data);
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->_fileSystem       = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_frontendUrl      = $frontendUrl;
        $this->serializer = $serializer;
        $this->_actionFlag       = $context->getActionFlag();

    }

    /**
     * get frontend url
     * @param string $route
     * @param mixed|array $params
     * @return string
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route,$params);
    }

    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    public function execute() {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $model = $objectManager->get('Lofmp\Slider\Model\Slider');
                try {
                    if($data['image_link']){
                        $image = explode(",", $data['image_link']);
                    }else{
                        if(isset($data['photo']) && $_FILES['file']['name'][0] =='' ){
                            $image = $data['photo'];
                        }else {
                            $image = $this->uploadImage('file');
                        }
                    }

                    $image_data = [];
                    foreach($image as $key => $val){
                        $image_data[$key] = array(
                            "image_url" => $val,
                            "caption"   => ($data['caption'][$key])? $data['caption'][$key] : '',
                            "url_link"  => ($data['url_link'][$key])? $data['url_link'][$key] : '',
                            "position"  => ($data['position'][$key])? $data['position'][$key] : $key,
                        );
                    }

                    $data['image_url'] = $this->serializer->serialize($image_data);

                    unset($data['caption']);
                    unset($data['url_link']);
                    unset($data['status']);
                    unset($data['image_link']);
                    unset($data['photo']);

                    //$data['created_at'] = date("Y-m-d H:i:s");
                    if (isset($data['slider_id'])) {
                        $model->load($data['slider_id']);
                    }
                    if (isset($data['created_at'])) {
                        unset($data['created_at']);
                    }
                    $model->setData($data);
                    $model->save();
                    $this->messageManager->addSuccess(__('Update data success') );
                    if($data['is_active']){
                        $model->updateStatus($model->getId());
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
                }
                $this->_redirect ('catalog/slider' );
            }
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('catalog/slider'));
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    public function uploadImage($fieldId = 'file')
    {
        $file_path = [];
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name'][0]!='')
        {
            $total_file = $this->getRequest()->getFiles('file');
            for($i=0; $i < count($total_file); $i++){

                $uploader=$this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'file['.$i.']']);
                $path = $this->_fileSystem->getDirectoryRead(
                    DirectoryList::MEDIA)->getAbsolutePath('lofmp/slider/');
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'lofmp/slider/';

                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                    );
                $result['name'] = $result['file'];

                $file_path[$i] = $mediaFolder.$result['name'];
            }
        }
        return $file_path;
    }

}
