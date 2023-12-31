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

namespace Lof\MarketPlace\Helper;

use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\SellerProductFactory;
use Lof\MarketPlace\Model\VacationFactory;
use Lof\MarketPlace\Model\GroupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Setup\Exception;

class Seller extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProductFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Customer\Model\Address
     */
    protected $address;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var mixed|array
     */
    protected $_vacationData = [];

    /**
     * @var VacationFactory
     */
    protected $vacation;

    /**
     * @var GroupFactory
     */
    protected $group;

    /**
     * @var array
     */
    private $postData = null;

    /**
     * Seller constructor.
     * @param Context $context
     * @param SellerFactory $sellerFactory
     * @param SellerProductFactory $sellerProductFactory
     * @param CustomerFactory $customerFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Customer\Model\Address $address
     * @param Data $sellerHelper
     * @param Session $customerSession
     * @param FilterManager $filter
     * @param VacationFactory $vacation
     */
    public function __construct(
        Context $context,
        SellerFactory $sellerFactory,
        SellerProductFactory $sellerProductFactory,
        CustomerFactory $customerFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Customer\Model\Address $address,
        Data $sellerHelper,
        Session $customerSession,
        FilterManager $filter,
        VacationFactory $vacation,
        GroupFactory $group,
        DateTime $helperDateTime
    ) {
        parent::__construct($context);
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->dataPersistor = $dataPersistor;
        $this->address = $address;
        $this->_sellerHelper = $sellerHelper;
        $this->filter = $filter;
        $this->vacation = $vacation;
        $this->group = $group;
        $this->helperDateTime = $helperDateTime;
    }

    /**
     * Format Key for URL
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $this->filter->translitUrl($str);
    }

    /**
     * get helper data
     *
     * @return Data
     */
    public function getHelperData()
    {
        return $this->_sellerHelper;
    }

    /**
     * @return array|mixed|null
     */
    public function getSellerId()
    {
        $seller = $this->getSeller();

        return $seller->getData('seller_id');
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getListSeller($limit = null, $currPage = 1)
    {
        $collection = $this->sellerFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->setOrder("position", "ASC");
        if ($limit) {
            $collection->setPageSize((int)$limit)
                ->setCurpage($currPage);
        }
        return $collection;
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getSellerIdByProduct($productId)
    {
        $seller = $this->sellerProductFactory->create()->load($productId, 'product_id');
        return $seller->getSellerId();
    }

    /**
     * @param $productId
     * @return \Lof\MarketPlace\Model\SellerProduct
     */
    public function getSellerByProduct($productId)
    {
        return $this->sellerProductFactory->create()->load($productId, 'product_id');
    }

    /**
     * @return array|mixed|null
     */
    public function getSellerByCustomer()
    {
        $seller = $this->getSeller();
        return $seller->getData();
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller()
    {
        return $this->sellerFactory->create()->load($this->getCustomerId(), 'customer_id');
    }

    /**
     * @param $sellerId
     * @return Customer
     */
    public function getCustomerBySeller($sellerId)
    {
        $seller = $this->sellerFactory->create()->load($sellerId, 'seller_id');
        return $this->customerFactory->create()->load($seller->getCustomerId());
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        $customer = $this->customerSession->getCustomer();

        return $customer->getId();
    }

    /**
     * getCurrentCustomer
     *
     * @return mixed|array|null
     */
    public function getCurrentCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @param $country
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkCountry($country)
    {
        $availableCountries = $this->_sellerHelper->getConfig('available_countries/available_countries');
        $enableAvailableCountries = $this->_sellerHelper->getConfig('available_countries/enable_available_countries');
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

    /**
     * @param $sellerGroup
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkSellerGroup($sellerGroup)
    {
        $enableSellerGroup = $this->_sellerHelper->getConfig('group_seller/enable_group_seller');
        $availableSellerGroup = $this->_sellerHelper->getConfig('group_seller/group_seller');
        if ($enableSellerGroup == '1' && $availableSellerGroup) {
            $availableSellerGroup = explode(',', $availableSellerGroup);
            if (!in_array($sellerGroup, $availableSellerGroup)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Check seller url is exists or not
     * Return: false - is exists, true - is not exists
     * @param $sellerUrl
     * @return bool
     */
    public function checkSellerUrl($sellerUrl)
    {
        $collection = $this->sellerFactory->create()->getCollection();
        $collection->addFieldToFilter('url_key', $sellerUrl);
        if ($collection->count()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $customerId
     * @return bool
     */
    public function checkSellerExist($customerId)
    {
        $collection = $this->sellerFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        if ($collection->getData()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $sellerId
     * @return \Lof\MarketPlace\Model\Vacation|mixed
     */
    public function getVacationBySellerId($sellerId)
    {
        if (!isset($this->_vacationData[$sellerId])) {
            $vacationSeller = $this->vacation->create()->getCollection();
            $today = $this->helperDateTime->getTimezoneDateTime();
            $vacationSeller->addFieldToFilter("from_date", ["lteq" => $today]);
            $vacationSeller->addFieldToFilter("to_date", ["gteq" => $today]);
            $vacationSeller->addFieldToFilter("seller_id", $sellerId);
            $vacationSeller->addFieldToFilter("status", 1);
            $this->_vacationData[$sellerId] = $vacationSeller->getFirstItem();
        }
        return $this->_vacationData[$sellerId];
    }

    /**
     * Get product ids of all seller are on vacation
     *
     * @return array
     */
    public function getSellerVacationProducts(AbstractCollection $collection)
    {
        $vacationSellerProducts = [];
        try {
            foreach ($collection->getItems() as $product) {
                if ($product->getSellerId()){
                    $addToCartText = $this->getSellerOnVacationText($product->getSellerId());
                    if ($addToCartText){
                        $vacationSellerProducts[$product->getId()]['text_add_cart'] = $addToCartText;
                    }
                }
            }
        } catch (Exception $e){
            return $vacationSellerProducts;
        }
        return $vacationSellerProducts;
    }

    /**
     * Return add to cart text if seller is on vacation
     *
     * @param int $sellerId
     * @return string|bool
     */
    public function getSellerOnVacationText($sellerId)
    {
        $vacationSeller = $this->getVacationBySellerId($sellerId);
        if ($vacationSeller->getId()) {
            $text = $vacationSeller->getData('text_add_cart');
            if (!$text) {
                $text = 'Not available';
            }
            return $text;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getSellerGroupId()
    {
        return $this->getSeller()->getGroupId();
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->getGroupNameById($this->getSellerGroupId());
    }

    /**
     * @param $id
     * @return string
     */
    public function getGroupNameById($id)
    {
        return $this->getGroup($id)->getName();
    }

    /**
     * @param $id
     * @return \Lof\MarketPlace\Model\Group
     */
    public function getGroup($id)
    {
        return $this->group->create()->load($id);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->getCurrentCustomer();

        return $customer->getEmail();
    }

    /**
     * Get user address
     *
     * @return array|null
     */
    public function getUserAddress()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->getCurrentCustomer();
        $billingAddressId = $customer->getDefaultBilling();

        return $this->address->load($billingAddressId);
    }

    /**
     * Get user country id
     *
     * @return string|null
     */
    public function getUserCountryId ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['country_id'];
        }

        return '';
    }

    /**
     * Get user region id
     *
     * @return mix|null
     */
    public function getUserRegionId ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['region_id'];
        }

        return '';
    }

    /**
     * Get user region
     *
     * @return mix|null
     */
    public function getUserRegion ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['region'];
        }

        return '';
    }

    /**
     * Get user postcode
     *
     * @return mix|null
     */
    public function getUserPostcode ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['postcode'];
        }

        return '';
    }

    /**
     * Get user city
     *
     * @return mix|null
     */
    public function getUserCity ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['city'];
        }

        return '';
    }

    /**
     * Get user company
     *
     * @return mix|null
     */
    public function getUserCompany ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['company'];
        }

        return '';
    }

    /**
     * Get user telephone
     *
     * @return mix|null
     */
    public function getUserTelephone ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['telephone'];
        }

        return '';
    }

    /**
     * Get user street
     *
     * @return string|null
     */
    public function getUserStreet ()
    {
        $userAddress = $this->getUserAddress();
        if ($userAddress) {
            return $userAddress['street'];
        }

        return '';
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('seller-form-validate');
            $this->getDataPersistor()->clear('seller-form-validate');
        }

        if (isset($this->postData[$key])) {
            return $this->postData[$key];
        }

        return '';
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    /**
     * @param $code
     * @return string
     */
    public function getCountryPhoneCode($code)
    {
        $countrycode = array(
            'AD'=>'376',
            'AE'=>'971',
            'AF'=>'93',
            'AG'=>'1268',
            'AI'=>'1264',
            'AL'=>'355',
            'AM'=>'374',
            'AN'=>'599',
            'AO'=>'244',
            'AQ'=>'672',
            'AR'=>'54',
            'AS'=>'1684',
            'AT'=>'43',
            'AU'=>'61',
            'AW'=>'297',
            'AZ'=>'994',
            'BA'=>'387',
            'BB'=>'1246',
            'BD'=>'880',
            'BE'=>'32',
            'BF'=>'226',
            'BG'=>'359',
            'BH'=>'973',
            'BI'=>'257',
            'BJ'=>'229',
            'BL'=>'590',
            'BM'=>'1441',
            'BN'=>'673',
            'BO'=>'591',
            'BR'=>'55',
            'BS'=>'1242',
            'BT'=>'975',
            'BW'=>'267',
            'BY'=>'375',
            'BZ'=>'501',
            'CA'=>'1',
            'CC'=>'61',
            'CD'=>'243',
            'CF'=>'236',
            'CG'=>'242',
            'CH'=>'41',
            'CI'=>'225',
            'CK'=>'682',
            'CL'=>'56',
            'CM'=>'237',
            'CN'=>'86',
            'CO'=>'57',
            'CR'=>'506',
            'CU'=>'53',
            'CV'=>'238',
            'CX'=>'61',
            'CY'=>'357',
            'CZ'=>'420',
            'DE'=>'49',
            'DJ'=>'253',
            'DK'=>'45',
            'DM'=>'1767',
            'DO'=>'1809',
            'DZ'=>'213',
            'EC'=>'593',
            'EE'=>'372',
            'EG'=>'20',
            'ER'=>'291',
            'ES'=>'34',
            'ET'=>'251',
            'FI'=>'358',
            'FJ'=>'679',
            'FK'=>'500',
            'FM'=>'691',
            'FO'=>'298',
            'FR'=>'33',
            'GA'=>'241',
            'GB'=>'44',
            'GD'=>'1473',
            'GE'=>'995',
            'GH'=>'233',
            'GI'=>'350',
            'GL'=>'299',
            'GM'=>'220',
            'GN'=>'224',
            'GQ'=>'240',
            'GR'=>'30',
            'GT'=>'502',
            'GU'=>'1671',
            'GW'=>'245',
            'GY'=>'592',
            'HK'=>'852',
            'HN'=>'504',
            'HR'=>'385',
            'HT'=>'509',
            'HU'=>'36',
            'ID'=>'62',
            'IE'=>'353',
            'IL'=>'972',
            'IM'=>'44',
            'IN'=>'91',
            'IQ'=>'964',
            'IR'=>'98',
            'IS'=>'354',
            'IT'=>'39',
            'JM'=>'1876',
            'JO'=>'962',
            'JP'=>'81',
            'KE'=>'254',
            'KG'=>'996',
            'KH'=>'855',
            'KI'=>'686',
            'KM'=>'269',
            'KN'=>'1869',
            'KP'=>'850',
            'KR'=>'82',
            'KW'=>'965',
            'KY'=>'1345',
            'KZ'=>'7',
            'LA'=>'856',
            'LB'=>'961',
            'LC'=>'1758',
            'LI'=>'423',
            'LK'=>'94',
            'LR'=>'231',
            'LS'=>'266',
            'LT'=>'370',
            'LU'=>'352',
            'LV'=>'371',
            'LY'=>'218',
            'MA'=>'212',
            'MC'=>'377',
            'MD'=>'373',
            'ME'=>'382',
            'MF'=>'1599',
            'MG'=>'261',
            'MH'=>'692',
            'MK'=>'389',
            'ML'=>'223',
            'MM'=>'95',
            'MN'=>'976',
            'MO'=>'853',
            'MP'=>'1670',
            'MR'=>'222',
            'MS'=>'1664',
            'MT'=>'356',
            'MU'=>'230',
            'MV'=>'960',
            'MW'=>'265',
            'MX'=>'52',
            'MY'=>'60',
            'MZ'=>'258',
            'NA'=>'264',
            'NC'=>'687',
            'NE'=>'227',
            'NG'=>'234',
            'NI'=>'505',
            'NL'=>'31',
            'NO'=>'47',
            'NP'=>'977',
            'NR'=>'674',
            'NU'=>'683',
            'NZ'=>'64',
            'OM'=>'968',
            'PA'=>'507',
            'PE'=>'51',
            'PF'=>'689',
            'PG'=>'675',
            'PH'=>'63',
            'PK'=>'92',
            'PL'=>'48',
            'PM'=>'508',
            'PN'=>'870',
            'PR'=>'1',
            'PT'=>'351',
            'PW'=>'680',
            'PY'=>'595',
            'QA'=>'974',
            'RO'=>'40',
            'RS'=>'381',
            'RU'=>'7',
            'RW'=>'250',
            'SA'=>'966',
            'SB'=>'677',
            'SC'=>'248',
            'SD'=>'249',
            'SE'=>'46',
            'SG'=>'65',
            'SH'=>'290',
            'SI'=>'386',
            'SK'=>'421',
            'SL'=>'232',
            'SM'=>'378',
            'SN'=>'221',
            'SO'=>'252',
            'SR'=>'597',
            'ST'=>'239',
            'SV'=>'503',
            'SY'=>'963',
            'SZ'=>'268',
            'TC'=>'1649',
            'TD'=>'235',
            'TG'=>'228',
            'TH'=>'66',
            'TJ'=>'992',
            'TK'=>'690',
            'TL'=>'670',
            'TM'=>'993',
            'TN'=>'216',
            'TO'=>'676',
            'TR'=>'90',
            'TT'=>'1868',
            'TV'=>'688',
            'TW'=>'886',
            'TZ'=>'255',
            'UA'=>'380',
            'UG'=>'256',
            'US'=>'1',
            'UY'=>'598',
            'UZ'=>'998',
            'VA'=>'39',
            'VC'=>'1784',
            'VE'=>'58',
            'VG'=>'1284',
            'VI'=>'1340',
            'VN'=>'84',
            'VU'=>'678',
            'WF'=>'681',
            'WS'=>'685',
            'XK'=>'381',
            'YE'=>'967',
            'YT'=>'262',
            'ZA'=>'27',
            'ZM'=>'260',
            'ZW'=>'263'
        );

        $countryDialCode = $countrycode[$code];
        return "(+" . $countryDialCode . ")";
    }
}
