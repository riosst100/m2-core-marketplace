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

namespace Lof\MarketPermissions\Model\Data;

use Lof\MarketPermissions\Api\Data\SellerInterface;

class Seller extends \Magento\Framework\Api\AbstractExtensibleObject implements SellerInterface
{

    /**
     * Get id
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::SELLER_ID);
    }

    /**
     * Get id
     * @return Seller
     */
    public function setId($id)
    {
        return $this->setData(self::SELLER_ID, $id);
    }

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId()
    {
        return $this->_get(self::SELLER_ID);
    }

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPermissions\Api\Data\SellerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Get sale
     * @return string|null
     */
    public function getSale()
    {
        return $this->_get(self::SALE);
    }

    /**
     * Set sale
     * @param string $sale
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSale($sale)
    {
        return $this->setData(self::SALE, $sale);
    }

    /**
     * Get commission_id
     * @return string|null
     */
    public function getCommissionId()
    {
        return $this->_get(self::COMMISSION_ID);
    }

    /**
     * Set commission_id
     * @param string $commissionId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCommissionId($commissionId)
    {
        return $this->setData(self::COMMISSION_ID, $commissionId);
    }

    /**
     * Get image
     * @return string|null
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * Set image
     * @param string $image
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->_get(self::THUMBNAIL);
    }

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * Get page_title
     * @return string|null
     */
    public function getPageTitle()
    {
        return $this->_get(self::PAGE_TITLE);
    }

    /**
     * Set page_title
     * @param string $pageTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageTitle($pageTitle)
    {
        return $this->setData(self::PAGE_TITLE, $pageTitle);
    }

    /**
     * Get meta_keywords
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->_get(self::META_KEYWORDS);
    }

    /**
     * Set meta_keywords
     * @param string $metaKeywords
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->_get(self::CREATION_TIME);
    }

    /**
     * Set creation_time
     * @param string $creationTime
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->_get(self::UPDATE_TIME);
    }

    /**
     * Set update_time
     * @param string $updateTime
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout()
    {
        return $this->_get(self::PAGE_LAYOUT);
    }

    /**
     * Set page_layout
     * @param string $pageLayout
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageLayout($pageLayout)
    {
        return $this->setData(self::PAGE_LAYOUT, $pageLayout);
    }

    /**
     * Get address
     * @return string|null
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * Set address
     * @param string $address
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Get layout_update_xml
     * @return string|null
     */
    public function getLayoutUpdateXml()
    {
        return $this->_get(self::LAYOUT_UPDATE_XML);
    }

    /**
     * Set layout_update_xml
     * @param string $layoutUpdateXml
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get position
     * @return string|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Set position
     * @param string $position
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get payment_source
     * @return string|null
     */
    public function getPaymentSource()
    {
        return $this->_get(self::PAYMENT_SOURCE);
    }

    /**
     * Set payment_source
     * @param string $paymentSource
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPaymentSource($paymentSource)
    {
        return $this->setData(self::PAYMENT_SOURCE, $paymentSource);
    }

    /**
     * Get twitter_id
     * @return string|null
     */
    public function getTwitterId()
    {
        return $this->_get(self::TWITTER_ID);
    }

    /**
     * Set twitter_id
     * @param string $twitterId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTwitterId($twitterId)
    {
        return $this->setData(self::TWITTER_ID, $twitterId);
    }

    /**
     * Get facebook_id
     * @return string|null
     */
    public function getFacebookId()
    {
        return $this->_get(self::FACEBOOK_ID);
    }

    /**
     * Set facebook_id
     * @param string $facebookId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setFacebookId($facebookId)
    {
        return $this->setData(self::FACEBOOK_ID, $facebookId);
    }

    /**
     * Get gplus_id
     * @return string|null
     */
    public function getGplusId()
    {
        return $this->_get(self::GPLUS_ID);
    }

    /**
     * Set gplus_id
     * @param string $gplusId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGplusId($gplusId)
    {
        return $this->setData(self::GPLUS_ID, $gplusId);
    }

    /**
     * Get youtube_id
     * @return string|null
     */
    public function getYoutubeId()
    {
        return $this->_get(self::YOUTUBE_ID);
    }

