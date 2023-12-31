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

use Lof\MarketPlace\Api\Data\RegisterSellerInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @codeCoverageIgnore
 */
class RegisterSeller extends AbstractExtensibleObject implements RegisterSellerInterface
{
    /**
     * @inheritDoc
     */
    public function getShopUrl()
    {
        return $this->_get(self::SHOP_URL);
    }

    /**
     * @inheritDoc
     */
    public function setShopUrl($shop_url)
    {
        return $this->setData(self::SHOP_URL, $shop_url);
    }

    /**
     * @inheritDoc
     */
    public function getContactNumber()
    {
        return $this->_get(self::CONTACT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setContactNumber($contact_number)
    {
        return $this->setData(self::CONTACT_NUMBER, $contact_number);
    }

    /**
     * @inheritDoc
     */
    public function getCompany()
    {
        return $this->_get(self::COMPANY);
    }

    /**
     * @inheritDoc
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->_get(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
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
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\Marketplace\Api\Data\RegisterSellerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
