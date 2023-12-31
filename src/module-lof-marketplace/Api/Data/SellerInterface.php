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

namespace Lof\MarketPlace\Api\Data;

interface SellerInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const ID = 'id';
    const SELLER_ID = 'seller_id';
    const CONTACT_NUMBER = 'contact_number';
    const SHOP_TITLE = 'shop_title';
    const COMPANY_LOCALITY = 'company_locality';
    const COMPANY = 'company';
    const COMPANY_DESCRIPTION = 'company_description';
    const RETURN_POLICY = 'return_policy';
    const SHIPPING_POLICY = 'shipping_policy';
    const ADDRESS = 'address';
    const COUNTRY = 'country';
    const IMAGE = 'image';
    const THUMBNAIL = 'thumbnail';
    const CREATION_TIME = 'creation_time';
    const CITY = 'city';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const GROUP = 'group';
    const VERIFY_STATUS = 'verify_status';
    const URL = 'url';
    const URL_KEY = 'url_key';
    const CUSTOMER_ID = 'customer_id';
    const EMAIL = 'email';
    const NAME = 'name';
    const SALE = 'sale';
    const COMMISSION_ID = 'commission_id';
    const PAGE_LAYOUT = 'page_layout';
    const STATUS = 'status';
    const POSITION = 'position';
    const TWITTER_ID = 'twitter_id';
    const FACEBOOK_ID = 'facebook_id';
    const GPLUS_ID = 'gplus_id';
    const YOUTUBE_ID = 'youtube_id';
    const VIMEO_ID = 'vimeo_id';
    const INSTAGRAM_ID = 'instagram_id';
    const PINTEREST_ID = 'pinterest_id';
    const LINKEDIN_ID = 'linkedin_id';
    const TW_ACTIVE = 'tw_active';
    const FB_ACTIVE = 'fb_active';
    const GPLUS_ACTIVE = 'gplus_active';
    const VIMEO_ACTIVE = 'vimeo_active';
    const INSTAGRAM_ACTIVE = 'instagram_active';
    const PINTEREST_ACTIVE = 'pinterest_active';
    const LINKEDIN_ACTIVE = 'linkedin_active';
    const BANNER_PIC = 'banner_pic';
    const SHOP_URL = 'shop_url';
    const LOGO_PIC = 'logo_pic';
    const STORE_ID = 'store_id';
    const GROUP_ID = 'group_id';
    const PRODUCT_COUNT = 'product_count';
    const POSTCODE = 'postcode';
    const SELLER_RATES = 'seller_rates';
    const PRODUCTS = 'products';
    const COUNTRY_ID = 'country_id';
    const MESSAGE = 'message';
    const COMPANY_URL = 'company_url';
    const META_KEYWORD = 'meta_keyword';
    const META_DESCRIPTION = 'meta_description';
    const STREET = 'street';
    const DURATION_OF_VENDOR = 'duration_of_vendor';
    const TELEPHONE = 'telephone';
    const TOTAL_SOLD = 'total_sold';
    const TOTAL_PRODUCTS = 'total_products';
    const TOTAL_REVIEWS = 'total_reviews';
    const IS_SEARCHABLE = 'is_searchable';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const OPENING_HOURS = 'opening_hours';
    const SPECIAL_OPENING_HOURS = 'special_opening_hours';
    const TAXVAT = 'taxvat';
    const SELLER_REVIEWS = 'seller_reviews';
    const SUMMARY_RATES = 'summary_rates';
    const TOTAL_SALES = 'total_sales';
    const VACATION = 'vacation';
    const OPERATING_TIME = 'operating_time';
    const ORDER_PROCESSING_TIME = 'order_processing_time';
    const SHIPPING_PARTNERS = 'shipping_partners';
    const OFFERS = 'offers';
    const BENEFITS = 'benefits';
    const PRODUCT_SHIPPING_INFO = 'product_shipping_info';
    const PREPARE_TIME = 'prepare_time';
    const RESPONSE_RATIO = 'response_ratio';
    const RESPONSE_TIME = 'response_time';
    const CREATED_AT = 'created_at';

    const KEY_SELLER_ID = 'seller_id';
    const KEY_CONTACT_NUMBER = 'contact_number';
    const KEY_SHOP_TITLE = 'shop_title';
    const KEY_COMPANY_LOCALITY = 'company_locality';
    const KEY_COMPANY = 'company';
    const KEY_COMPANY_DESCRIPTION = 'company_description';
    const KEY_RETURN_POLICY = 'return_policy';
    const KEY_SHIPPING_POLICY = 'shipping_policy';
    const KEY_ADDRESS = 'address';
    const KEY_COUNTRY = 'country';
    const KEY_IMAGE = 'image';
    const KEY_THUMBNAIL = 'thumbnail';
    const KEY_CITY = 'city';
    const KEY_REGION = 'region';
    const KEY_GROUP = 'group';
    const KEY_URL = 'url';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_EMAIL = 'email';
    const KEY_NAME = 'name';
    const KEY_SALE = 'sale';
    const KEY_COMMISSION_ID = 'commission_id';
    const KEY_PAGE_LAYOUT = 'page_layout';
    const KEY_STTAUS = 'status';
    const KEY_POSITION = 'position';
    const KEY_TWITTER_ID = 'twitter_id';
    const KEY_FACEBOOK_ID = 'facebook_id';
    const KEY_GPLUS_ID = 'gplus_id';
    const KEY_YOUTUBE_ID = 'youtube_id';
    const KEY_VIMEO_ID = 'vimeo_id';
    const KEY_INSTAGRAM_ID = 'instagram_id';
    const KEY_PINTEREST_ID = 'pinterest_id';
    const KEY_LINKEDIN_ID = 'linkedin_id';
    const KEY_TW_ACTIVE = 'tw_active';
    const KEY_FB_ACTIVE = 'fb_active';
    const KEY_GPLUS_ACTIVE = 'gplus_active';
    const KEY_VIMEO_ACTIVE = 'vimeo_active';
    const KEY_INSTAGRAM_ACTIVE = 'instagram_active';
    const KEY_PINTEREST_ACTIVE = 'pinterest_active';
    const KEY_LINKEDIN_ACTIVE = 'linkedin_active';
    const KEY_BANNER_PIC = 'banner_pic';
    const KEY_SHOP_URL = 'shop_url';
    const KEY_LOGO_PIC = 'logo_pic';
    const KEY_STORE_ID = 'store_id';
    const KEY_URL_KEY = 'url_key';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setId($id);

    /**
     * Get sale
     * @return string|null
     */
    public function getSale();

    /**
     * Set sale
     * @param string $sale
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSale($sale);

    /**
     * Get commission_id
     * @return int|null
     */
    public function getCommissionId();

    /**
     * Set commission_id
     * @param int $commission_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCommissionId($commission_id);

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout();

    /**
     * Set page_layout
     * @param string $page_layout
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPageLayout($page_layout);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setStatus($status);

    /**
     * Get verify_status
     * @return int|null
     */
    public function getVerifyStatus();

    /**
     * Set verify_status
     * @param int $verify_status
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setVerifyStatus($verify_status);

    /**
     * Get position
     * @return int|null
     */
    public function getPosition();

    /**
     * Set position
     * @param int $position
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPosition($position);

    /**
     * Get twitter_id
     * @return string|null
     */
    public function getTwitterId();

    /**
     * Set twitter_id
     * @param string $twitter_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTwitterId($twitter_id);

    /**
     * Get facebook_id
     * @return string|null
     */
    public function getFacebookId();

    /**
     * Set facebook_id
     * @param string $facebook_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setFacebookId($facebook_id);

    /**
     * Get gplus_id
     * @return string|null
     */
    public function getGplusId();

    /**
     * Set gplus_id
     * @param string $gplus_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setGplusId($gplus_id);

    /**
     * Get youtube_id
     * @return string|null
     */
    public function getYoutubeId();

    /**
     * Set youtube_id
     * @param string $youtube_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setYoutubeId($youtube_id);

    /**
     * Get vimeo_id
     * @return string|null
     */
    public function getVimeoId();

    /**
     * Set vimeo_id
     * @param string $vimeo_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setVimeoId($vimeo_id);

    /**
     * Get instagram_id
     * @return string|null
     */
    public function getInstagramId();

    /**
     * Set instagram_id
     * @param string $instagram_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setInstagramId($instagram_id);

    /**
     * Get pinterest_id
     * @return string|null
     */
    public function getPinterestId();

    /**
     * Set pinterest_id
     * @param string $pinterest_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPinterestId($pinterest_id);

    /**
     * Get linkedin_id
     * @return string|null
     */
    public function getLinkedinId();

    /**
     * Set linkedin_id
     * @param string $linkedin_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setLinkedinId($linkedin_id);

    /**
     * Get tw_active
     * @return string|null
     */
    public function getTwActive();

    /**
     * Set tw_active
     * @param string $tw_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTwActive($tw_active);

    /**
     * Get fb_active
     * @return string|null
     */
    public function getFbActive();

    /**
     * Set fb_active
     * @param string $fb_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setFbActive($fb_active);

    /**
     * Get gplus_active
     * @return string|null
     */
    public function getGplusActive();

    /**
     * Set gplus_active
     * @param string $gplus_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setGplusActive($gplus_active);

    /**
     * Get vimeo_active
     * @return string|null
     */
    public function getVimeoActive();

    /**
     * Set vimeo_active
     * @param string $vimeo_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setVimeoActive($vimeo_active);

    /**
     * Get instagram_active
     * @return string|null
     */
    public function getInstagramActive();

    /**
     * Set instagram_active
     * @param string $instagram_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setInstagramActive($instagram_active);

    /**
     * Get pinterest_active
     * @return string|null
     */
    public function getPinterestActive();

    /**
     * Set pinterest_active
     * @param string $pinterest_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPinterestActive($pinterest_active);

    /**
     * Get linkedin_active
     * @return string|null
     */
    public function getLinkedinActive();

    /**
     * Set linkedin_active
     * @param string $linkedin_active
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setLinkedinActive($linkedin_active);

    /**
     * Get banner_pic
     * @return string|null
     */
    public function getBannerPic();

    /**
     * Set banner_pic
     * @param string $banner_pic
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setBannerPic($banner_pic);

    /**
     * Get shop_url
     * @return string|null
     */
    public function getShopUrl();

    /**
     * Set shop_url
     * @param string $shop_url
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setShopUrl($shop_url);

    /**
     * Get logo_pic
     * @return string|null
     */
    public function getLogoPic();

    /**
     * Set logo_pic
     * @param string $logo_pic
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setLogoPic($logo_pic);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $store_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setStoreId($store_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSellerId($sellerId);

    /**
     * get contact_number
     * @return string|null
     */
    public function getContactNumber();

    /**
     * Set contact_number
     * @param string $contact_number
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setContactNumber($contact_number);

    /**
     * get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customer_id
     * @return string|null
     */
    public function setCustomerId($customer_id);

    /**
     * get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setName($name);

    /**
     * get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setEmail($email);

    /**
     * get shop_title
     * @return string|null
     */
    public function getShopTitle();

    /**
     * Set shop_title
     * @param string $shop_title
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setShopTitle($shop_title);

    /**
     * get company_locality
     * @return string|null
     */
    public function getCompanyLocality();

    /**
     * Set company_locality
     * @param string $company_locality
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCompanyLocality($company_locality);

    /**
     * get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCompany($company);

    /**
     * get company_description
     * @return string|null
     */
    public function getCompanyDescription();

    /**
     * Set company_description
     * @param string $company_description
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCompanyDescription($company_description);

    /**
     * get return_policy
     * @return string|null
     */
    public function getReturnPolicy();

    /**
     * Set return_policy
     * @param string $return_policy
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setReturnPolicy($return_policy);

    /**
     * get shipping_policy
     * @return string|null
     */
    public function getShippingPolicy();

    /**
     * Set shipping_policy
     * @param string $shipping_policy
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setShippingPolicy($shipping_policy);

    /**
     * get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setAddress($address);

    /**
     * get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set Country
     * @param string $country
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCountry($country);

    /**
     * get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setImage($image);

    /**
     * get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setRegion($region);

    /**
     * get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region
     * @param string $region_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setRegionId($region_id);

    /**
     * get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCity($city);

    /**
     * get group
     * @return string|null
     */
    public function getGroup();

    /**
     * Set group
     * @param string $group
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setGroup($group);

    /**
     * get url
     * @return string|null
     */
    public function getUrl();

    /**
     * Set Url
     * @param string $url
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setUrl($url);

    /**
     * get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $group_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setGroupId($group_id);

    /**
     * get product_count
     * @return int|null
     */
    public function getProductCount();

    /**
     * Set product_count
     * @param int $product_count
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setProductCount($product_count);

    /**
     * get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPostcode($postcode);

    /**
     * get country_id
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     * @param string $country_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCountryId($country_id);

    /**
     * get company_url
     * @return string|null
     */
    public function getCompanyUrl();

    /**
     * Set company_url
     * @param string $company_url
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCompanyUrl($company_url);

    /**
     * get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setMessage($message);

    /**
     * get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $url_key
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setUrlKey($url_key);

    /**
     * get meta_keyword
     * @return string|null
     */
    public function getMetaKeyword();

    /**
     * Set meta_keyword
     * @param string $meta_keyword
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setMetaKeyword($meta_keyword);

    /**
     * get meta_description
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta_description
     * @param string $meta_description
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setMetaDescription($meta_description);

    /**
     * get street
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     * @param string $street
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setStreet($street);

    /**
     * get duration_of_vendor
     * @return string|null
     */
    public function getDurationOfVendor();

    /**
     * Set duration_of_vendor
     * @param string $duration_of_vendor
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setDurationOfVendor($duration_of_vendor);

    /**
     * get telephone
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     * @param string $telephone
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTelephone($telephone);

    /**
     * get total_sold
     * @return int|float|null
     */
    public function getTotalSold();

    /**
     * Set total_sold
     * @param int|float $total_sold
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTotalSold($total_sold);

    /**
     * get is_searchable
     * @return int|null
     */
    public function getIsSearchable();

    /**
     * Set is_searchable
     * @param int $is_searchable
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setIsSearchable($is_searchable);

    /**
     * get attribute_set_id
     * @return int|null
     */
    public function getAttributeSetId();

    /**
     * Set attribute_set_id
     * @param int $attribute_set_id
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setAttributeSetId($attribute_set_id);

    /**
     * get latitude
     * @return float|int|null
     */
    public function getLatitude();

    /**
     * Set latitude
     * @param float|int $latitude
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setLatitude($latitude);

    /**
     * get longitude
     * @return float|int|null
     */
    public function getLongitude();

    /**
     * Set longitude
     * @param float|int $longitude
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setLongitude($longitude);

    /**
     * get opening_hours
     * @return string|string[]|null
     */
    public function getOpeningHours();

    /**
     * Set opening_hours
     * @param string|string[] $opening_hours
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setOpeningHours($opening_hours);

    /**
     * get special_opening_hours
     * @return string|string[]|null
     */
    public function getSpecialOpeningHours();

    /**
     * Set special_opening_hours
     * @param string|string[] $special_opening_hours
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSpecialOpeningHours($special_opening_hours);

    /**
     * get taxvat
     * @return string|null
     */
    public function getTaxvat();

    /**
     * Set taxvat
     * @param string|null $taxvat
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTaxvat($taxvat);

    /**
     * Set creation_time
     * @param string $creationTime
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Get creation_time
     * @return string
     */
    public function getCreationTime();

    /**
     * get seller_rates
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface|null
     */
    public function getSellerRates();

    /**
     * Set seller_rates
     * @param \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface|null $seller_rates
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSellerRates($seller_rates = null);

    /**
     * get seller_reviews
     * @return \Lof\MarketPlace\Api\Data\ReviewSearchResultsInterface|null
     */
    public function getSellerReviews();

    /**
     * Set seller_reviews
     * @param \Lof\MarketPlace\Api\Data\ReviewSearchResultsInterface|null $seller_reviews
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSellerReviews($seller_reviews = null);

    /**
     * get products
     * @return \Lof\MarketPlace\Api\Data\SellerProductSearchResultsInterface|null
     */
    public function getProducts();

    /**
     * Set products
     * @param \Lof\MarketPlace\Api\Data\SellerProductSearchResultsInterface|null $products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setProducts($products = null);

    /**
     * get total_reviews
     * @return int
     */
    public function getTotalReviews();

    /**
     * Set total_reviews
     * @param int $total_reviews
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTotalReviews($total_reviews);

    /**
     * get total_products
     * @return int
     */
    public function getTotalProducts();

    /**
     * Set total_products
     * @param int $total_products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTotalProducts($total_products);

    /**
     * get summary_rates
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface|null
     */
    public function getSummaryRates();

    /**
     * Set summary_rates
     * @param \Lof\MarketPlace\Api\Data\SummaryRatingInterface|null $summary_rates
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setSummaryRates($summary_rates = null);

    /**
     * get totalSales
     * @return int
     */
    public function getTotalSales();

    /**
     * Set totalSales
     * @param int $totalSales
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setTotalSales($totalSales);

    /**
     * get vacation
     * @return \Lof\MarketPlace\Api\Data\SellerVacationInterface|null
     */
    public function getVacation();

    /**
     * Set vacation
     * @param \Lof\MarketPlace\Api\Data\SellerVacationInterface|null $vacation
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setVacation($vacation = null);

    /**
     * get operating_time
     * @return string|null
     */
    public function getOperatingTime();

    /**
     * Set operating_time
     * @param string|null $operating_time
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setOperatingTime($operating_time);

    /**
     * get order_processing_time
     * @return string|null
     */
    public function getOrderProcessingTime();

    /**
     * Set order_processing_time
     * @param string|null $order_processing_time
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setOrderProcessingTime($order_processing_time);

    /**
     * get shipping_partners
     * @return string|null
     */
    public function getShippingPartners();

    /**
     * Set shipping_partners
     * @param string|null $shipping_partners
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setShippingPartners($shipping_partners);

    /**
     * get offers
     * @return string|null
     */
    public function getOffers();

    /**
     * Set offers
     * @param string|null $offers
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setOffers($offers);

    /**
     * get benefits
     * @return string|null
     */
    public function getBenefits();

    /**
     * Set benefits
     * @param string|null $benefits
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setBenefits($benefits);

    /**
     * get product_shipping_info
     * @return string|null
     */
    public function getProductShippingInfo();

    /**
     * Set product_shipping_info
     * @param string|null $product_shipping_info
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setProductShippingInfo($product_shipping_info);

    /**
     * get prepare_time
     * @return string|null
     */
    public function getPrepareTime();

    /**
     * Set prepare_time
     * @param string|null $prepare_time
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setPrepareTime($prepare_time);

    /**
     * get response_ratio
     * @return string|null
     */
    public function getResponseRatio();

    /**
     * Set response_ratio
     * @param string|null $response_ratio
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setResponseRatio($response_ratio);

    /**
     * get response_time
     * @return string|null
     */
    public function getResponseTime();

    /**
     * Set response_time
     * @param string|null $response_time
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setResponseTime($response_time);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPlace\Api\Data\SellerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPlace\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setExtensionAttributes(
        \Lof\MarketPlace\Api\Data\SellerExtensionInterface $extensionAttributes
    );
}