    /**
     * Set youtube_id
     * @param string $youtubeId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setYoutubeId($youtubeId)
    {
        return $this->setData(self::YOUTUBE_ID, $youtubeId);
    }

    /**
     * Get vimeo_id
     * @return string|null
     */
    public function getVimeoId()
    {
        return $this->_get(self::VIMEO_ID);
    }

    /**
     * Set vimeo_id
     * @param string $vimeoId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVimeoId($vimeoId)
    {
        return $this->setData(self::VIMEO_ID, $vimeoId);
    }

    /**
     * Get instagram_id
     * @return string|null
     */
    public function getInstagramId()
    {
        return $this->_get(self::INSTAGRAM_ID);
    }

    /**
     * Set instagram_id
     * @param string $instagramId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setInstagramId($instagramId)
    {
        return $this->setData(self::INSTAGRAM_ID, $instagramId);
    }

    /**
     * Get pinterest_id
     * @return string|null
     */
    public function getPinterestId()
    {
        return $this->_get(self::PINTEREST_ID);
    }

    /**
     * Set pinterest_id
     * @param string $pinterestId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPinterestId($pinterestId)
    {
        return $this->setData(self::PINTEREST_ID, $pinterestId);
    }

    /**
     * Get linkedin_id
     * @return string|null
     */
    public function getLinkedinId()
    {
        return $this->_get(self::LINKEDIN_ID);
    }

    /**
     * Set linkedin_id
     * @param string $linkedinId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLinkedinId($linkedinId)
    {
        return $this->setData(self::LINKEDIN_ID, $linkedinId);
    }

    /**
     * Get tw_active
     * @return string|null
     */
    public function getTwActive()
    {
        return $this->_get(self::TW_ACTIVE);
    }

    /**
     * Set tw_active
     * @param string $twActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTwActive($twActive)
    {
        return $this->setData(self::TW_ACTIVE, $twActive);
    }

    /**
     * Get fb_active
     * @return string|null
     */
    public function getFbActive()
    {
        return $this->_get(self::FB_ACTIVE);
    }

    /**
     * Set fb_active
     * @param string $fbActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setFbActive($fbActive)
    {
        return $this->setData(self::FB_ACTIVE, $fbActive);
    }

    /**
     * Get gplus_active
     * @return string|null
     */
    public function getGplusActive()
    {
        return $this->_get(self::GPLUS_ACTIVE);
    }

    /**
     * Set gplus_active
     * @param string $gplusActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setGplusActive($gplusActive)
    {
        return $this->setData(self::GPLUS_ACTIVE, $gplusActive);
    }

    /**
     * Get youtube_active
     * @return string|null
     */
    public function getYoutubeActive()
    {
        return $this->_get(self::YOUTUBE_ACTIVE);
    }

    /**
     * Set youtube_active
     * @param string $youtubeActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setYoutubeActive($youtubeActive)
    {
        return $this->setData(self::YOUTUBE_ACTIVE, $youtubeActive);
    }

    /**
     * Get vimeo_active
     * @return string|null
     */
    public function getVimeoActive()
    {
        return $this->_get(self::VIMEO_ACTIVE);
    }

    /**
     * Set vimeo_active
     * @param string $vimeoActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVimeoActive($vimeoActive)
    {
        return $this->setData(self::VIMEO_ACTIVE, $vimeoActive);
    }

    /**
     * Get instagram_active
     * @return string|null
     */
    public function getInstagramActive()
    {
        return $this->_get(self::INSTAGRAM_ACTIVE);
    }

    /**
     * Set instagram_active
     * @param string $instagramActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setInstagramActive($instagramActive)
    {
        return $this->setData(self::INSTAGRAM_ACTIVE, $instagramActive);
    }

    /**
     * Get pinterest_active
     * @return string|null
     */
    public function getPinterestActive()
    {
        return $this->_get(self::PINTEREST_ACTIVE);
    }

    /**
     * Set pinterest_active
     * @param string $pinterestActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPinterestActive($pinterestActive)
    {
        return $this->setData(self::PINTEREST_ACTIVE, $pinterestActive);
    }

    /**
     * Get linkedin_active
     * @return string|null
     */
    public function getLinkedinActive()
    {
        return $this->_get(self::LINKEDIN_ACTIVE);
    }

