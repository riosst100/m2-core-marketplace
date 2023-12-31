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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditProfileSave extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

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
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * EditProfileSave constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Psr\Log\LoggerInterface $_logger
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $_logger
    ) {
        parent::__construct($context);

        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->_logger = $_logger;
        $this->_actionFlag = $context->getActionFlag();
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $data = $this->getRequest()->getPostValue();

            if ($data) {
                $id = $this->getRequest()->getParam('seller_id');
                $sellerModel = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)->load($id);

                try {
                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                        ->getDirectoryRead(DirectoryList::MEDIA);

                    // phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
                    $imagePath = $mediaDirectory->getAbsolutePath($_FILES['image']['name']);
                    // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                    if (isset($data['image']['delete']) && file_exists($imagePath)) {
                        unlink($imagePath);
                        $data['image'] = '';
                    }

                    if (isset($data['image']) && is_array($data['image'])) {
                        unset($data['image']);
                    }

                    if ($image = $this->uploadImage('image')) {

                        $data['image'] = $image;
                    }

                    // Delete, Upload Thumbnail
                    $thumbnailPath = $mediaDirectory->getAbsolutePath($_FILES['thumpnail']['name']);
                    if (isset($data['thumpnail']['delete']) && file_exists($thumbnailPath)) {
                        unlink($thumbnailPath);
                        $data['thumpnail'] = '';
                    }

                    if (isset($data['thumpnail']) && is_array($data['thumpnail'])) {
                        unset($data['thumpnail']);
                    }

                    if ($thumbnail = $this->uploadImage('thumpnail')) {
                        $data['thumpnail'] = $thumbnail;
                    }

                    $sellerModel->setData($data);
                    $sellerModel->save();
                    $this->_redirect('marketplace/marketplace/catalog/seller/profile');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the seller.'));
                }
            }
        } elseif ($customerSession->isLoggedIn() && ($status == 0 || $status == 2)) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param string $fieldId
     * @return bool|\Magento\Backend\Model\View\Result\Redirect|string
     */
    public function uploadImage($fieldId = 'image')
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name'] != '') {
            $uploader = $this->_objectManager->create(
                \Magento\Framework\File\Uploader::class,
                ['fileId' => $fieldId]
            );

            $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                'catalog/category/'
            );

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/seller/';

            try {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder));

                return $mediaFolder . $result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['seller_id' => $this->getRequest()->getParam('seller_id')]
                );
            }
        }

        return false;
    }
}
