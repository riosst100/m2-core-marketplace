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

interface RegisterSellerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CONTACT_NUMBER = 'contact_number';
    const TELEPHONE = 'telephone';
    const COMPANY = 'company';
    const ADDRESS = 'address';
    const CITY = 'city';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const SHOP_URL = 'shop_url';
    const GROUP_ID = 'group_id';
    const POSTCODE = 'postcode';
    const COUNTRY_ID = 'country_id';

    /**
     * Get shop_url
     * @return string|null
     */
    public function getShopUrl();

    /**
     * Set shop_url
     * @param string $shop_url
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setShopUrl($shop_url);

    /**
     * get contact_number
     * @return string|null
     */
    public function getContactNumber();

    /**
     * Set contact_number
     * @param string $contact_number
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setContactNumber($contact_number);

    /**
     * get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setCompany($company);

    /**
     * get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setAddress($address);

    /**
     * get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setRegion($region);

    /**
     * get region_id
     * @return int|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param int $region_id
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
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
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setCity($city);

    /**
     * get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $group_id
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setGroupId($group_id);

    /**
     * get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
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
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setCountryId($country_id);

    /**
     * get telephone
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     * @param string $telephone
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerInterface
     */
    public function setTelephone($telephone);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPlace\Api\Data\RegisterSellerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPlace\Api\Data\RegisterSellerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\MarketPlace\Api\Data\RegisterSellerExtensionInterface $extensionAttributes
    );
}