    /**
     * Set linkedin_active
     * @param string $linkedinActive
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLinkedinActive($linkedinActive)
    {
        return $this->setData(self::LINKEDIN_ACTIVE, $linkedinActive);
    }

    /**
     * Get others_info
     * @return string|null
     */
    public function getOthersInfo()
    {
        return $this->_get(self::OTHERS_INFO);
    }

    /**
     * Set others_info
     * @param string $othersInfo
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setOthersInfo($othersInfo)
    {
        return $this->setData(self::OTHERS_INFO, $othersInfo);
    }

    /**
     * Get banner_pic
     * @return string|null
     */
    public function getBannerPic()
    {
        return $this->_get(self::BANNER_PIC);
    }

    /**
     * Set banner_pic
     * @param string $bannerPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setBannerPic($bannerPic)
    {
        return $this->setData(self::BANNER_PIC, $bannerPic);
    }

    /**
     * Get shop_url
     * @return string|null
     */
    public function getShopUrl()
    {
        return $this->_get(self::SHOP_URL);
    }

    /**
     * Set shop_url
     * @param string $shopUrl
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShopUrl($shopUrl)
    {
        return $this->setData(self::SHOP_URL, $shopUrl);
    }

    /**
     * Get shop_title
     * @return string|null
     */
    public function getShopTitle()
    {
        return $this->_get(self::SHOP_TITLE);
    }

    /**
     * Set shop_title
     * @param string $shopTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShopTitle($shopTitle)
    {
        return $this->setData(self::SHOP_TITLE, $shopTitle);
    }

    /**
     * Get logo_pic
     * @return string|null
     */
    public function getLogoPic()
    {
        return $this->_get(self::LOGO_PIC);
    }

    /**
     * Set logo_pic
     * @param string $logoPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setLogoPic($logoPic)
    {
        return $this->setData(self::LOGO_PIC, $logoPic);
    }

    /**
     * Get company_locality
     * @return string|null
     */
    public function getCompanyLocality()
    {
        return $this->_get(self::COMPANY_LOCALITY);
    }

    /**
     * Set company_locality
     * @param string $companyLocality
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompanyLocality($companyLocality)
    {
        return $this->setData(self::COMPANY_LOCALITY, $companyLocality);
    }

    /**
     * Get country_pic
     * @return string|null
     */
    public function getCountryPic()
    {
        return $this->_get(self::COUNTRY_PIC);
    }

    /**
     * Set country_pic
     * @param string $countryPic
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountryPic($countryPic)
    {
        return $this->setData(self::COUNTRY_PIC, $countryPic);
    }

    /**
     * Get country
     * @return string|null
     */
    public function getCountry()
    {
        return $this->_get(self::COUNTRY);
    }

    /**
     * Set country
     * @param string $country
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * Get company_description
     * @return string|null
     */
    public function getCompanyDescription()
    {
        return $this->_get(self::COMPANY_DESCRIPTION);
    }

    /**
     * Set company_description
     * @param string $companyDescription
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompanyDescription($companyDescription)
    {
        return $this->setData(self::COMPANY_DESCRIPTION, $companyDescription);
    }

    /**
     * Get meta_keyword
     * @return string|null
     */
    public function getMetaKeyword()
    {
        return $this->_get(self::META_KEYWORD);
    }

    /**
     * Set meta_keyword
     * @param string $metaKeyword
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaKeyword($metaKeyword)
    {
        return $this->setData(self::META_KEYWORD, $metaKeyword);
    }

    /**
     * Get background_width
     * @return string|null
     */
    public function getBackgroundWidth()
    {
        return $this->_get(self::BACKGROUND_WIDTH);
    }

    /**
     * Set background_width
     * @param string $backgroundWidth
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setBackgroundWidth($backgroundWidth)
    {
        return $this->setData(self::BACKGROUND_WIDTH, $backgroundWidth);
    }

    /**
     * Get meta_description
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * Set meta_description
     * @param string $metaDescription
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get contact_number
     * @return string|null
     */
    public function getContactNumber()
    {
        return $this->_get(self::CONTACT_NUMBER);
    }

