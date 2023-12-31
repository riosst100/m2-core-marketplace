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

namespace Lof\MarketPlace\Controller\Seller;

use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Helper\WebsiteStore;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Sender;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Directory\Model\Region;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var UrlInterface
     */
    protected $urlModel;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var RegionInterfaceFactory
     */
    private $regionInterfaceFactory;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressInterfaceFactory;

    /**
     * @var Registration
     */
    private $registration;

    /**
     * @var CustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var Region
     */
    private $region;

    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var SellerHelper
     */
    protected $helperSellerData;

    /**
     * CreatePost constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $accountManagement
     * @param UrlFactory $urlFactory
     * @param Escaper $escaper
     * @param FormFactory $formFactory
     * @param RegionInterfaceFactory $regionInterfaceFactory
     * @param AddressInterfaceFactory $addressInterfaceFactory
     * @param Registration $registration
     * @param CustomerExtractor $customerExtractor
     * @param SubscriberFactory $subscriberFactory
     * @param Region $region
     * @param Address $address
     * @param Url $url
     * @param Seller $seller
     * @param StoreManagerInterface $storeManagerInterface
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Sender $sender
     * @param SellerHelper $sellerHelper
     * @param AccountRedirect $accountRedirect
     * @param WebsiteStore $websiteStoreHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $accountManagement,
        UrlFactory $urlFactory,
        Escaper $escaper,
        FormFactory $formFactory,
        RegionInterfaceFactory $regionInterfaceFactory,
        AddressInterfaceFactory $addressInterfaceFactory,
        Registration $registration,
        CustomerExtractor $customerExtractor,
        SubscriberFactory $subscriberFactory,
        Region $region,
        Address $address,
        Url $url,
        Seller $seller,
        StoreManagerInterface $storeManagerInterface,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        Sender $sender,
        SellerHelper $sellerHelper,
        AccountRedirect $accountRedirect,
        WebsiteStore $websiteStoreHelper
    ) {
        $this->helperSellerData = $sellerHelper;
        $this->_sellerHelper = $sellerHelper->getHelperData();
        $this->sender = $sender;
        $this->seller = $seller;
        $this->address = $address;
        $this->registration = $registration;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->regionInterfaceFactory = $regionInterfaceFactory;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->url = $url;
        $this->region = $region;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerExtractor = $customerExtractor;
        $this->formFactory = $formFactory;
        $this->session = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->escaper = $escaper;
        $this->urlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->accountRedirect = $accountRedirect;
        $this->websiteStoreHelper = $websiteStoreHelper;
        parent::__construct($context);
    }

    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $addressForm = $this->formFactory->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();

        $addressData = [];

        $regionDataObject = $this->regionInterfaceFactory->create();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $regionDataObject->setRegionId($value);
                    break;
                case 'region':
                    $regionDataObject->setRegion($value);
                    break;
                default:
                    $addressData [$attributeCode] = $value;
            }
        }
        $addressDataObject = $this->addressInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            $addressData,
            AddressInterface::class
        );
        $addressDataObject->setRegion($regionDataObject);

        $addressDataObject->setIsDefaultBilling($this->getRequest()->getParam(
            'default_billing',
            false
        ))->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        return $addressDataObject;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $resultRedirectFlag = 0;
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if (!$this->getRequest()->isPost()) {
            $returnUrl = $this->urlModel->getUrl('*/*/create', [
                '_secure' => true
            ]);
            $resultRedirect->setUrl($this->_redirect->error($returnUrl));
            return $resultRedirect;
        }

        $this->session->regenerateId();
        $data = $this->getRequest()->getPost();
        $data['telephone'] = str_replace(' ', '', $data['telephone']);
        if (preg_match('/^\(?\+?(\d{1,4})?\)?\(?\d{3,4}\)?[\s.-]?\d{3,4}[\s.-]?(\d{3,6})?$/', $data['telephone'])) {
            $phoneNumber = $data['telephone'];
        } else {
            $this->messageManager->addErrorMessage(__('Sorry, The phone number invalid.'));
            $this->dataPersistor->set('seller-form-validate', $data);
            $this->_redirect('lofmarketplace/seller/create');
            return;
        }
        if (!str_contains($phoneNumber, "+") && $data['country_dial_code']) {
            $countryDialCode = $this->helperSellerData->getCountryPhoneCode(strtoupper
            ($data['country_dial_code']));
            $phoneNumber = $countryDialCode . $phoneNumber;
        }
        $url = $this->getRequest()->getPost('url');
        $suffix = $this->_sellerHelper->getConfig('general_settings/url_suffix');
        if ($suffix) {
            $url = str_replace($suffix, "", $url);
            $url = str_replace(".", "-", $url);
        }
        $url = $this->helperSellerData->formatUrlKey($url);

        try {
            $address = $this->extractAddress();
            $addresses = $address === null ? [] : [
                $address
            ];

            if (!$this->helperSellerData->checkSellerUrl($url)) {
                $this->messageManager->addErrorMessage(__('URL key for specified store already exists.'));
                $this->dataPersistor->set('seller-form-validate', $data);
                $returnUrl = $this->urlModel->getUrl('*/*/create', [
                    '_secure' => true
                ]);
                $resultRedirect->setUrl($this->_redirect->error($returnUrl));
                return $resultRedirect;
            }

            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $customer->setAddresses($addresses);

            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $this->session->getBeforeAuthUrl();

            $this->checkPasswordConfirmation($password, $confirmation);
            $customer->setData('is_seller', 1);
            $customer = $this->accountManagement->createAccount($customer, $password, $redirectUrl);

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

            $this->_eventManager->dispatch('customer_register_success', [
                'account_controller' => $this,
                'customer' => $customer
            ]);

            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $this->messageManager->addComplexSuccessMessage(
                    'confirmAccountSuccessMessage',
                    [
                        'url' => $this->url->getEmailConfirmationUrl($customer->getEmail()),
                    ]
                );

                $returnUrl = $this->urlModel->getUrl('*/*/index', [
                    '_secure' => true
                ]);
                $resultRedirect->setUrl($this->_redirect->success($returnUrl));
            } else {
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->messageManager->addSuccessMessage($this->getSuccessMessage());
                $resultRedirect = $this->accountRedirect->getRedirect();
                $returnUrl = $this->urlModel->getUrl('customer/account', [
                    '_secure' => true
                ]);
                $resultRedirect->setUrl($this->_redirect->success($returnUrl));
            }
            $resultRedirectFlag = 1;
        } catch (StateException $e) {
            $this->messageManager->addComplexErrorMessage(
                'customerAlreadyExistsErrorMessage',
                [
                    'url' => $this->urlModel->getUrl('customer/account/forgotpassword'),
                ]
            );
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t save the customer.'));
        }
        if ($resultRedirectFlag == 0) {
            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
            $defaultUrl = $this->urlModel->getUrl('*/*/create', [
                '_secure' => true
            ]);
            $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        }


        $group = $this->getRequest()->getPost('group');
        $enableGroupSeller = $this->_sellerHelper->getConfig('group_seller/enable_group_seller');
        $enableSellerMembership = $this->_sellerHelper->isEnableModule('Lofmp_SellerMembership');
        if (!$enableGroupSeller || $enableSellerMembership) {
            $group = (int)$this->_sellerHelper->getConfig('seller_settings/default_seller_group');
        }
        $layout = '2columns-left';
        $stores = [];
        $stores[] = $this->_sellerHelper->getCurrentStoreId();
        if ($this->_sellerHelper->getConfig('general_settings/enable_all_store')) {
            $newStores = $this->websiteStoreHelper->getWebsteStoreIds();
            if ($newStores && count($newStores) > 0) {
                $stores = array_merge($newStores, $stores);
            }
        }
        $customerConfirmEmail = $this->_sellerHelper->getConfigCustomer('create_account/confirm');
        $sellerApproval = $this->_sellerHelper->getConfig('general_settings/seller_approval');
        if ($sellerApproval) {
            $status = 2;
            $redirectSellerUrl = 'lofmarketplace/seller/becomeseller/approval/2';
        } else {
            $status = 1;
            $redirectSellerUrl = 'marketplace/catalog/dashboard';
        }
        $customerSession = $this->session;
        $street = '';
        $country = $this->_sellerHelper->getCountryname($data['country_id']);
        if (empty($data['region'])) {
            $region = $this->region->load($data['region_id']);
            $data['region'] = $region->getData('name');
        }
        foreach ($data['street'] as $_street) {
            $street .= ' ' . $_street;
        }

        $sellerModel = $this->seller;

        if ($customerConfirmEmail) {
            $customerId = $customer->getId();
            $customerEmail = $customer->getEmail();
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();

            try {
                $sellerModel->setCity($data['city'])
                    ->setCompany($data['company'])
                    ->setTelephone($phoneNumber)
                    ->setContactNumber($phoneNumber)
                    ->setAddress($street)
                    ->setRegion($data['region'])
                    ->setRegionId($data['region_id'])
                    ->setPostcode($data['postcode'])
                    ->setCountry($country)
                    ->setCountryId($data['country_id'])
                    ->setName($customerName)
                    ->setEmail($customerEmail)
                    ->setStatus($status)
                    ->setGroupId($group)
                    ->setCustomerId($customerId)
                    ->setStores($stores)
                    ->setUrlKey($url)
                    ->setShopTitle($data['shop_title'])
                    ->setPageLayout($layout)
                    ->save();
                $this->_eventManager->dispatch('seller_register_success', [
                    'account_controller' => $this,
                    'seller' => $sellerModel
                ]);
                if ($this->_sellerHelper->getConfig('email_settings/enable_send_email')) {
                    $data = [];
                    $data['name'] = $customerName;
                    $data['email'] = $customerEmail;
                    $data['group'] = $group;
                    $data['url'] = $sellerModel->getUrl();
                    $this->sender->noticeAdmin($data);
                    $this->sender->thankForRegisterSeller($data);
                }
                $this->_redirect('lofmarketplace/seller/login/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('lofmarketplace/seller/login/');
            }
        }
        else
        {
            if ($customerSession->isLoggedIn()) {
                $customerId = $customerSession->getId();
                $customerObject = $customerSession->getCustomer();
                $customerEmail = $customerObject->getEmail();
                $customerName = $customerObject->getName();

                try {
                    $sellerModel->setCity($data['city'])
                        ->setCompany($data['company'])
                        ->setTelephone($phoneNumber)
                        ->setContactNumber($phoneNumber)
                        ->setAddress($street)
                        ->setRegion($data['region'])
                        ->setRegionId($data['region_id'])
                        ->setPostcode($data['postcode'])
                        ->setCountry($country)
                        ->setCountryId($data['country_id'])
                        ->setName($customerName)
                        ->setEmail($customerEmail)
                        ->setStatus($status)
                        ->setGroupId($group)
                        ->setCustomerId($customerId)
                        ->setStores($stores)
                        ->setUrlKey($url)
                        ->setShopTitle($data['shop_title'])
                        ->setPageLayout($layout)
                        ->save();
                    $this->_eventManager->dispatch('seller_register_success', [
                        'account_controller' => $this,
                        'seller' => $sellerModel,
                        'customer' => $customerObject,
                        'url' => $url,
                        'is_become_seller' => true
                    ]);
                    if ($this->_sellerHelper->getConfig('email_settings/enable_send_email')) {
                        $data = [];
                        $data['name'] = $customerName;
                        $data['email'] = $customerEmail;
                        $data['group'] = $group;
                        $data['url'] = $sellerModel->getUrl();
                        $this->sender->noticeAdmin($data);
                        if ($sellerApproval) {
                            $this->sender->thankForRegisterSeller($data);
                        } else {
                            $this->sender->approveSeller($data);
                        }
                    }
                    $this->_redirect($redirectSellerUrl);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('lofmarketplace/seller/becomeseller');
                }
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('lofmarketplace/seller/login/');
                return $resultRedirect;
            }
        }
        return null;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->address->isVatValidationEnabled()) {
            if ($this->address->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                $message = __(
                // phpcs:disable Generic.Files.LineLength.TooLong
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->urlModel->getUrl('customer/address/edit')
                );
            } else {
                $message = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->urlModel->getUrl('customer/address/edit')
                );
            }
        } else {
            $message = __(
                'Thank you for registering with %1.',
                $this->storeManagerInterface->getStore()->getFrontendName()
            );
        }
        return $message;
    }
}
