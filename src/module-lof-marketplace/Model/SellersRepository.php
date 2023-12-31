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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Api\Data\SellerInterface;
use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Lof\MarketPlace\Helper\WebsiteStore;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellersRepository implements SellersRepositoryInterface
{
    /**
     * @var Seller
     */
    protected $_seller;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Sender|SenderFactory
     */
    private $senderFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var SellerHelper
     */
    protected $heplerSeller;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @var bool
     */
    protected $flagCheckVerify = true;

    /**
     * SellersFrontendRepository constructor.
     *
     * @param Seller $seller
     * @param SellerFactory $sellerFactory
     * @param CollectionFactory $collection
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Data $helperData
     * @param SellerHelper $heplerSeller
     * @param SenderFactory $sender
     * @param AddressFactory $addressFactory
     * @param CustomerFactory $customerFactory
     * @param WebsiteStore $websiteStoreHelper
     * @param EventManager $eventManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Seller $seller,
        SellerFactory $sellerFactory,
        CollectionFactory $collection,
        StoreManagerInterface $storeManager,
        Data $helperData,
        AccountManagementInterface $accountManagement,
        SellerHelper $heplerSeller,
        SenderFactory $sender,
        AddressFactory $addressFactory,
        CustomerFactory $customerFactory,
        WebsiteStore $websiteStoreHelper,
        EventManager $eventManager
    ) {
        $this->_seller = $seller;
        $this->sellerCollectionFactory = $collection;
        $this->_storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
        $this->helperData = $helperData;
        $this->accountManagement = $accountManagement;
        $this->heplerSeller = $heplerSeller;
        $this->senderFactory = $sender;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->_eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCollection()
    {
        return $this->sellerCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicProfile($sellerId, $message = "")
    {
        if ($sellerId) {
            $seller = $this->_seller->load((int)$sellerId);
            if ($seller->getId() && (!$this->flagCheckVerify || ($seller->getStatus() == Seller::STATUS_VERIFY))) {
                $customerId = $seller->getCustomerId();
                $customer = $this->customerFactory->create()->load((int)$customerId);

                $data = [
                    "customer_id" => $customerId,
                    "seller_id" => (int)$sellerId,
                    "contact_number" => $seller->getContactNumber(),
                    "shop_title" => $seller->getShopTitle(),
                    "company_locality" => $seller->getCompanyLocality(),
                    "company_description" => $seller->getCompanyDescription(),
                    "return_policy" => $seller->getReturnPolicy(),
                    "shipping_policy" => $seller->getShippingPolicy(),
                    "address" => $seller->getAddress(),
                    "country" => $seller->getCountry(),
                    "status" => $seller->getStatus(),
                    "verify_status" => $seller->getVerifyStatus(),
                    "region" => $seller->getRegion(),
                    "city" => $seller->getCity(),
                    "url" => $seller->getUrl(),
                    "telephone" => $seller->getData('telephone'),
                    "url_key" => $seller->getData('url_key'),
                    "product_count" => $seller->getProductCount(),
                    "postcode" => $seller->getPostcode(),
                    "country_id" => $seller->getCountryId(),
                    "company" => $seller->getCompany(),
                    "store_id" => $seller->getStoreId(),
                    "sale" => $seller->getSale(),
                    "message" => $message,
                    "banner_pic" => $seller->getBannerPic(),
                    "total_sold" => $seller->getData('total_sold'),
                    "logo_pic" => $seller->getLogoPic(),
                    "taxvat" => $customer->getTaxvat(),
                    "image" => $this->_storeManager->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $seller->getImage(),
                    "thumbnail" => $this->_storeManager->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $seller->getThumbnail(),
                    "creation_time" => $seller->getData('creation_time')
                ];
                return $seller->getDataModel($data);
            }
        }
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getCurrentSellers($customerId)
    {
        if ($customerId) {
            $sellerCollection = $this->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            $seller_data = $sellerCollection ? $sellerCollection->getData() : [];
            if (count($seller_data) > 0) {
                return $this->getPublicProfile((int)$seller_data["seller_id"]);
            } else {
                throw new NoSuchEntityException(__('Customer has not registered the seller yet'));
            }
        }
        throw new NoSuchEntityException(__('Customer ID is required!'));
    }

    /**
     * {@inheritdoc}
     */
    public function saveProfile(SellerInterface $seller, $customerId)
    {
        if (!$customerId) {
            throw new CouldNotSaveException(__('Sorry. Create New Seller require customer_id field.'));
        }
        $customer = $this->customerFactory->create()->load((int)$customerId);
        if (!$customer || ($customer && !$customer->getId()) ) {
            throw new CouldNotSaveException(__('Customer account is not exists.'));
        }
        try {
            $taxvat = null;
            if ($seller->getTaxvat()) {
                $taxvat = $seller->getTaxvat();
            }
            $seller_model = $this->sellerFactory->create()->load((int)$customerId, "customer_id");
            if (!$seller_model || ($seller_model && !$seller_model->getSellerId())) {
                throw new CouldNotSaveException(__('Seller Account is not exists.'));
            }
            $data = $seller_model->getData();
            if ($seller->getContactNumber()) {
                $data["contact_number"] = $seller->getContactNumber();
            }
            if ($seller->getShopTitle()) {
                $data["shop_title"] = $seller->getShopTitle();
            }
            if ($seller->getCompany()) {
                $data["company"] = $seller->getCompany();
            }
            if ($seller->getCompanyLocality()) {
                $data["company_locality"] = $seller->getCompanyLocality();
            }
            if ($seller->getCompanyDescription()) {
                $data["company_description"] = $seller->getCompanyDescription();
            }
            if ($seller->getReturnPolicy()) {
                $data["return_policy"] = $seller->getReturnPolicy();
            }
            if ($seller->getShippingPolicy()) {
                $data["shipping_policy"] = $seller->getShippingPolicy();
            }
            if ($seller->getAddress()) {
                $data["address"] = $seller->getAddress();
            }
            if ($seller->getRegionId()) {
                $data["region_id"] = $seller->getRegionId();
            }
            if ($seller->getRegion()) {
                $data["region"] = $seller->getRegion();
            }
            if ($seller->getCity()) {
                $data["city"] = $seller->getCity();
            }
            if ($seller->getPostcode()) {
                $data["postcode"] = $seller->getPostcode();
            }
            if ($seller->getCountry()) {
                $data["country"] = $seller->getCountry();
            }
            if ($seller->getImage()) {
                $data["image"] = $seller->getImage();
            }
            if ($seller->getThumbnail()) {
                $data["thumbnail"] = $seller->getThumbnail();
            }
            if ($seller->getEmail() && filter_var($seller->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $data["email"] = $seller->getEmail();
            }
            if ($seller->getName()) {
                $data["name"] = $seller->getName();
            }

            $data["twitter_id"] = $seller->getTwitterId();
            $data["facebook_id"] = $seller->getFacebookId();
            $data["youtube_id"] = $seller->getYoutubeId();
            $data["gplus_id"] = $seller->getGplusId();
            $data["vimeo_id"] = $seller->getVimeoId();
            $data["instagram_id"] = $seller->getInstagramId();
            $data["pinterest_id"] = $seller->getPinterestId();
            $data["linkedin_id"] = $seller->getLinkedinId();
            $data["tw_active"] = $seller->getTwActive();
            $data["fb_active"] = $seller->getFbActive();
            $data["gplus_active"] = $seller->getGplusActive();
            $data["vimeo_active"] = $seller->getVimeoActive();
            $data["instagram_active"] = $seller->getInstagramActive();
            $data["pinterest_active"] = $seller->getPinterestActive();
            $data["linkedin_active"] = $seller->getLinkedinActive();
            if ($seller->getBannerPic()) {
                $data["banner_pic"] = $seller->getBannerPic();
            }
            // if ($seller->getShopUrl()) {
            //     $data["shop_url"] = $seller->getShopUrl();
            // }
            if ($seller->getLogoPic()) {
                $data["logo_pic"] = $seller->getLogoPic();
            }

            $seller_model->setData($data);
            $seller_model->save();

            if ($taxvat) {
                $customer->setTaxvat($taxvat);
                $customer->save();
            }

            return $this->getPublicProfile($seller_model->getId());
        } catch (\Exception $exception) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception($exception->getMessage());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function saveSeller(SellerInterface $seller, $customerId)
    {
        if (!$customerId) {
            throw new CouldNotSaveException(__('Sorry. Create New Seller require customerId field.'));
        }
        $customer = $this->customerFactory->create()->load((int)$customerId);
        if (!$customer || ($customer && !$customer->getId()) ) {
            throw new CouldNotSaveException(__('Customer account is not exists.'));
        }
        $helper = $this->helperData;
        if ($seller && $customerId) {
            $taxvat = null;
            if ($seller->getTaxvat()) {
                $taxvat = $seller->getTaxvat();
            }
            $findExistSeller = $this->sellerCollectionFactory->create()->addFieldToFilter('customer_id', (int)$customerId)->getFirstItem();
            if ($findExistSeller && $findExistSeller->getId()) {
                throw new CouldNotSaveException(__('Sorry. You really registered seller account.'));
            }
            $helperSeller = $this->heplerSeller;
            $group = $seller->getGroup();
            $enableGroupSeller = $helper->getConfig('group_seller/enable_group_seller');
            $enableSellerMembership = $helper->isEnableModule('Lofmp_SellerMembership');
            if (!$enableGroupSeller || $enableSellerMembership) {
                $group = (int)$helper->getConfig('seller_settings/default_seller_group');
            }
            $name = $seller->getName() ? $seller->getName() : $customer->getName();
            $email = $seller->getEmail() ? $seller->getEmail() : $customer->getEmail();
            $url = $seller->getUrl();
            $suffix = $helper->getConfig('general_settings/url_suffix');
            if ($suffix) {
                $url = str_replace($suffix, "", $url);
                $url = str_replace(".", "-", $url);
            }
            $url = $helperSeller->formatUrlKey($url);
            $sellerApproval = $helper->getConfig('general_settings/seller_approval');
            $layout = "2columns-left";
            $stores = [];
            $stores[] = $helper->getCurrentStoreId();
            $sellerModel = $this->sellerFactory->create();
            $status = ($sellerApproval) ? 0 : 1;
            try {
                if (!$url) {
                    throw new CouldNotSaveException(__('Seller URL is required'));
                } elseif (!$email || !$helper->validateEmailAddress($email)) {
                    throw new CouldNotSaveException(__('Email address is required, '));
                } elseif (!$helperSeller->checkSellerUrl($url)) {
                    throw new CouldNotSaveException(__('Sorry. URL key for specified store already exists.'));
                } elseif (!$helperSeller->checkSellerExist((int)$customerId)) {
                    throw new CouldNotSaveException(__('You have already been a seller.'));
                } elseif (!$helperSeller->checkSellerGroup($group)) {
                    throw new CouldNotSaveException(__('Sorry. The store does not support to create sellers in your seller group.'));
                } else {
                    $sellerModel->setName($name)
                                ->setEmail($email)
                                ->setStatus($status)
                                ->setGroupId($group)
                                ->setCustomerId((int)$customerId)
                                ->setStores($stores)
                                ->setUrlKey($url)
                                ->setPageLayout($layout);

                    $sellerModel->save();

                    if ($taxvat) {
                        $customer->setTaxvat($taxvat);
                        $customer->save();
                    }

                    if ($helper->getConfig('email_settings/enable_send_email')) {
                        $data = [];
                        $data['name'] = $name;
                        $data['email'] = $email;
                        $data['group'] = $group;
                        $data['url'] = $sellerModel->getUrl();
                        $this->senderFactory->create()->registerSeller($data);
                    }

                }
                if ($sellerApproval) {
                    $message =  __('Save data success! Please wait admin approval.');
                } else {
                    $message =__('Save data success!');
                }
                return $this->getPublicProfile((int)$sellerModel->getId(), $message);
            } catch (LocalizedException $e) {
                throw new CouldNotSaveException(__("Can not save Seller Data. Error: %1", $e->getMessage()));
            }
        } else {
            throw new CouldNotSaveException(__('Customer ID and Seller Data are required.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerNewSeller(CustomerInterface $customer, \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data, $password = null)
    {
        if (!$customer || !$data) {
            throw new CouldNotSaveException(__('Required data for customer or seller are missing. Please try input again!'));
        }
        $message = "";
        $sellerId = 0;
        $helperSeller = $this->heplerSeller;
        $helperData = $this->helperData;
        $url = $data->getShopUrl();
        $group = $data->getGroupId();
        $layout = '2columns-left';
        $current_store_id = $this->_storeManager->getStore()->getId();
        $emailcustomer = $customer->getEmail();
        $newCustomer = null;
        try {
            $suffix = $helperData->getConfig('general_settings/url_suffix');
            if ($suffix) {
                $url = str_replace($suffix, "", $url);
                $url = str_replace(".", "-", $url);
            }
            $url = $helperSeller->formatUrlKey($url);
            if (!$url) {
                throw new CouldNotSaveException(__('URL key is required.'));
            }
            if (!$helperSeller->checkSellerUrl($url)) {
                throw new CouldNotSaveException(__('URL key for specified store already exists.'));
            }
            $emailts = $helperData->getCustomerByEmail($emailcustomer)->getData('entity_id');
            if (!$helperSeller->checkSellerExist($emailts)) {
                throw new CouldNotSaveException(__('Email already exists.'));
            }

            $enableGroupSeller = $helperData->getConfig('group_seller/enable_group_seller');
            $enableSellerMembership = $helperData->isEnableModule('Lofmp_SellerMembership');
            if (!$enableGroupSeller || $enableSellerMembership) {
                $group = (int)$helperData->getConfig('seller_settings/default_seller_group');
            } elseif ($enableGroupSeller && !$group) {
                throw new CouldNotSaveException(__('Sorry. Create New Seller require group_id field.'));
            } elseif ($enableGroupSeller && $group && !$helperSeller->checkSellerGroup((int)$group)) {
                throw new CouldNotSaveException(__('Sorry. The store does not support to create sellers in your seller group.'));
            }
            if (!$helperSeller->checkCountry($data->getCountryId())) {
                throw new CouldNotSaveException(__('Sorry. The store does not support to create sellers in your country.'));
            }
            //Create customer account
            $newCustomer = $this->accountManagement->createAccount($customer, $password);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('Sorry. We can not create account at now: %1', $e->getMessage()));
        }
        //Create New Seller Account
        $addressCustomer = $this->addressFactory->create()->load($newCustomer->getId());
        if ($newCustomer->getId()) {
            $stores = [];
            $stores[] = $current_store_id;
            if ($helperData->getConfig('general_settings/enable_all_store')) {
                $newStores = $this->websiteStoreHelper->getWebsteStoreIds();
                if ($newStores && count($newStores) > 0) {
                    $stores = array_merge($newStores, $stores);
                }
            }
            $name = $addressCustomer->getName();
            $email = $newCustomer->getEmail();
            $sellerApproval = $helperData->getConfig('general_settings/seller_approval');
            $stores = [];
            $stores[] = $helperData->getCurrentStoreId();
            $sellerModel = $this->sellerFactory->create();
            $status = $sellerApproval ? 0 : 1;
            try {
                $sellerModel->setData($addressCustomer->getData())
                    ->setUrlKey($url)
                    ->setGroupId((int)$group)
                    ->setCustomerId($newCustomer->getId())
                    ->setName($newCustomer->getFirstname() . ' ' . $newCustomer->getLastname())
                    ->setCountryId($addressCustomer->getCountryId())
                    ->setStores($stores)
                    ->setPageLayout($layout)
                    ->setEmail($email)
                    ->setStatus($status);

                if ($addressCustomer->getCountryId()) {
                    $country = $helperData->getCountryname($addressCustomer->getCountryId());
                    $sellerModel->setCountry($country);
                }
                if ($data->getCity()) {
                    $sellerModel->setCity($data->getCity());
                }
                if ($data->getCompany()) {
                    $sellerModel->setCompany($data->getCompany());
                }
                if ($data->getTelephone()) {
                    $sellerModel->setTelephone($data->getTelephone());
                }
                if ($data->getContactNumber()) {
                    $sellerModel->setContactNumber($data->getContactNumber());
                }
                if ($data->getAddress()) {
                    $sellerModel->setAddress($data->getAddress());
                }
                if ($data->getRegion()) {
                    $sellerModel->setRegion($data->getRegion());
                }
                if ($data->getRegionId()) {
                    $sellerModel->setRegionId($data->getRegionId());
                }
                if ($data->getPostcode()) {
                    $sellerModel->setPostcode($data->getPostcode());
                }
                if ($data->getCountryId()) {
                    $country = $helperData->getCountryname($data->getCountryId());
                    $sellerModel->setCountryId($data->getCountryId());
                    $sellerModel->setCountry($country);
                }
                if ($sellerApproval) {
                    $message = __('Save data success! Please wait admin approval.');
                } else {
                    $message = __('Save data success!');
                }
                $sellerModel->save();

                $this->_eventManager->dispatch('seller_register_success_api', [
                    'object' => $this,
                    'seller' => $sellerModel,
                    'url' => $url
                ]);

                $sellerId = $sellerModel->getId();
                if ($helperData->getConfig('email_settings/enable_send_email')) {
                    $senderData = [];
                    $senderData['name'] = $name;
                    $senderData['email'] = $email;
                    $senderData['group'] = $group;
                    $senderData['url'] = $sellerModel->getUrl();
                    $sender = $this->senderFactory->create();
                    $sender->noticeAdmin($data->__toArray());
                    $sender->registerSeller($senderData);
                }
                $this->flagCheckVerify = false;
                return $this->getPublicProfile((int)$sellerId, $message);
            } catch (LocalizedException $e) {
                throw new CouldNotSaveException(__(
                    'Having error when create new seller account: %1',
                    $e->getMessage()
                ));
            }
        } else {
            throw new CouldNotSaveException(__('Sorry. We can not create seller account at now: Error when create customer account or missing customer data'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function becomeSeller(int $customerId, \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data)
    {
        if (!$data || !$data->getShopUrl() || !$customerId) {
            throw new CouldNotSaveException(__('Required data seller are missing. Please try input again!'));
        }
        $findExistSeller = $this->getCollection()
                        ->addFieldToFilter('customer_id', (int)$customerId)
                        ->getFirstItem();
        if ($findExistSeller && $findExistSeller->getId()) {
            throw new CouldNotSaveException(__('Sorry. You really registered seller account.'));
        }
        $customer = $this->customerFactory->create()->load((int)$customerId);
        if (!$customer || ($customer && !$customer->getId()) ) {
            throw new CouldNotSaveException(__('Customer account is not exists.'));
        }
        $message = "";
        $sellerId = 0;
        $helperSeller = $this->heplerSeller;
        $helperData = $this->helperData;
        $url = $data->getShopUrl();
        $group = $data->getGroupId();
        $layout = '2columns-left';
        $current_store_id = $this->_storeManager->getStore()->getId();
        try {
            $suffix = $helperData->getConfig('general_settings/url_suffix');
            if ($suffix) {
                $url = str_replace($suffix, "", $url);
                $url = str_replace(".", "-", $url);
            }
            $url = $helperSeller->formatUrlKey($url);
            if (!$url) {
                throw new CouldNotSaveException(__('URL key is required.'));
            }
            if (!$helperSeller->checkSellerUrl($url)) {
                throw new CouldNotSaveException(__('URL key for specified store already exists.'));
            }

            $enableGroupSeller = $helperData->getConfig('group_seller/enable_group_seller');
            $enableSellerMembership = $helperData->isEnableModule('Lofmp_SellerMembership');
            if (!$enableGroupSeller || $enableSellerMembership) {
                $group = (int)$helperData->getConfig('seller_settings/default_seller_group');
            } elseif ($enableGroupSeller && !$group) {
                throw new CouldNotSaveException(__('Sorry. Create New Seller require group_id field.'));
            } elseif ($enableGroupSeller && $group && !$helperSeller->checkSellerGroup((int)$group)) {
                throw new CouldNotSaveException(__('Sorry. The store does not support to create sellers in your seller group.'));
            }
            if (!$helperSeller->checkCountry($data->getCountryId())) {
                throw new CouldNotSaveException(__('Sorry. The store does not support to create sellers in your country.'));
            }
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('Sorry. We can not create account at now: %1', $e->getMessage()));
        }
        //Create New Seller Account
        $addressCustomer = $this->addressFactory->create()->load($customer->getId());
        if ($customer->getId()) {
            $stores = [];
            $stores[] = $current_store_id;
            if ($helperData->getConfig('general_settings/enable_all_store')) {
                $newStores = $this->websiteStoreHelper->getWebsteStoreIds();
                if ($newStores && count($newStores) > 0) {
                    $stores = array_merge($newStores, $stores);
                }
            }
            $name = $addressCustomer->getName();
            $email = $customer->getEmail();
            $sellerApproval = $helperData->getConfig('general_settings/seller_approval');
            $stores = [];
            $stores[] = $helperData->getCurrentStoreId();
            $sellerModel = $this->sellerFactory->create();
            $status = $sellerApproval ? 0 : 1;
            try {
                $sellerModel->setData($addressCustomer->getData())
                    ->setUrlKey($url)
                    ->setGroupId((int)$group)
                    ->setCustomerId($customer->getId())
                    ->setName($customer->getFirstname() . ' ' . $customer->getLastname())
                    ->setCountryId($addressCustomer->getCountryId())
                    ->setStores($stores)
                    ->setPageLayout($layout)
                    ->setEmail($email)
                    ->setStatus($status);

                if ($addressCustomer->getCountryId()) {
                    $country = $helperData->getCountryname($addressCustomer->getCountryId());
                    $sellerModel->setCountry($country);
                }
                if ($data->getCity()) {
                    $sellerModel->setCity($data->getCity());
                }
                if ($data->getCompany()) {
                    $sellerModel->setCompany($data->getCompany());
                }
                if ($data->getTelephone()) {
                    $sellerModel->setTelephone($data->getTelephone());
                }
                if ($data->getContactNumber()) {
                    $sellerModel->setContactNumber($data->getContactNumber());
                }
                if ($data->getAddress()) {
                    $sellerModel->setAddress($data->getAddress());
                }
                if ($data->getRegion()) {
                    $sellerModel->setRegion($data->getRegion());
                }
                if ($data->getRegionId()) {
                    $sellerModel->setRegionId($data->getRegionId());
                }
                if ($data->getPostcode()) {
                    $sellerModel->setPostcode($data->getPostcode());
                }
                if ($data->getCountryId()) {
                    $country = $helperData->getCountryname($data->getCountryId());
                    $sellerModel->setCountryId($data->getCountryId());
                    $sellerModel->setCountry($country);
                }
                if ($sellerApproval) {
                    $message = __('Save data success! Please wait admin approval.');
                } else {
                    $message = __('Save data success!');
                }
                $sellerModel->save();

                $this->_eventManager->dispatch('seller_register_success_api', [
                    'object' => $this,
                    'seller' => $sellerModel,
                    'customer' => $customer,
                    'url' => $url,
                    'is_become_seller' => true
                ]);

                $sellerId = $sellerModel->getId();
                if ($helperData->getConfig('email_settings/enable_send_email')) {
                    $senderData = [];
                    $senderData['name'] = $name;
                    $senderData['email'] = $email;
                    $senderData['group'] = $group;
                    $senderData['url'] = $sellerModel->getUrl();
                    $sender = $this->senderFactory->create();
                    $sender->noticeAdmin($data);
                    $sender->registerSeller($senderData);
                }
                $this->flagCheckVerify = false;
                return $this->getPublicProfile((int)$sellerId, $message);
            } catch (LocalizedException $e) {
                throw new CouldNotSaveException(__(
                    'Having error when create new seller account: %1',
                    $e->getMessage()
                ));
            }
        } else {
            throw new CouldNotSaveException(__('Sorry. We can not create seller account at now: Error when create customer account or missing customer data'));
        }
    }
}