    /**
     * Set contact_number
     * @param string $contactNumber
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setContactNumber($contactNumber)
    {
        return $this->setData(self::CONTACT_NUMBER, $contactNumber);
    }

    /**
     * Get return_policy
     * @return string|null
     */
    public function getReturnPolicy()
    {
        return $this->_get(self::RETURN_POLICY);
    }

    /**
     * Set return_policy
     * @param string $returnPolicy
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setReturnPolicy($returnPolicy)
    {
        return $this->setData(self::RETURN_POLICY, $returnPolicy);
    }

    /**
     * Get shipping_policy
     * @return string|null
     */
    public function getShippingPolicy()
    {
        return $this->_get(self::SHIPPING_POLICY);
    }

    /**
     * Set shipping_policy
     * @param string $shippingPolicy
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setShippingPolicy($shippingPolicy)
    {
        return $this->setData(self::SHIPPING_POLICY, $shippingPolicy);
    }

    /**
     * Get page_id
     * @return string|null
     */
    public function getPageId()
    {
        return $this->_get(self::PAGE_ID);
    }

    /**
     * Set page_id
     * @param string $pageId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPageId($pageId)
    {
        return $this->setData(self::PAGE_ID, $pageId);
    }

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->_get(self::COUNTRY_ID);
    }

    /**
     * Set country_id
     * @param string $countryId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * Get verify_status
     * @return string|null
     */
    public function getVerifyStatus()
    {
        return $this->_get(self::VERIFY_STATUS);
    }

    /**
     * Set verify_status
     * @param string $verifyStatus
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setVerifyStatus($verifyStatus)
    {
        return $this->setData(self::VERIFY_STATUS, $verifyStatus);
    }

    /**
     * Get company
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get(self::COMPANY);
    }

    /**
     * Set company
     * @param string $company
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * Get city
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get region
     * @return string|null
     */
    public function getRegion()
    {
        return $this->_get(self::REGION);
    }

    /**
     * Set region
     * @param string $region
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Get street
     * @return string|null
     */
    public function getStreet()
    {
        return $this->_get(self::STREET);
    }

    /**
     * Set street
     * @param string $street
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * Get product_count
     * @return string|null
     */
    public function getProductCount()
    {
        return $this->_get(self::PRODUCT_COUNT);
    }

    /**
     * Set product_count
     * @param string $productCount
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setProductCount($productCount)
    {
        return $this->setData(self::PRODUCT_COUNT, $productCount);
    }

    /**
     * Get duration_of_vendor
     * @return string|null
     */
    public function getDurationOfVendor()
    {
        return $this->_get(self::DURATION_OF_VENDOR);
    }

    /**
     * Set duration_of_vendor
     * @param string $durationOfVendor
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setDurationOfVendor($durationOfVendor)
    {
        return $this->setData(self::DURATION_OF_VENDOR, $durationOfVendor);
    }

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId()
    {
        return $this->_get(self::REGION_ID);
    }

    /**
     * Set region_id
     * @param string $regionId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Get postcode
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get(self::POSTCODE);
    }

    /**
     * Set postcode
     * @param string $postcode
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Get sale_completed_count
     * @return string|null
     */
    public function getSaleCompletedCount()
    {
        return $this->_get(self::SALE_COMPLETED_COUNT);
    }

    /**
     * Set sale_completed_count
     * @param string $saleCompletedCount
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setSaleCompletedCount($saleCompletedCount)
    {
        return $this->setData(self::SALE_COMPLETED_COUNT, $saleCompletedCount);
    }

    /**
     * Get telephone
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get(self::TELEPHONE);
    }

    /**
     * Set telephone
     * @param string $telephone
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Get total_sold
     * @return string|null
     */
    public function getTotalSold()
    {
        return $this->_get(self::TOTAL_SOLD);
    }

    /**
     * Set total_sold
     * @param string $totalSold
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setTotalSold($totalSold)
    {
        return $this->setData(self::TOTAL_SOLD, $totalSold);
    }

    /**
     * Get job_title
     * @return string|null
     */
    public function getJobTitle()
    {
        return $this->_get(self::JOB_TITLE);
    }

    /**
     * Set job_title
     * @param string $jobTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function setJobTitle($jobTitle)
    {
        return $this->setData(self::JOB_TITLE, $jobTitle);
    }
}
