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

namespace Lof\MarketPlace\Controller\Adminhtml\Seller;

use Exception;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Model\Seller;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\Product\Url;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
{
    const XML_PATH_EMAIL_SENDER = 'lofmarketplace/email_settings/sender_email_identity';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Lof_MarketPlace::seller_save';

    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CustomerFactory
     */
    private $customer;

    /**
     * @var Js
     */
    private $jsHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $store;

    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var UrlInterface
     */
    private $_urlInterface;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $_transportBuilder;

    /**
     * @var Escaper
     */
    private $_escaper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var SellerHelper
     */
    protected $sellerHelper;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $store
     * @param Js $jsHelper
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customer
     * @param UrlInterface $urlInterface
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param Url $url
     * @param Seller $seller
     * @param Escaper $escaper
     * @param Data $helper
     * @param SellerHelper $sellerHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        StoreManagerInterface $store,
        Js $jsHelper,
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customer,
        UrlInterface $urlInterface,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        Session $session,
        Url $url,
        Seller $seller,
        Escaper $escaper,
        Data $helper,
        SellerHelper $sellerHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->helper = $helper;
        $this->customer = $customer;
        $this->store = $store;
        $this->seller = $seller;
        $this->_escaper = $escaper;
        $this->session = $session;
        $this->url = $url;
        $this->_urlInterface = $urlInterface;
        $this->_fileSystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->jsHelper = $jsHelper;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->inlineTranslation = $inlineTranslation;
        $this->sellerHelper = $sellerHelper;

        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            !isset($data['tw_active']) ? $data['tw_active'] = 0 : $data['tw_active'] = 1;
            !isset($data['fb_active']) ? $data['fb_active'] = 0 : $data['fb_active'] = 1;
            !isset($data['gplus_active']) ? $data['gplus_active'] = 0 : $data['gplus_active'] = 1;
            !isset($data['youtube_active']) ? $data['youtube_active'] = 0 : $data['youtube_active'] = 1;
            !isset($data['vimeo_active']) ? $data['vimeo_active'] = 0 : $data['vimeo_active'] = 1;
            !isset($data['instagram_active']) ? $data['instagram_active'] = 0 : $data['instagram_active'] = 1;
            !isset($data['linkedin_active']) ? $data['linkedin_active'] = 0 : $data['linkedin_active'] = 1;
            !isset($data['pinterest_active']) ? $data['pinterest_active'] = 0 : $data['pinterest_active'] = 1;

            $model = $this->seller;

            $id = $this->getRequest()->getParam('seller_id');
            if ($id) {
                $model->load($id);
            }

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
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
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if (isset($data['thumbnail']['delete']) && file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
                $data['thumbnail'] = '';
            }

            if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
                unset($data['thumbnail']);
            }

            if ($thumbnail = $this->uploadImage('thumbnail')) {
                $data['thumbnail'] = $thumbnail;
            }

            if ($data['url_key'] == '') {
                $data['url_key'] = $data['name'];
            }

            $url_key = $data['url_key'];
            $suffix = $this->helper->getConfig('general_settings/url_suffix');
            if ($suffix) {
                $url_key = str_replace($suffix, "", $url_key);
                $url_key = str_replace(".", "-", $url_key);
            }
            $url_key = $this->sellerHelper->formatUrlKey($url_key);
            $isExistUrl = !$this->sellerHelper->checkSellerUrl($url_key);
            if ( $isExistUrl && (!$model->getId() || ($model->getId() && $url_key != $model->getUrlKey()))) {
                $this->messageManager->addErrorMessage(__('URL key for specified store already exists.'));
                if ($id) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['seller_id' => $this->getRequest()->getParam('seller_id')]
                    );
                } else {
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $data['url_key'] = $url_key;

            $this->_eventManager->dispatch('lof_marketplace_urlkey', ['data' => $data]);

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if (!empty($links) && isset($links['related'])) {
                $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                $data['products'] = $products;
            }

            if ($data['customer_id'] != '0') {
                $customer = $this->helper->getCustomerById($data['customer_id']);
                $data['email'] = $customer->getData('email');
            } else {
                if (!(filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
                    $this->messageManager->addErrorMessage(__('Please enter the correct email.'));
                    if ($id) {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            ['seller_id' => $this->getRequest()->getParam('seller_id')]
                        );
                    } else {
                        return $resultRedirect->setPath('*/*/');
                    }
                }

                $customer = $this->helper->getCustomerByEmail($data['email']);
                if ($customer->getData()) {
                    $data['customer_id'] = $customer->getEntityId();
                } else {
                    try {
                        $customer = $this->customer->create();
                        $customer->setEmail($data['email']);
                        $customer->setFirstname($data['name']);
                        $customer->setLastname(' ');
                        $password = $this->randomPassword();
                        $customer->setPassword($password);
                        /** @var CustomerRepositoryInterface $customerRepository */
                        $customer->save();
                        $data['customer_id'] = $this->customer->create()->getCollection()
                            ->addFieldToFilter('email', $data['email'])
                            ->getFirstItem()->getData('entity_id');
                        $template = 'lofmarketplace_email_settings_create_seller_template';
                        $url = $this->_urlInterface->getBaseUrl() . 'marketplace/' . $data['url_key'];
                        $dataEmail = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => $password,
                            'url' => $url
                        ];
                        $this->sendEmail($dataEmail, $data['email'], $template);
                        // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                    } catch (\Exception $e) {
                        //
                    }
                }
            }

            $existedSellerId = $model->getCollection()
                ->addFieldToFilter('email', $data['email'])->getFirstItem()->getData('seller_id');

            if ($id && $existedSellerId && $existedSellerId != $id) {
                $this->messageManager->addErrorMessage(
                    __('This email has already been registered for another seller.')
                );
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['seller_id' => $this->getRequest()->getParam('seller_id')]
                );
            } elseif ($existedSellerId && !$id) {
                $this->messageManager->addErrorMessage(
                    __('This email has already been registered for another seller.')
                );
                return $resultRedirect->setPath('*/*/new');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->_eventManager->dispatch('lof_marketplace_urlkey', ['data' => $model->getData()]);
                $this->_eventManager->dispatch('seller_register_success', [
                    'account_controller' => $this,
                    'seller' => $model
                ]);
                $this->messageManager->addSuccessMessage(__('You saved this seller.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['seller_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the seller.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['seller_id' => $this->getRequest()->getParam('seller_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param string $fieldId
     * @return Redirect|string|void
     */
    public function uploadImage($fieldId = 'image')
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name'] != '') {
            $uploader = $this->_objectManager->create(
                \Magento\Framework\File\Uploader::class,
                ['fileId' => $fieldId]
            );

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_fileSystem
                ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/seller/';

            try {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath($mediaFolder)
                );

                $result['name'] = str_replace(' ', '_', $result['name']);

                return $mediaFolder . $result['name'];
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['seller_id' => $this->getRequest()->getParam('seller_id')]
                );
            }
        }
    }

    /**
     * @return string
     */
    public function randomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $password = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $password[] = $alphabet[$n];
        }

        return implode($password);
    }

    /**
     * @param $data
     * @param $emailRecipient
     * @param $template
     */
    public function sendEmail($data, $emailRecipient, $template)
    {
        try {
            $storeScope = ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $senderData = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);
            if (!empty($senderData)) {
                $senderDataName = $this->scopeConfig->getValue(
                    'trans_email/ident_' . $senderData . '/name',
                    $storeScope
                );
                $senderDataEmails = $this->scopeConfig->getValue(
                    'trans_email/ident_' . $senderData . '/email',
                    $storeScope
                );
            } else {
                $senderDataName = $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope);
                $senderDataEmails = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
            }
            $sender = [
                'name' => $this->_escaper->escapeHtml($senderDataName),
                'email' => $this->_escaper->escapeHtml($senderDataEmails),
            ];

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo($emailRecipient)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );
        }
    }
}
