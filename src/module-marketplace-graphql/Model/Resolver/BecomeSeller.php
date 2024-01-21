<?php

declare(strict_types=1);

namespace CoreMarketplace\MarketplaceGraphQl\Model\Resolver;

use Lof\MarketPlace\Api\Data\SellerInterface;
use Lof\MarketplaceGraphQl\Model\Resolver\DataProvider\CreateSeller as DataProviderCreateSeller;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Catalog\Model\Product\Url;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\SenderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Helper\WebsiteStore;
use CoreMarketplace\MarketPlace\Helper\Data as MarketplaceHelper;

class BecomeSeller extends \Lof\MarketplaceGraphQl\Model\Resolver\BecomeSeller
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var DataProviderCreateSeller
     */
    private $_createSeller;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var SellerInterface
     */
    private $sellerInterface;

    /**
     * @var CollectionFactory
     */
    private $collection;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var SenderFactory
     */
    private $senderFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var SellerHelper
     */
    protected $helperSeller;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * Become Seller constructor.
     * @param DataProviderCreateSeller $createSeller
     * @param GetCustomer $getCustomer
     * @param SellerInterface $sellerInterface
     * @param Url $url
     * @param CollectionFactory $collection
     * @param CustomerFactory $customerFactory
     * @param SenderFactory $sender
     * @param Data $helperData
     * @param SellerFactory $sellerFactory
     * @param StoreManagerInterface $storeManager
     * @param SellerHelper $helperSeller
     * @param WebsiteStore $websiteStoreHelper
     * @param MarketplaceHelper $marketplaceHelper
     */
    public function __construct(
        DataProviderCreateSeller $createSeller,
        GetCustomer $getCustomer,
        SellerInterface $sellerInterface,
        Url $url,
        CollectionFactory $collection,
        CustomerFactory $customerFactory,
        SenderFactory $sender,
        Data $helperData,
        SellerFactory $sellerFactory,
        StoreManagerInterface $storeManager,
        SellerHelper $helperSeller,
        WebsiteStore $websiteStoreHelper,
        MarketplaceHelper $marketplaceHelper
    ) {
        $this->_createSeller = $createSeller;
        $this->getCustomer = $getCustomer;
        $this->sellerInterface = $sellerInterface;
        $this->url = $url;
        $this->sellerCollectionFactory = $collection;
        $this->customerFactory = $customerFactory;
        $this->senderFactory = $sender;
        $this->helperData = $helperData;
        $this->sellerFactory = $sellerFactory;
        $this->_storeManager = $storeManager;
        $this->helperSeller = $helperSeller;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->marketplaceHelper = $marketplaceHelper;

        parent::__construct(
            $createSeller,
            $getCustomer,
            $sellerInterface,
            $url
        );
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $code = 0;
        $message = __("We can not create seller account at now");

        /** @var ContextInterface $context */
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        if (!($args['input']) || !isset($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }
        
        $customer = $this->getCustomer->execute($context);

        $result = $this->becomeSeller($customer, $args['input']);
        if ($result) {
            $code = $result['code'];
            $message = $result['message'];
        }

        return [
            "code" => $code,
            "message" => $message
        ];
    }
    
    public function becomeSeller($customer, $sellerData)
    {
        $message = "";
        $sellerId = 0;
        
        $customerId = $customer->getId();

        if (!$sellerData || !isset($sellerData['url_key']) || !$customerId) {
            throw new GraphQlInputException(__('Required data seller are missing. Please try input again!'));
        }

        $findExistSeller = $this->sellerCollectionFactory->create()
                        ->addFieldToFilter('customer_id', (int)$customerId)
                        ->getFirstItem();
        if ($findExistSeller && $findExistSeller->getId()) {
            throw new GraphQlInputException(__('You already registered as seller.'));
        }
        
        $countryId = isset($sellerData['country_id']) ? $sellerData['country_id'] : '';
        $country = $countryId ? $this->helperData->getCountryname($countryId) : '';
        
        $url = isset($sellerData['url_key']) ? $sellerData['url_key'] : '';
        $group = isset($sellerData['group_id']) ? $sellerData['group_id'] : '';
        $layout = '2columns-left';
        $current_store_id = $this->_storeManager->getStore()->getId();
        try {
            $suffix = $this->helperData->getConfig('general_settings/url_suffix');
            if ($suffix) {
                $url = str_replace($suffix, "", $url);
                $url = str_replace(".", "-", $url);
            }
            $url = $this->helperSeller->formatUrlKey($url);
            if (!$url) {
                throw new GraphQlInputException(__('URL key is required.'));
            }
            if (!$this->helperSeller->checkSellerUrl($url)) {
                throw new GraphQlInputException(__('URL key for specified store already exists.'));
            }

            $enableGroupSeller = $this->helperData->getConfig('group_seller/enable_group_seller');
            $enableSellerMembership = $this->helperData->isEnableModule('Lofmp_SellerMembership');
            if (!$enableGroupSeller || $enableSellerMembership) {
                $group = (int)$this->helperData->getConfig('seller_settings/default_seller_group');
            } elseif ($enableGroupSeller && !$group) {
                throw new GraphQlInputException(__('Sorry. Create New Seller require group_id field.'));
            } elseif ($enableGroupSeller && $group && !$this->helperSeller->checkSellerGroup((int)$group)) {
                throw new GraphQlInputException(__('Sorry. The store does not support to create sellers in your seller group.'));
            }
            if (!$this->helperSeller->checkCountry($countryId)) {
                throw new GraphQlInputException(__('Sorry. The store does not support to create sellers in your country.'));
            }
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__('Sorry. We can not create account at now: %1', $e->getMessage()));
        }

        // Create New Seller Account
        if ($customer->getId()) {
            $stores = [];
            $stores[] = $current_store_id;
            if ($this->helperData->getConfig('general_settings/enable_all_store')) {
                $newStores = $this->websiteStoreHelper->getWebsteStoreIds();
                if ($newStores && count($newStores) > 0) {
                    $stores = array_merge($newStores, $stores);
                }
            }

            $sellerType = isset($sellerData['seller_type']) ? $sellerData['seller_type'] : '';
            $name = isset($sellerData['name']) ? $sellerData['name'] : '';
            $city = isset($sellerData['city']) ? $sellerData['city'] : '';
            $company = isset($sellerData['company']) ? $sellerData['company'] : '';
            $companyRegistrationNumber = isset($sellerData['company_registration_number']) ? $sellerData['company_registration_number'] : '';
            $contactNumber = isset($sellerData['contact_number']) ? $sellerData['contact_number'] : '';
            $addressLine1 = isset($sellerData['address_line_1']) ? $sellerData['address_line_1'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $region = isset($sellerData['region']) ? $sellerData['region'] : '';
            $regionId = isset($sellerData['region_id']) ? $sellerData['region_id'] : '';
            if ($regionId) {
                $regionData = $this->marketplaceHelper->getRegionData($regionId);
                if ($regionData) {
                    $region = $regionData->getDefaultName();
                    $regionId = $regionData->getRegionId();
                }
            }
            $postcode = isset($sellerData['postcode']) ? $sellerData['postcode'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $addressLine2 = isset($sellerData['address_line_2']) ? $sellerData['address_line_2'] : '';
            $email = $customer->getEmail();
            $sellerApproval = $this->helperData->getConfig('general_settings/seller_approval');
            $stores = [];
            $stores[] = $this->helperData->getCurrentStoreId();
            $sellerModel = $this->sellerFactory->create();
            $status = $sellerApproval ? 2 : 1;
            try {
                $sellerModel->setSellerType($sellerType)
                    ->setUrlKey($url)
                    ->setGroupId((int)$group)
                    ->setCustomerId($customer->getId())
                    ->setName($name)
                    ->setShopTitle($name)
                    ->setCountryId($countryId)
                    ->setCountry($country)
                    ->setCity($city)
                    ->setCompany($company)
                    ->setCompanyRegistrationNumber($companyRegistrationNumber)
                    ->setContactNumber($contactNumber)
                    ->setTelephone($contactNumber)
                    ->setAddressLine1($addressLine1)
                    ->setAddressLine2($addressLine2)
                    ->setRegion($region)
                    ->setRegionId($regionId)
                    ->setPostcode($postcode)
                    ->setStores($stores)
                    ->setPageLayout($layout)
                    ->setEmail($email)
                    ->setStatus($status);
                if ($sellerApproval) {
                    $message = __('Save data success! Please wait admin approval.');
                } else {
                    $message = __('Save data success!');
                }
                $sellerModel->save();

                $sellerId = $sellerModel->getId();
                if ($this->helperData->getConfig('email_settings/enable_send_email')) {
                    $senderData = [];
                    $senderData['name'] = $name;
                    $senderData['email'] = $email;
                    $senderData['group'] = $group;
                    $senderData['url'] = $sellerModel->getUrl();
                    $sender = $this->senderFactory->create();
                    $sender->noticeAdmin($sellerData);
                    $sender->registerSeller($senderData);
                }
            } catch (LocalizedException $e) {
                throw new GraphQlInputException(__(
                    'Having error when create new seller account: %1',
                    $e->getMessage()
                ));
            }
        } else {
            throw new GraphQlInputException(__('Sorry. We can not create seller account at now: Error when create customer account or missing customer data'));
        }

        return [
            'message' => $message,
            'code' => $sellerId
        ];
    }
}
