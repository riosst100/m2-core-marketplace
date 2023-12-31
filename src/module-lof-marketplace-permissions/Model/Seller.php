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

namespace Lof\MarketPermissions\Model;

use Lof\MarketPermissions\Api\Data\SellerInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Seller extends AbstractExtensibleModel implements SellerInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Seller::class);
    }

    /**
     * @return mixed|string|null
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @param string $sellerId
     * @return SellerInterface|Seller
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @return mixed|string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return SellerInterface|Seller
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return mixed|string|null
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @param string $urlKey
     * @return SellerInterface|Seller
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * @return mixed|string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return SellerInterface|Seller
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return mixed|string|null
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @param string $groupId
     * @return SellerInterface|Seller
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * @return mixed|string|null
     */
    public function getSale()
    {
        return $this->getData(self::SALE);
    }

    /**
     * @param string $sale
     * @return SellerInterface|Seller
     */
    public function setSale($sale)
    {
        return $this->setData(self::SALE, $sale);
    }

    /**
     * @return mixed|string|null
     */
    public function getCommissionId()
    {
        return $this->getData(self::COMMISSION_ID);
    }

    /**
     * @param string $commissionId
     * @return SellerInterface|Seller
     */
    public function setCommissionId($commissionId)
    {
        return $this->setData(self::COMMISSION_ID, $commissionId);
    }

    /**
     * @return mixed|string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param string $image
     * @return SellerInterface|Seller
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return mixed|string|null
     */
    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * @param string $thumbnail
     * @return SellerInterface|Seller
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * @return mixed|string|null
     */
    public function getPageTitle()
    {
        return $this->getData(self::PAGE_TITLE);
    }

    /**
     * @param string $pageTitle
     * @return SellerInterface|Seller
     */
    public function setPageTitle($pageTitle)
    {
        return $this->setData(self::PAGE_TITLE, $pageTitle);
    }

    /**
     * @return mixed|string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORD);
    }

    /**
     * @param string $metaKeywords
     * @return SellerInterface|Seller
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORD, $metaKeywords);
    }

    /**
     * @return mixed|string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * @param string $creationTime
     * @return SellerInterface|Seller
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @return mixed|string|null
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @param string $updateTime
     * @return SellerInterface|Seller
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @return mixed|string|null
     */
    public function getPageLayout()
    {
        return $this->getData(self::PAGE_LAYOUT);
    }

    /**
     * @param string $pageLayout
     * @return SellerInterface|Seller
     */
    public function setPageLayout($pageLayout)
    {
        return $this->setData(self::PAGE_LAYOUT, $pageLayout);
    }

    /**
     * @return mixed|string|null
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * @param string $address
     * @return SellerInterface|Seller
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * @return mixed|string|null
     */
    public function getLayoutUpdateXml()
    {
        return $this->getData(self::LAYOUT_UPDATE_XML);
    }

    /**
     * @param string $layoutUpdateXml
     * @return SellerInterface|Seller
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * @return mixed|string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return SellerInterface|Seller
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return mixed|string|null
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @param string $position
     * @return SellerInterface|Seller
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @return mixed|string|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param string $customerId
     * @return SellerInterface|Seller
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return mixed|string|null
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @param string $email
     * @return SellerInterface|Seller
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @return mixed|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return SellerInterface|Seller
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return mixed|string|null
     */
    public function getPaymentSource()
    {
        return $this->getData(self::PAYMENT_SOURCE);
    }

    /**
     * @param string $paymentSource
     * @return SellerInterface|Seller
     */
    public function setPaymentSource($paymentSource)
    {
        return $this->setData(self::PAYMENT_SOURCE, $paymentSource);
    }

    /**
     * @return mixed|string|null
     */
    public function getTwitterId()
    {
        return $this->getData(self::TWITTER_ID);
    }

    /**
     * @param string $twitterId
     * @return SellerInterface|Seller
     */
    public function setTwitterId($twitterId)
    {
        return $this->setData(self::TWITTER_ID, $twitterId);
    }

    /**
     * @return mixed|string|null
     */
    public function getFacebookId()
    {
        return $this->getData(self::FACEBOOK_ID);
    }

    /**
     * @param string $facebookId
     * @return SellerInterface|Seller
     */
    public function setFacebookId($facebookId)
    {
        return $this->setData(self::FACEBOOK_ID, $facebookId);
    }

    /**
     * @return mixed|string|null
     */
    public function getGplusId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @param string $gplusId
     * @return SellerInterface|Seller
     */
    public function setGplusId($gplusId)
    {
        return $this->setData(self::GROUP_ID, $gplusId);
    }

    /**
     * @return mixed|string|null
     */
    public function getYoutubeId()
    {
        return $this->getData(self::YOUTUBE_ID);
    }

    /**
     * @param string $youtubeId
     * @return SellerInterface|Seller
     */
    public function setYoutubeId($youtubeId)
    {
        return $this->setData(self::YOUTUBE_ID, $youtubeId);
    }

    /**
     * @return mixed|string|null
     */
    public function getVimeoId()
    {
        return $this->getData(self::VIMEO_ID);
    }

    /**
     * @param string $vimeoId
     * @return SellerInterface|Seller
     */
    public function setVimeoId($vimeoId)
    {
        return $this->setData(self::VIMEO_ID, $vimeoId);
    }

    /**
     * @return mixed|string|null
     */
    public function getInstagramId()
    {
        return $this->getData(self::INSTAGRAM_ID);
    }

    /**
     * @param string $instagramId
     * @return SellerInterface|Seller
     */
    public function setInstagramId($instagramId)
    {
        return $this->setData(self::INSTAGRAM_ID, $instagramId);
    }

    /**
     * @return mixed|string|null
     */
    public function getPinterestId()
    {
        return $this->getData(self::PINTEREST_ID);
    }

    /**
     * @param string $pinterestId
     * @return SellerInterface|Seller
     */
    public function setPinterestId($pinterestId)
    {
        return $this->setData(self::PINTEREST_ID, $pinterestId);
    }

    /**
     * @return mixed|string|null
     */
    public function getLinkedinId()
    {
        return $this->getData(self::LINKEDIN_ID);
    }

    /**
     * @param string $linkedinId
     * @return SellerInterface|Seller
     */
    public function setLinkedinId($linkedinId)
    {
        return $this->setData(self::LINKEDIN_ID, $linkedinId);
    }

    /**
     * @return mixed|string|null
     */
    public function getTwActive()
    {
        return $this->getData(self::LINKEDIN_ID);
    }

    /**
     * @param string $twActive
     * @return SellerInterface|Seller
     */
    public function setTwActive($twActive)
    {
        return $this->setData(self::TW_ACTIVE, $twActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getFbActive()
    {
        return $this->getData(self::FB_ACTIVE);
    }

    /**
     * @param string $fbActive
     * @return SellerInterface|Seller
     */
    public function setFbActive($fbActive)
    {
        return $this->setData(self::FB_ACTIVE, $fbActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getGplusActive()
    {
        return $this->getData(self::GPLUS_ACTIVE);
    }

    /**
     * @param string $gplusActive
     * @return SellerInterface|Seller
     */
    public function setGplusActive($gplusActive)
    {
        return $this->setData(self::GPLUS_ACTIVE, $gplusActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getYoutubeActive()
    {
        return $this->getData(self::YOUTUBE_ACTIVE);
    }

    /**
     * @param string $youtubeActive
     * @return SellerInterface|Seller
     */
    public function setYoutubeActive($youtubeActive)
    {
        return $this->setData(self::YOUTUBE_ACTIVE, $youtubeActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getVimeoActive()
    {
        return $this->getData(self::VIMEO_ACTIVE);
    }

    /**
     * @param string $vimeoActive
     * @return SellerInterface|Seller
     */
    public function setVimeoActive($vimeoActive)
    {
        return $this->setData(self::VIMEO_ACTIVE, $vimeoActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getInstagramActive()
    {
        return $this->getData(self::INSTAGRAM_ACTIVE);
    }

    /**
     * @param string $instagramActive
     * @return SellerInterface|Seller
     */
    public function setInstagramActive($instagramActive)
    {
        return $this->setData(self::INSTAGRAM_ACTIVE, $instagramActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getPinterestActive()
    {
        return $this->getData(self::PINTEREST_ACTIVE);
    }

    /**
     * @param string $pinterestActive
     * @return SellerInterface|Seller
     */
    public function setPinterestActive($pinterestActive)
    {
        return $this->setData(self::PINTEREST_ACTIVE, $pinterestActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getLinkedinActive()
    {
        return $this->getData(self::LINKEDIN_ACTIVE);
    }

    /**
     * @param string $linkedinActive
     * @return SellerInterface|Seller
     */
    public function setLinkedinActive($linkedinActive)
    {
        return $this->setData(self::LINKEDIN_ACTIVE, $linkedinActive);
    }

    /**
     * @return mixed|string|null
     */
    public function getOthersInfo()
    {
        return $this->getData(self::OTHERS_INFO);
    }

    /**
     * @param string $othersInfo
     * @return SellerInterface|Seller
     */
    public function setOthersInfo($othersInfo)
    {
        return $this->setData(self::OTHERS_INFO, $othersInfo);
    }

    /**
     * @return mixed|string|null
     */
    public function getBannerPic()
    {
        return $this->getData(self::BANNER_PIC);
    }

    /**
     * @param string $bannerPic
     * @return SellerInterface|Seller
     */
    public function setBannerPic($bannerPic)
    {
        return $this->setData(self::BANNER_PIC, $bannerPic);
    }

    /**
     * @return mixed|string|null
     */
    public function getShopUrl()
    {
        return $this->getData(self::SHOP_URL);
    }

    /**
     * @param string $shopUrl
     * @return SellerInterface|Seller
     */
    public function setShopUrl($shopUrl)
    {
        return $this->setData(self::SHOP_URL, $shopUrl);
    }

    /**
     * @return mixed|string|null
     */
    public function getShopTitle()
    {
        return $this->getData(self::SHOP_TITLE);
    }

    /**
     * @param string $shopTitle
     * @return SellerInterface|Seller
     */
    public function setShopTitle($shopTitle)
    {
        return $this->setData(self::SHOP_TITLE, $shopTitle);
    }

    /**
     * @return mixed|string|null
     */
    public function getLogoPic()
    {
        return $this->getData(self::LOGO_PIC);
    }

    /**
     * @param string $logoPic
     * @return SellerInterface|Seller
     */
    public function setLogoPic($logoPic)
    {
        return $this->setData(self::LOGO_PIC, $logoPic);
    }

    /**
     * @return mixed|string|null
     */
    public function getCompanyLocality()
    {
        return $this->getData(self::COMPANY_LOCALITY);
    }

    /**
     * @param string $companyLocality
     * @return SellerInterface|Seller
     */
    public function setCompanyLocality($companyLocality)
    {
        return $this->setData(self::COMPANY_LOCALITY, $companyLocality);
    }

    /**
     * @return mixed|string|null
     */
    public function getCountryPic()
    {
        return $this->getData(self::COUNTRY_PIC);
    }

    /**
     * @param string $countryPic
     * @return SellerInterface|Seller
     */
    public function setCountryPic($countryPic)
    {
        return $this->setData(self::COUNTRY_PIC, $countryPic);
    }

    /**
     * @return mixed|string|null
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @param string $country
     * @return SellerInterface|Seller
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @return mixed|string|null
     */
    public function getCompanyDescription()
    {
        return $this->getData(self::COMPANY_DESCRIPTION);
    }

    /**
     * @param string $companyDescription
     * @return SellerInterface|Seller
     */
    public function setCompanyDescription($companyDescription)
    {
        return $this->setData(self::COMPANY_DESCRIPTION, $companyDescription);
    }

    /**
     * @return mixed|string|null
     */
    public function getMetaKeyword()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * @param string $metaKeyword
     * @return SellerInterface|Seller
     */
    public function setMetaKeyword($metaKeyword)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeyword);
    }

    /**
     * @return mixed|string|null
     */
    public function getBackgroundWidth()
    {
        return $this->getData(self::BACKGROUND_WIDTH);
    }

    /**
     * @param string $backgroundWidth
     * @return SellerInterface|Seller
     */
    public function setBackgroundWidth($backgroundWidth)
    {
        return $this->setData(self::BACKGROUND_WIDTH, $backgroundWidth);
    }

    /**
     * @return mixed|string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * @param string $metaDescription
     * @return SellerInterface|Seller
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * @return mixed|string|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param string $storeId
     * @return SellerInterface|Seller
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @return mixed|string|null
     */
    public function getContactNumber()
    {
        return $this->getData(self::CONTACT_NUMBER);
    }

    /**
     * @param string $contactNumber
     * @return SellerInterface|Seller
     */
    public function setContactNumber($contactNumber)
    {
        return $this->setData(self::CONTACT_NUMBER, $contactNumber);
    }

    /**
     * @return mixed|string|null
     */
    public function getReturnPolicy()
    {
        return $this->getData(self::CONTACT_NUMBER);
    }

    /**
     * @param string $returnPolicy
     * @return SellerInterface|Seller
     */
    public function setReturnPolicy($returnPolicy)
    {
        return $this->setData(self::RETURN_POLICY, $returnPolicy);
    }

    /**
     * @return mixed|string|null
     */
    public function getShippingPolicy()
    {
        return $this->getData(self::SHIPPING_POLICY);
    }

    /**
     * @param string $shippingPolicy
     * @return SellerInterface|Seller
     */
    public function setShippingPolicy($shippingPolicy)
    {
        return $this->setData(self::SHIPPING_POLICY, $shippingPolicy);
    }

    /**
     * @return mixed|string|null
     */
    public function getPageId()
    {
        return $this->getData(self::PAGE_ID);
    }

    /**
     * @param string $pageId
     * @return SellerInterface|Seller
     */
    public function setPageId($pageId)
    {
        return $this->setData(self::PAGE_ID, $pageId);
    }

    /**
     * @return mixed|string|null
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @param string $countryId
     * @return SellerInterface|Seller
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @return mixed|string|null
     */
    public function getVerifyStatus()
    {
        return $this->getData(self::VERIFY_STATUS);
    }

    /**
     * @param string $verifyStatus
     * @return SellerInterface|Seller
     */
    public function setVerifyStatus($verifyStatus)
    {
        return $this->setData(self::VERIFY_STATUS, $verifyStatus);
    }

    /**
     * @return mixed|string|null
     */
    public function getCompany()
    {
        return $this->getData(self::COMPANY);
    }

    /**
     * @param string $company
     * @return SellerInterface|Seller
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * @return mixed|string|null
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @param string $city
     * @return SellerInterface|Seller
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @return mixed|string|null
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @param string $region
     * @return SellerInterface|Seller
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @return mixed|string|null
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @param string $street
     * @return SellerInterface|Seller
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @return mixed|string|null
     */
    public function getProductCount()
    {
        return $this->getData(self::PRODUCT_COUNT);
    }

    /**
     * @param string $productCount
     * @return SellerInterface|Seller
     */
    public function setProductCount($productCount)
    {
        return $this->setData(self::PRODUCT_COUNT, $productCount);
    }

    /**
     * @return mixed|string|null
     */
    public function getDurationOfVendor()
    {
        return $this->getData(self::DURATION_OF_VENDOR);
    }

    /**
     * @param string $durationOfVendor
     * @return SellerInterface|Seller
     */
    public function setDurationOfVendor($durationOfVendor)
    {
        return $this->setData(self::DURATION_OF_VENDOR, $durationOfVendor);
    }

    /**
     * @return mixed|string|null
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @param string $regionId
     * @return SellerInterface|Seller
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @return mixed|string|null
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @param string $postcode
     * @return SellerInterface|Seller
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @return mixed|string|null
     */
    public function getSaleCompletedCount()
    {
        return $this->getData(self::SALE_COMPLETED_COUNT);
    }

    /**
     * @param string $saleCompletedCount
     * @return SellerInterface|Seller
     */
    public function setSaleCompletedCount($saleCompletedCount)
    {
        return $this->setData(self::SALE_COMPLETED_COUNT, $saleCompletedCount);
    }

    /**
     * @return mixed|string|null
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @param string $telephone
     * @return SellerInterface|Seller
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @return mixed|string|null
     */
    public function getTotalSold()
    {
        return $this->getData(self::TOTAL_SOLD);
    }

    /**
     * @param string $totalSold
     * @return SellerInterface|Seller
     */
    public function setTotalSold($totalSold)
    {
        return $this->setData(self::TOTAL_SOLD, $totalSold);
    }

    /**
     * @return mixed|null
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param $customerGroupId
     * @return Seller
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerGroupId);
    }

    /**
     * @return mixed|string|null
     */
    public function getJobTitle()
    {
        return $this->getData(self::JOB_TITLE);
    }

    /**
     * @param string $jobTitle
     * @return SellerInterface|Seller
     */
    public function setJobTitle($jobTitle)
    {
        return $this->setData(self::JOB_TITLE, $jobTitle);
    }

    /**
     * @return \Lof\MarketPermissions\Api\Data\SellerExtensionInterface|\Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        if (!$this->_getExtensionAttributes()) {
            $this->setExtensionAttributes(
                $this->extensionAttributesFactory->create(get_class($this))
            );
        }
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return Seller
     */
    public function setExtensionAttributes(\Lof\MarketPermissions\Api\Data\SellerExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
