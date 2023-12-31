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

namespace Lof\MarketPlace\Controller\Marketplace\Saveprofile;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Lof\MarketPlace\Helper\WebsiteStore;
use Lof\MarketPlace\Helper\Data;
use Magento\Framework\Url;
use Magento\Customer\Model\CustomerFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends \Magento\Customer\Controller\AbstractAccount
{
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
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var Url
     */
    private $_frontendUrl;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Psr\Log\LoggerInterface $_logger
     * @param WebsiteStore $websiteStoreHelper
     * @param Data $sellerHelper
     * @param Url $frontendUrl
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $_logger,
        WebsiteStore $websiteStoreHelper,
        Data $sellerHelper,
        Url $frontendUrl,
        CustomerFactory $customerFactory
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        $this->_logger = $_logger;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->_sellerHelper = $sellerHelper;
        $this->_frontendUrl = $frontendUrl;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $sellerId = $seller->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $data = $this->getRequest()->getPostValue();
            $data['telephone'] = str_replace(' ', '', $data['telephone']);
            if (preg_match('/^\(?\+?(\d{1,4})?\)?\(?\d{3,4}\)?[\s.-]?\d{3,4}[\s.-]?(\d{3,6})?$/',
                $data['telephone'])) {
                $data['contact_number'] = $data['telephone'];
            } else {
                $this->messageManager->addErrorMessage(__('Sorry, The phone number invalid.'));
                $this->_redirect('catalog/seller/index');
                return;
            }
            !isset($data['tw_active']) ? $data['tw_active'] = 0 : $data['tw_active'] = 1;
            !isset($data['fb_active']) ? $data['fb_active'] = 0 : $data['fb_active'] = 1;
            !isset($data['gplus_active']) ? $data['gplus_active'] = 0 : $data['gplus_active'] = 1;
            !isset($data['youtube_active']) ? $data['youtube_active'] = 0 : $data['youtube_active'] = 1;
            !isset($data['vimeo_active']) ? $data['vimeo_active'] = 0 : $data['vimeo_active'] = 1;
            !isset($data['instagram_active']) ? $data['instagram_active'] = 0 : $data['instagram_active'] = 1;
            !isset($data['linkedin_active']) ? $data['linkedin_active'] = 0 : $data['linkedin_active'] = 1;
            !isset($data['pinterest_active']) ? $data['pinterest_active'] = 0 : $data['pinterest_active'] = 1;

            $sellerModel = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)->load($sellerId);
            $sellerData = $sellerModel->getData();
            $data['seller_id'] = $sellerId;

            try {
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'lof/seller/';
                $path = $mediaDirectory->getAbsolutePath($mediaFolder);
                // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                if (isset($data['image']['delete']) && file_exists($path)) {
                    unlink($path);
                    $data['image'] = '';
                }

                if (isset($data['image']) && is_array($data['image'])) {
                    unset($data['image']);
                }

                if ($image = $this->uploadImage('image')) {
                    $data['image'] = $image;
                }

                if (isset($data['thumbnail']['delete']) && file_exists($path)) {
                    unlink($path);
                    $data['thumbnail'] = '';
                }

                if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
                    unset($data['thumbnail']);
                }

                if ($thumbnail = $this->uploadImage('thumbnail')) {
                    $data['thumbnail'] = $thumbnail;
                }

                $sellerStores = $sellerModel->getStoreId();
                $data['store_id'] = is_array($sellerStores) ? $sellerStores[0] : (int)$sellerStores;
                if ($this->_sellerHelper->getConfig('general_settings/enable_all_store')) {
                    $newStores = $this->websiteStoreHelper->getWebsteStoreIds();
                    if ($newStores && count($newStores) > 0) {
                        $sellerStores = is_array($sellerStores) ? $sellerStores : [$sellerStores];
                        $sellerStores = array_merge($newStores, $sellerStores);
                    }
                }
                $data['stores'] = $sellerStores;
                if (isset($data['email'])) {
                    // Don't change email.
                    unset($data['email']);
                }

                $taxvat = isset($data['taxvat']) ? $data['taxvat'] : null;

                $this->_eventManager->dispatch(
                    'marketplace_seller_start_saveprofile',
                    ['account_controller' => $this, 'data' => $data, 'seller_id' => $sellerId, 'seller' => $sellerModel]
                );

                foreach ($data as $k => $v) {
                    $sellerData[$k] = $data[$k];
                }

                $sellerModel->setData($sellerData);
                $sellerModel->save();

                $customer = $this->customerFactory->create()->load((int)$customerId);

                if ($taxvat != null) {
                    $taxvat = $taxvat ? strip_tags($taxvat) : "";

                    $customer->setTaxvat($taxvat);
                    $customer->save();
                }

                $this->_eventManager->dispatch('seller_update_profile', [
                    'account_controller' => $this,
                    'customer' => $customer,
                    'seller' => $sellerModel
                ]);

                $this->messageManager->addSuccessMessage(__('Save Profile Success'));
                $this->_redirect('catalog/seller/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('catalog/seller/index');
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('catalog/seller/index');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the seller %1.', $e->getMessage()));
                $this->_redirect('catalog/seller/index');
            }

        } elseif ($customerSession->isLoggedIn() && ($status == 0 || $status == 2)) {
            $this->_redirect('lofmarketplace/seller/becomeseller');
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirect($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param string $fieldId
     * @return string|void
     */
    public function uploadImage($fieldId = 'image')
    {
        // phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name'] != '') {
            $uploader = $this->_objectManager->create(\Magento\Framework\File\Uploader::class, ['fileId' => $fieldId]);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/seller/';

            try {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder));
                $result['name'] = str_replace(' ', '_', $result['name']);
                if (preg_match('/[^\00-\255]+/u', $result['name']) || preg_match('/[^a-zA-Z0-9]/', $result['name'])) {
                    return $mediaFolder . $result['file'];
                }
                return $mediaFolder . $result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('catalog/seller/index/status/1');
            }
        }
    }
}
