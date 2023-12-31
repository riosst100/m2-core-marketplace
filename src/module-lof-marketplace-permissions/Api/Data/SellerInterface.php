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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Api\Data;

interface SellerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const SELLER_ID = 'seller_id';
    const NAME = 'name';
    const URL_KEY = 'url_key';
    const DESCRIPTION = 'description';
    const GROUP_ID = 'group_id';
    const SALE = 'sale';
    const COMMISSION_ID = 'commission_id';
    const IMAGE = 'image';
    const THUMBNAIL = 'thumbnail';
    const PAGE_TITLE = 'page_title';
    const META_KEYWORDS = 'meta_keywords';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const PAGE_LAYOUT = 'page_layout';
    const ADDRESS = 'address';
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
    const STATUS = 'status';
    const POSITION = 'position';
    const CUSTOMER_ID = 'customer_id';
    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const PAYMENT_SOURCE = 'payment_source';
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
    const YOUTUBE_ACTIVE = 'youtube_active';
    const VIMEO_ACTIVE = 'vimeo_active';
    const INSTAGRAM_ACTIVE = 'instagram_active';
    const PINTEREST_ACTIVE = 'pinterest_active';
    const LINKEDIN_ACTIVE = 'linkedin_active';
    const OTHERS_INFO = 'others_info';
    const BANNER_PIC = 'banner_pic';
    const SHOP_URL = 'shop_url';
    const SHOP_TITLE = 'shop_title';
    const LOGO_PIC = 'logo_pic';
    const COMPANY_LOCALITY = 'company_locality';
    const COUNTRY_PIC = 'country_pic';
    const COUNTRY = 'country';
    const COMPANY_DESCRIPTION = 'company_description';
    const META_KEYWORD = 'meta_keyword';
    const BACKGROUND_WIDTH = 'background_width';
    const META_DESCRIPTION = 'meta_description';
    const STORE_ID = 'store_id';
    const CONTACT_NUMBER = 'contact_number';
    const RETURN_POLICY = 'return_policy';
    const SHIPPING_POLICY = 'shipping_policy';
    const PAGE_ID = 'page_id';
    const COUNTRY_ID = 'country_id';
    const VERIFY_STATUS = 'verify_status';
    const COMPANY = 'company';
    const CITY = 'city';
    const REGION = 'region';
    const STREET = 'street';
    const PRODUCT_COUNT = 'product_count';
    const DURATION_OF_VENDOR = 'duration_of_vendor';
    const REGION_ID = 'region_id';
    const POSTCODE = 'postcode';
    const SALE_COMPLETED_COUNT = 'sale_completed_count';
    const TELEPHONE = 'telephone';
    const TOTAL_SOLD = 'total_sold';
    const JOB_TITLE = 'job_title';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const STATUS_VERIFY = 1;
    const STATUS_UNVERIFY = 0;

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Id.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setId($id);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setName($name);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setUrlKey($urlKey);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setDescription($description);

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGroupId($groupId);

    /**
     * Get sale
     * @return string|null
     */
    public function getSale();

    /**
     * Set sale
     * @param string $sale
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSale($sale);

    /**
     * Get commission_id
     * @return string|null
     */
    public function getCommissionId();

    /**
     * Set commission_id
     * @param string $commissionId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCommissionId($commissionId);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setImage($image);

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * Get page_title
     * @return string|null
     */
    public function getPageTitle();

    /**
     * Set page_title
     * @param string $pageTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageTitle($pageTitle);

    /**
     * Get meta_keywords
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set meta_keywords
     * @param string $metaKeywords
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation_time
     * @param string $creationTime
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update_time
     * @param string $updateTime
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout();

    /**
     * Set page_layout
     * @param string $pageLayout
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageLayout($pageLayout);

    /**
     * Get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setAddress($address);

    /**
     * Get layout_update_xml
     * @return string|null
     */
    public function getLayoutUpdateXml();

    /**
     * Set layout_update_xml
     * @param string $layoutUpdateXml
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStatus($status);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPosition($position);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setEmail($email);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get payment_source
     * @return string|null
     */
    public function getPaymentSource();

    /**
     * Set payment_source
     * @param string $paymentSource
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPaymentSource($paymentSource);

    /**
     * Get twitter_id
     * @return string|null
     */
    public function getTwitterId();

    /**
     * Set twitter_id
     * @param string $twitterId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTwitterId($twitterId);

    /**
     * Get facebook_id
     * @return string|null
     */
    public function getFacebookId();

    /**
     * Set facebook_id
     * @param string $facebookId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setFacebookId($facebookId);

    /**
     * Get gplus_id
     * @return string|null
     */
    public function getGplusId();

    /**
     * Set gplus_id
     * @param string $gplusId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGplusId($gplusId);

    /**
     * Get youtube_id
     * @return string|null
     */
    public function getYoutubeId();

    /**
     * Set youtube_id
     * @param string $youtubeId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setYoutubeId($youtubeId);

    /**
     * Get vimeo_id
     * @return string|null
     */
    public function getVimeoId();

    /**
     * Set vimeo_id
     * @param string $vimeoId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVimeoId($vimeoId);

    /**
     * Get instagram_id
     * @return string|null
     */
    public function getInstagramId();

    /**
     * Set instagram_id
     * @param string $instagramId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setInstagramId($instagramId);

    /**
     * Get pinterest_id
     * @return string|null
     */
    public function getPinterestId();

    /**
     * Set pinterest_id
     * @param string $pinterestId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPinterestId($pinterestId);

    /**
     * Get linkedin_id
     * @return string|null
     */
    public function getLinkedinId();

    /**
     * Set linkedin_id
     * @param string $linkedinId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLinkedinId($linkedinId);

    /**
     * Get tw_active
     * @return string|null
     */
    public function getTwActive();

    /**
     * Set tw_active
     * @param string $twActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTwActive($twActive);

    /**
     * Get fb_active
     * @return string|null
     */
    public function getFbActive();

    /**
     * Set fb_active
     * @param string $fbActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setFbActive($fbActive);

    /**
     * Get gplus_active
     * @return string|null
     */
    public function getGplusActive();

    /**
     * Set gplus_active
     * @param string $gplusActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGplusActive($gplusActive);

    /**
     * Get youtube_active
     * @return string|null
     */
    public function getYoutubeActive();

    /**
     * Set youtube_active
     * @param string $youtubeActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setYoutubeActive($youtubeActive);

    /**
     * Get vimeo_active
     * @return string|null
     */
    public function getVimeoActive();

    /**
     * Set vimeo_active
     * @param string $vimeoActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVimeoActive($vimeoActive);

    /**
     * Get instagram_active
     * @return string|null
     */
    public function getInstagramActive();

    /**
     * Set instagram_active
     * @param string $instagramActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setInstagramActive($instagramActive);

    /**
     * Get pinterest_active
     * @return string|null
     */
    public function getPinterestActive();

    /**
     * Set pinterest_active
     * @param string $pinterestActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPinterestActive($pinterestActive);

    /**
     * Get linkedin_active
     * @return string|null
     */
    public function getLinkedinActive();

    /**
     * Set linkedin_active
     * @param string $linkedinActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLinkedinActive($linkedinActive);

    /**
     * Get others_info
     * @return string|null
     */
    public function getOthersInfo();

    /**
     * Set others_info
     * @param string $othersInfo
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setOthersInfo($othersInfo);

    /**
     * Get banner_pic
     * @return string|null
     */
    public function getBannerPic();

    /**
     * Set banner_pic
     * @param string $bannerPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setBannerPic($bannerPic);

    /**
     * Get shop_url
     * @return string|null
     */
    public function getShopUrl();

    /**
     * Set shop_url
     * @param string $shopUrl
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShopUrl($shopUrl);

    /**
     * Get shop_title
     * @return string|null
     */
    public function getShopTitle();

    /**
     * Set shop_title
     * @param string $shopTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShopTitle($shopTitle);

    /**
     * Get logo_pic
     * @return string|null
     */
    public function getLogoPic();

    /**
     * Set logo_pic
     * @param string $logoPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLogoPic($logoPic);

    /**
     * Get company_locality
     * @return string|null
     */
    public function getCompanyLocality();

    /**
     * Set company_locality
     * @param string $companyLocality
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompanyLocality($companyLocality);

    /**
     * Get country_pic
     * @return string|null
     */
    public function getCountryPic();

    /**
     * Set country_pic
     * @param string $countryPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountryPic($countryPic);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountry($country);

    /**
     * Get company_description
     * @return string|null
     */
    public function getCompanyDescription();

    /**
     * Set company_description
     * @param string $companyDescription
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompanyDescription($companyDescription);

    /**
     * Get meta_keyword
     * @return string|null
     */
    public function getMetaKeyword();

    /**
     * Set meta_keyword
     * @param string $metaKeyword
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaKeyword($metaKeyword);

    /**
     * Get background_width
     * @return string|null
     */
    public function getBackgroundWidth();

    /**
     * Set background_width
     * @param string $backgroundWidth
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setBackgroundWidth($backgroundWidth);

    /**
     * Get meta_description
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta_description
     * @param string $metaDescription
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStoreId($storeId);

    /**
     * Get contact_number
     * @return string|null
     */
    public function getContactNumber();

    /**
     * Set contact_number
     * @param string $contactNumber
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setContactNumber($contactNumber);

    /**
     * Get return_policy
     * @return string|null
     */
    public function getReturnPolicy();

    /**
     * Set return_policy
     * @param string $returnPolicy
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setReturnPolicy($returnPolicy);

    /**
     * Get shipping_policy
     * @return string|null
     */
    public function getShippingPolicy();

    /**
     * Set shipping_policy
     * @param string $shippingPolicy
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShippingPolicy($shippingPolicy);

    /**
     * Get page_id
     * @return string|null
     */
    public function getPageId();

    /**
     * Set page_id
     * @param string $pageId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageId($pageId);

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     * @param string $countryId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountryId($countryId);

    /**
     * Get verify_status
     * @return string|null
     */
    public function getVerifyStatus();

    /**
     * Set verify_status
     * @param string $verifyStatus
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVerifyStatus($verifyStatus);

    /**
     * Get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompany($company);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCity($city);

    /**
     * Get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setRegion($region);

    /**
     * Get street
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     * @param string $street
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStreet($street);

    /**
     * Get product_count
     * @return string|null
     */
    public function getProductCount();

    /**
     * Set product_count
     * @param string $productCount
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setProductCount($productCount);

    /**
     * Get duration_of_vendor
     * @return string|null
     */
    public function getDurationOfVendor();

    /**
     * Set duration_of_vendor
     * @param string $durationOfVendor
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setDurationOfVendor($durationOfVendor);

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param string $regionId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setRegionId($regionId);

    /**
     * Get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPostcode($postcode);

    /**
     * Get sale_completed_count
     * @return string|null
     */
    public function getSaleCompletedCount();

    /**
     * Set sale_completed_count
     * @param string $saleCompletedCount
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSaleCompletedCount($saleCompletedCount);

    /**
     * Get telephone
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     * @param string $telephone
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTelephone($telephone);

    /**
     * Get total_sold
     * @return string|null
     */
    public function getTotalSold();

    /**
     * Set total_sold
     * @param string $totalSold
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTotalSold($totalSold);

    /**
     * Get job_title
     * @return string|null
     */
    public function getJobTitle();

    /**
     * Set job_title
     * @param string $jobTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setJobTitle($jobTitle);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPermissions\Api\Data\SellerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes
    );
}
