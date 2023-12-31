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

use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\WebsiteStore;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Sender;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Lof\MarketPlace\Model\ResourceModel\Group\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Saveseller extends \Magento\Framework\App\Action\Action
{
    /**
     * Form code for data extractor
     */
    const FORM_DATA_EXTRACTOR_CODE = 'customer_account_edit';

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var Collection
     */
    private $groupCollection;

    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var Session
     */
    private $sesson;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var SellerHelper
     */
    protected $helperSellerData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $region;

    /**
     * @var Mapper
     */
    protected $customerMapper;

    /**
     * @var \Magento\Customer\Model\CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Context $context
     * @param Session $session
     * @param Seller $seller
     * @param Sender $sender
     * @param Collection $groupCollection
     * @param SellerHelper $sellerHelper
     * @param WebsiteStore $websiteStoreHelper
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param RequestInterface $request
     * @param Mapper $customerMapper
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     * @param \Magento\Directory\Model\Region $region
     */
    public function __construct(
        Context $context,
        Session $session,
        Seller $seller,
        Sender $sender,
        Collection $groupCollection,
        SellerHelper $sellerHelper,
        WebsiteStore $websiteStoreHelper,
        DataPersistorInterface $dataPersistor,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor,
        \Magento\Directory\Model\Region $region
    ) {
        parent::__construct($context);
        $this->sesson = $session;
        $this->seller = $seller;
        $this->customerMapper = $customerMapper;
        $this->customerExtractor = $customerExtractor;
        $this->_request = $request;
        $this->groupCollection = $groupCollection;
        $this->helperSellerData = $sellerHelper;
        $this->customerRepository = $customerRepository;
        $this->_sellerHelper = $sellerHelper->getHelperData();
        $this->sender = $sender;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->dataPersistor = $dataPersistor;
        $this->region = $region;
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return CustomerInterface
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Get Customer Mapper instance
     *
     * @return Mapper
     *
     * @deprecated 100.1.3
     */
    private function getCustomerMapper()
    {
        if ($this->customerMapper === null) {
            $this->customerMapper = \Magento\Framework\App\ObjectManager::getInstance()->get(Mapper::class);
        }
        return $this->customerMapper;
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param RequestInterface $inputData
     * @param CustomerInterface $currentCustomerData
     * @return CustomerInterface
     */
    private function populateNewCustomerDataObject(
        \Magento\Framework\App\RequestInterface $inputData,
        \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
    ) {
        $attributeValues = $this->getCustomerMapper()->toFlatArray($currentCustomerData);
        $customerDto = $this->customerExtractor->extract(
            self::FORM_DATA_EXTRACTOR_CODE,
            $inputData,
            $attributeValues
        );
        $customerDto->setId($currentCustomerData->getId());
        if (!$customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (!$inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }

        return $customerDto;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $approvedConditions = $this->getRequest()->getPost('privacy_policy');
        $url = $this->getRequest()->getPost('url');
        $group = $this->getRequest()->getPost('group');
        $suffix = $this->_sellerHelper->getConfig('general_settings/url_suffix');
        if ($suffix) {
            $url = str_replace($suffix, "", $url);
            $url = str_replace(".", "-", $url);
        }
        $url = $this->helperSellerData->formatUrlKey($url);
        if (!$this->helperSellerData->checkSellerUrl($url)) {
            $this->messageManager->addErrorMessage(__('URL key for specified store already exists.'));
            $this->_redirect('lofmarketplace/seller/becomeseller');
            return;
        }
        $enableGroupSeller = $this->_sellerHelper->getConfig('group_seller/enable_group_seller');
        $enableSellerMembership = $this->_sellerHelper->isEnableModule('Lofmp_SellerMembership');
        if (!$enableGroupSeller || $enableSellerMembership) {
            $group = (int)$this->_sellerHelper->getConfig('seller_settings/default_seller_group');
        }
        $existGroup = $this->groupCollection
            ->addFieldToFilter('group_id', $group)
            ->addFieldToFilter('status', '1');
        if (!$existGroup->getData()) {
            $this->messageManager->addErrorMessage(__('Sorry. You can\'t create seller in this store.'));
            $this->_redirect('lofmarketplace/seller/becomeseller');
            return;
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
        $customerSession = $this->sesson;
        if ($customerSession->isLoggedIn()) {
            if ($approvedConditions) {
                $customerId = $customerSession->getId();
                $customerObject = $customerSession->getCustomer();
                $customerEmail = $customerObject->getEmail();
                $customerName = $customerObject->getName();
                $sellerApproval = $this->_sellerHelper->getConfig('general_settings/seller_approval');
                if ($customerObject->getAddresses()) {
                    $country = $customerObject->getAddresses()[$customerObject->getDefaultBilling()]->getCountryId();
                    if (!$this->checkCountry($country)) {
                        $this->messageManager->addErrorMessage(
                            __('Sorry. The store does not support to create sellers in your country')
                        );
                        $this->_redirect('lofmarketplace/seller/becomeseller');
                        return;
                    }
                }

                if ($sellerApproval) {
                    $status = 2;
                    $redirectUrl = 'lofmarketplace/seller/becomeseller/approval/2';
                } else {
                    $status = 1;
                    $redirectUrl = 'marketplace/catalog/dashboard';
                }
                $sellerModel = $this->seller;
                try {
                    $advancedBecomeseller = $this->_sellerHelper
                        ->getConfig('general_settings/customer_become_seller');
                    if ($advancedBecomeseller) {
                        $data = $this->getRequest()->getPost();
                        $data['telephone'] = str_replace(' ', '', $data['telephone']);
                        if (preg_match('/^\(?\+?(\d{1,4})?\)?\(?\d{3,4}\)?[\s.-]?\d{3,4}[\s.-]?(\d{3,6})?$/', $data['telephone'])) {
                            $phoneNumber = $data['telephone'];
                        } else {
                            $this->messageManager->addErrorMessage(
                                __('Sorry, The phone number invalid.')
                            );
                            $this->dataPersistor->set('buyer-seller', $data);
                            $this->_redirect('lofmarketplace/seller/becomeseller');
                            return;
                        }
                        if (!$this->helperSellerData->checkSellerUrl($url)) {
                            $this->messageManager->addErrorMessage(__('URL key for specified store already exists.'));
                            $this->dataPersistor->set('buyer-seller', $data);
                            $this->_redirect('lofmarketplace/seller/becomeseller');
                            return;
                        }
                        if (!str_contains($phoneNumber, "+") && $data['country_dial_code']) {
                            $countryDialCode = $this->helperSellerData->getCountryPhoneCode(strtoupper
                            ($data['country_dial_code']));
                            $phoneNumber = $countryDialCode . $phoneNumber;
                        }
                        $street = '';
                        $country = $this->_sellerHelper->getCountryname($data['country_id']);
                        if (empty($data['region'])) {
                            $region = $this->region->load($data['region_id']);
                            $data['region'] = $region->getData('name');
                        }
                        foreach ($data['street'] as $_street) {
                            $street .= ' ' . $_street;
                        }
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

                        $currentCustomerDataObject = $this->getCustomerDataObject($customerId);
                        $customerCandidateDataObject = $this->populateNewCustomerDataObject(
                            $this->_request,
                            $currentCustomerDataObject
                        );
                        $this->customerRepository->save($customerCandidateDataObject);
                    } else {
                        $sellerModel->setName($customerName)
                            ->setEmail($customerEmail)
                            ->setStatus($status)
                            ->setGroupId($group)
                            ->setCustomerId($customerId)
                            ->setStores($stores)
                            ->setUrlKey($url)
                            ->setPageLayout($layout)
                            ->save();
                    }
                    $this->_eventManager->dispatch(
                        'seller_register_success',
                        ['account_controller' => $this, 'seller' => $sellerModel]
                    );
                    $this->_eventManager->dispatch(
                        'controller_action_seller_save_entity_after',
                        ['controller' => $this, 'data' => $customerObject->getData(), 'seller' => $sellerModel]
                    );
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
                    $this->_redirect($redirectUrl);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('lofmarketplace/seller/becomeseller');
                }
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('account/login/');
            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @param $country
     * @return bool
     */
    public function checkCountry($country)
    {
        $availableCountries = $this->_sellerHelper->getConfig('available_countries/available_countries');
        $enableAvailableCountries = $this->_sellerHelper
            ->getConfig('available_countries/enable_available_countries');
        if ($enableAvailableCountries == '1' && $availableCountries) {
            $availableCountries = explode(',', $availableCountries);
            if (!in_array($country, $availableCountries)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
