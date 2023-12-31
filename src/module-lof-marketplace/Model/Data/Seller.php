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

namespace Lof\MarketPlace\Model\Data;

use Lof\MarketPlace\Api\Data\SellerInterface;
use Magento\Framework\Api\AbstractExtensibleObject;
use \Magento\Framework\Api\AttributeValueFactory;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @codeCoverageIgnore
 */
class Seller extends AbstractExtensibleObject implements SellerInterface
{
    /**
     * @var string[]
     */
    protected $customAttributesCodes = [];

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        $data = []
    ) {
        parent::__construct($extensionFactory, $attributeValueFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function getCustomAttributesCodes()
    {
        return $this->customAttributesCodes;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        $sellerId = $this->_get(self::ID);
        if (!$sellerId) {
            $sellerId = $this->getSellerId();
        }
        return $sellerId;
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->_get(self::KEY_SALE);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::KEY_SALE, $status);
    }

    /**
     * @inheritDoc
     */
    public function getVerifyStatus()
    {
        return $this->_get(self::VERIFY_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setVerifyStatus($verify_status)
    {
        return $this->setData(self::VERIFY_STATUS, $verify_status);
    }

    /**
     * @inheritDoc
     */
    public function setSale($sale)
    {
        return $this->setData(self::KEY_SALE, $sale);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->_get(self::KEY_STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($store_id)
    {
        return $this->setData(self::KEY_STORE_ID, $store_id);
    }

    /**
     * @inheritDoc
     */
    public function getFacebookId()
    {
        return $this->_get(self::KEY_FACEBOOK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setFacebookId($facebook_id)
    {
        return $this->setData(self::KEY_FACEBOOK_ID, $facebook_id);
    }

    /**
     * @inheritDoc
     */
    public function getCommissionId()
    {
        return $this->_get(self::KEY_COMMISSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCommissionId($commission_id)
    {
        return $this->setData(self::KEY_COMMISSION_ID, $commission_id);
    }

    /**
     * @inheritDoc
     */
    public function getBannerPic()
    {
        return $this->_get(self::KEY_BANNER_PIC);
    }

    /**
     * @inheritDoc
     */
    public function setBannerPic($banner_pic)
    {
        return $this->setData(self::KEY_BANNER_PIC, $banner_pic);
    }

    /**
     * @inheritDoc
     */
    public function getGplusId()
    {
        return $this->_get(self::KEY_GPLUS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGplusId($gplus_id)
    {
        return $this->setData(self::KEY_GPLUS_ID, $gplus_id);
    }

    /**
     * @inheritDoc
     */
    public function getInstagramId()
    {
        return $this->_get(self::KEY_INSTAGRAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setInstagramId($instagram_id)
    {
        return $this->setData(self::KEY_INSTAGRAM_ID, $instagram_id);
    }

    /**
     * @inheritDoc
     */
    public function getInstagramActive()
    {
        return $this->_get(self::KEY_INSTAGRAM_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setInstagramActive($instagram_active)
    {
        return $this->setData(self::KEY_INSTAGRAM_ACTIVE, $instagram_active);
    }

    /**
     * @inheritDoc
     */
    public function getGplusActive()
    {
        return $this->_get(self::KEY_GPLUS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setGplusActive($gplus_active)
    {
        return $this->setData(self::KEY_GPLUS_ACTIVE, $gplus_active);
    }

    /**
     * @inheritDoc
     */
    public function getLinkedinId()
    {
        return $this->_get(self::KEY_LINKEDIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLinkedinId($linkedin_id)
    {
        return $this->setData(self::KEY_LINKEDIN_ID, $linkedin_id);
    }

    /**
     * @inheritDoc
     */
    public function getLogoPic()
    {
        return $this->_get(self::KEY_LOGO_PIC);
    }

    /**
     * @inheritDoc
     */
    public function setLogoPic($logo_pic)
    {
        return $this->setData(self::KEY_LOGO_PIC, $logo_pic);
    }

    /**
     * @inheritDoc
     */
    public function getLinkedinActive()
    {
        return $this->_get(self::KEY_LINKEDIN_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setLinkedinActive($linkedin_active)
    {
        return $this->setData(self::KEY_LINKEDIN_ACTIVE, $linkedin_active);
    }

    /**
     * @inheritDoc
     */
    public function getPageLayout()
    {
        return $this->_get(self::KEY_PAGE_LAYOUT);
    }

    /**
     * @inheritDoc
     */
    public function setPageLayout($page_layout)
    {
        return $this->setData(self::KEY_PAGE_LAYOUT, $page_layout);
    }

    /**
     * @inheritDoc
     */
    public function getPinterestActive()
    {
        return $this->_get(self::KEY_PINTEREST_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setPinterestActive($pinterest_active)
    {
        return $this->setData(self::KEY_PINTEREST_ACTIVE, $pinterest_active);
    }

    /**
     * @inheritDoc
     */
    public function getPinterestId()
    {
        return $this->_get(self::KEY_PINTEREST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPinterestId($pinterest_id)
    {
        return $this->setData(self::KEY_PINTEREST_ID, $pinterest_id);
    }

    /**
     * @inheritDoc
     */
    public function getSale()
    {
        return $this->_get(self::KEY_SALE);
    }

    /**
     * @inheritDoc
     */
    public function getShopUrl()
    {
        return $this->_get(self::KEY_SHOP_URL);
    }

    /**
     * @inheritDoc
     */
    public function setShopUrl($shop_url)
    {
        return $this->setData(self::KEY_SHOP_URL, $shop_url);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->_get(self::KEY_POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::KEY_POSITION, $position);
    }

    /**
     * @inheritDoc
     */
    public function getTwitterId()
    {
        return $this->_get(self::KEY_TWITTER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTwitterId($twitter_id)
    {
        return $this->setData(self::KEY_TWITTER_ID, $twitter_id);
    }

    /**
     * @inheritDoc
     */
    public function getTwActive()
    {
        return $this->_get(self::KEY_TW_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setTwActive($tw_active)
    {
        return $this->setData(self::KEY_TW_ACTIVE, $tw_active);
    }

    /**
     * @inheritDoc
     */
    public function getVimeoId()
    {
        return $this->_get(self::KEY_VIMEO_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVimeoId($vimeo_id)
    {
        return $this->setData(self::KEY_VIMEO_ID, $vimeo_id);
    }

    /**
     * @inheritDoc
     */
    public function getVimeoActive()
    {
        return $this->_get(self::KEY_VIMEO_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setVimeoActive($vimeo_active)
    {
        return $this->setData(self::KEY_VIMEO_ACTIVE, $vimeo_active);
    }

    /**
     * @inheritDoc
     */
    public function getYoutubeId()
    {
        return $this->_get(self::KEY_YOUTUBE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setYoutubeId($youtube_id)
    {
        return $this->setData(self::KEY_YOUTUBE_ID, $youtube_id);
    }

    /**
     * @inheritDoc
     */
    public function getFbActive()
    {
        return $this->_get(self::KEY_FB_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setFbActive($fb_active)
    {
        return $this->setData(self::KEY_FB_ACTIVE, $fb_active);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->_get(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        $this->setId((int)$sellerId);
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->_get(self::KEY_CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::KEY_CUSTOMER_ID, $customer_id);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->_get(self::KEY_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::KEY_EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getContactNumber()
    {
        return $this->_get(self::KEY_CONTACT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setContactNumber($contact_number)
    {
        return $this->setData(self::KEY_CONTACT_NUMBER, $contact_number);
    }

    /**
     * @inheritDoc
     */
    public function getShopTitle()
    {
        return $this->_get(self::KEY_SHOP_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setShopTitle($shop_title)
    {
        return $this->setData(self::KEY_SHOP_TITLE, $shop_title);
    }

    /**
     * @inheritDoc
     */
    public function getCompanyLocality()
    {
        return $this->_get(self::KEY_COMPANY_LOCALITY);
    }

    /**
     * @inheritDoc
     */
    public function setCompanyLocality($company_locality)
    {
        return $this->setData(self::KEY_COMPANY_LOCALITY, $company_locality);
    }

    /**
     * @inheritDoc
     */
    public function getCompany()
    {
        return $this->_get(self::KEY_COMPANY);
    }

    /**
     * @inheritDoc
     */
    public function setCompany($company)
    {
        return $this->setData(self::KEY_COMPANY, $company);
    }

    /**
     * @inheritDoc
     */
    public function getCompanyDescription()
    {
        return $this->_get(self::KEY_COMPANY_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setCompanyDescription($company_description)
    {
        return $this->setData(self::KEY_COMPANY_DESCRIPTION, $company_description);
    }

    /**
     * @inheritDoc
     */
    public function getReturnPolicy()
    {
        return $this->_get(self::KEY_RETURN_POLICY);
    }

    /**
     * @inheritDoc
     */
    public function setReturnPolicy($return_policy)
    {
        return $this->setData(self::KEY_RETURN_POLICY, $return_policy);
    }

    /**
     * @inheritDoc
     */
    public function getShippingPolicy()
    {
        return $this->_get(self::KEY_SHIPPING_POLICY);
    }

    /**
     * @inheritDoc
     */
    public function setShippingPolicy($shipping_policy)
    {
        return $this->setData(self::KEY_SHIPPING_POLICY, $shipping_policy);
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->_get(self::KEY_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setAddress($address)
    {
        return $this->setData(self::KEY_ADDRESS, $address);
    }

    /**
     * @inheritDoc
     */
    public function getCountry()
    {
        return $this->_get(self::KEY_COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry($country)
    {
        return $this->setData(self::KEY_COUNTRY, $country);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->_get(self::KEY_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::KEY_IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getThumbnail()
    {
        return $this->_get(self::KEY_THUMBNAIL);
    }

    /**
     * @inheritDoc
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::KEY_THUMBNAIL, $thumbnail);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->_get(self::KEY_CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::KEY_CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->_get(self::KEY_REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::KEY_REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->_get(self::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($region_id)
    {
        return $this->setData(self::REGION_ID, $region_id);
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return $this->_get(self::KEY_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setGroup($group)
    {
        return $this->setData(self::KEY_GROUP, $group);
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->_get(self::KEY_URL);
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        return $this->setData(self::KEY_URL, $url);
    }

    /**
     * @inheritDoc
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroupId($group_id)
    {
        return $this->setData(self::GROUP_ID, $group_id);
    }

    /**
     * @inheritDoc
     */
    public function getProductCount()
    {
        return $this->_get(self::PRODUCT_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setProductCount($product_count)
    {
        return $this->setData(self::PRODUCT_COUNT, $product_count);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->_get(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->_get(self::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($country_id)
    {
        return $this->setData(self::COUNTRY_ID, $country_id);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey($url_key)
    {
        return $this->setData(self::URL_KEY, $url_key);
    }

    /**
     * @inheritDoc
     */
    public function getCompanyUrl()
    {
        return $this->_get(self::COMPANY_URL);
    }

    /**
     * @inheritDoc
     */
    public function setCompanyUrl($company_url)
    {
        return $this->setData(self::COMPANY_URL, $company_url);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeyword()
    {
        return $this->_get(self::META_KEYWORD);
    }

    /**
     * @inheritDoc
     */
    public function setMetaKeyword($meta_keyword)
    {
        return $this->setData(self::META_KEYWORD, $meta_keyword);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription($meta_description)
    {
        return $this->setData(self::META_DESCRIPTION, $meta_description);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->_get(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function getDurationOfVendor()
    {
        return $this->_get(self::DURATION_OF_VENDOR);
    }

    /**
     * @inheritDoc
     */
    public function setDurationOfVendor($duration_of_vendor)
    {
        return $this->setData(self::DURATION_OF_VENDOR, $duration_of_vendor);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->_get(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function getTotalSold()
    {
        return $this->_get(self::TOTAL_SOLD);
    }

    /**
     * @inheritDoc
     */
    public function setTotalSold($total_sold)
    {
        return $this->setData(self::TOTAL_SOLD, $total_sold);
    }

    /**
     * @inheritDoc
     */
    public function getIsSearchable()
    {
        return $this->_get(self::IS_SEARCHABLE);
    }

    /**
     * @inheritDoc
     */
    public function setIsSearchable($is_searchable)
    {
        return $this->setData(self::IS_SEARCHABLE, $is_searchable);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeSetId()
    {
        return $this->_get(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeSetId($attribute_set_id)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attribute_set_id);
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->_get(self::LATITUDE);
    }

    /**
     * @inheritDoc
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->_get(self::LONGITUDE);
    }

    /**
     * @inheritDoc
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function getOpeningHours()
    {
        return $this->_get(self::OPENING_HOURS);
    }

    /**
     * @inheritDoc
     */
    public function setOpeningHours($opening_hours)
    {
        return $this->setData(self::OPENING_HOURS, $opening_hours);
    }

    /**
     * @inheritDoc
     */
    public function getSpecialOpeningHours()
    {
        return $this->_get(self::SPECIAL_OPENING_HOURS);
    }

    /**
     * @inheritDoc
     */
    public function setSpecialOpeningHours($special_opening_hours)
    {
        return $this->setData(self::SPECIAL_OPENING_HOURS, $special_opening_hours);
    }

    /**
     * @inheritDoc
     */
    public function getSellerRates()
    {
        return $this->_get(self::SELLER_RATES);
    }

    /**
     * @inheritDoc
     */
    public function setSellerRates($seller_rates = null)
    {
        return $this->setData(self::SELLER_RATES, $seller_rates);
    }

    /**
     * @inheritDoc
     */
    public function getSellerReviews()
    {
        return $this->_get(self::SELLER_REVIEWS);
    }

    /**
     * @inheritDoc
     */
    public function setSellerReviews($seller_reviews = null)
    {
        return $this->setData(self::SELLER_REVIEWS, $seller_reviews);
    }

    /**
     * @inheritDoc
     */
    public function getTaxvat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * @inheritDoc
     */
    public function setTaxvat($taxvat)
    {
        return $this->setData(self::TAXVAT, $taxvat);
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->_get(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setProducts($products = null)
    {
        return $this->setData(self::PRODUCTS, $products);
    }

    /**
     * @inheritDoc
     */
    public function getTotalReviews()
    {
        return $this->_get(self::TOTAL_REVIEWS);
    }

    /**
     * @inheritDoc
     */
    public function setTotalReviews($total_reviews)
    {
        return $this->setData(self::TOTAL_REVIEWS, $total_reviews);
    }

    /**
     * @inheritDoc
     */
    public function getTotalProducts()
    {
        return $this->_get(self::TOTAL_PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setTotalProducts($total_products)
    {
        return $this->setData(self::TOTAL_PRODUCTS, $total_products);
    }

    /**
     * @inheritDoc
     */
    public function getSummaryRates()
    {
        return $this->_get(self::SUMMARY_RATES);
    }

    /**
     * @inheritDoc
     */
    public function setSummaryRates($summary_rates = null)
    {
        return $this->setData(self::SUMMARY_RATES, $summary_rates);
    }

    /**
     * @inheritDoc
     */
    public function getTotalSales()
    {
        return $this->_get(self::TOTAL_SALES);
    }

    /**
     * @inheritDoc
     */
    public function setTotalSales($totalSales)
    {
        return $this->setData(self::TOTAL_SALES, $totalSales);
    }

    /**
     * @inheritDoc
     */
    public function getVacation()
    {
        return $this->_get(self::VACATION);
    }

    /**
     * @inheritDoc
     */
    public function setVacation($vacation = null)
    {
        return $this->setData(self::VACATION, $vacation);
    }

    /**
     * @inheritDoc
     */
    public function setCreationTime($creationTime = null)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @inheritDoc
     */
    public function getCreationTime()
    {
        return $this->_get(self::CREATION_TIME);
    }

    /**
     * @inheritDoc
     */
    public function getOperatingTime()
    {
        return $this->_get(self::OPERATING_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setOperatingTime($operating_time = null)
    {
        return $this->setData(self::OPERATING_TIME, $operating_time);
    }

    /**
     * @inheritDoc
     */
    public function getOrderProcessingTime()
    {
        return $this->_get(self::ORDER_PROCESSING_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setOrderProcessingTime($order_processing_time = null)
    {
        return $this->setData(self::ORDER_PROCESSING_TIME, $order_processing_time);
    }

    /**
     * @inheritDoc
     */
    public function getShippingPartners()
    {
        return $this->_get(self::SHIPPING_PARTNERS);
    }

    /**
     * @inheritDoc
     */
    public function setShippingPartners($shipping_partners = null)
    {
        return $this->setData(self::SHIPPING_PARTNERS, $shipping_partners);
    }

    /**
     * @inheritDoc
     */
    public function getOffers()
    {
        return $this->_get(self::OFFERS);
    }

    /**
     * @inheritDoc
     */
    public function setOffers($offers = null)
    {
        return $this->setData(self::OFFERS, $offers);
    }

    /**
     * @inheritDoc
     */
    public function getBenefits()
    {
        return $this->_get(self::BENEFITS);
    }

    /**
     * @inheritDoc
     */
    public function setBenefits($benefits = null)
    {
        return $this->setData(self::BENEFITS, $benefits);
    }

    /**
     * @inheritDoc
     */
    public function getProductShippingInfo()
    {
        return $this->_get(self::PRODUCT_SHIPPING_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setProductShippingInfo($product_shipping_info = null)
    {
        return $this->setData(self::PRODUCT_SHIPPING_INFO, $product_shipping_info);
    }

    /**
     * @inheritDoc
     */
    public function getPrepareTime()
    {
        return $this->_get(self::PREPARE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setPrepareTime($prepare_time = null)
    {
        return $this->setData(self::PREPARE_TIME, $prepare_time);
    }

    /**
     * @inheritDoc
     */
    public function getResponseRatio()
    {
        return $this->_get(self::RESPONSE_RATIO);
    }

    /**
     * @inheritDoc
     */
    public function setResponseRatio($response_ratio = null)
    {
        return $this->setData(self::RESPONSE_RATIO, $response_ratio);
    }

    /**
     * @inheritDoc
     */
    public function getResponseTime()
    {
        return $this->_get(self::RESPONSE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setResponseTime($response_time = null)
    {
        return $this->setData(self::RESPONSE_TIME, $response_time);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Marketplace\Api\Data\SellerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\Marketplace\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Marketplace\Api\Data\SellerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
