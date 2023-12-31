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

namespace Lof\MarketPlace\ViewModel;

use Lof\MarketPlace\Helper\Seller;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Provides the user data to fill the form.
 */
class SellerRegisterDataProvider implements ArgumentInterface
{

    /**
     * @var Seller
     */
    private $helperSeller;

    /**
     * SellerRegisterDataProvider constructor.
     *
     * @param Seller $helperSeller
     */
    public function __construct(
        Seller $helperSeller
    ) {
        $this->helperSeller = $helperSeller;
    }

    /**
     * Get seller email
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->helperSeller->getPostValue('email') ?: $this->helperSeller->getUserEmail();
    }

    /**
     * Get seller first name
     *
     * @return string
     */
    public function getUserFirstName()
    {
        return $this->helperSeller->getPostValue('firstname');
    }

    /**
     * Get seller last name
     *
     * @return string
     */
    public function getUserLastName()
    {
        return $this->helperSeller->getPostValue('lastname');
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->helperSeller->getPostValue('prefix');
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->helperSeller->getPostValue('middlename');
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->helperSeller->getPostValue('suffix');
    }

    /**
     * Get seller country id
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->helperSeller->getPostValue('country_id') ?: $this->helperSeller->getUserCountryId();
    }

    /**
     * Get seller shop title
     *
     * @return string
     */
    public function getShopTitle()
    {
        return $this->helperSeller->getPostValue('shop_title');
    }

    /**
     * Get seller url
     *
     * @return string
     */
    public function getSellerUrl()
    {
        return $this->helperSeller->getPostValue('url');
    }

    /**
     * Get seller company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->helperSeller->getPostValue('company') ?: $this->helperSeller->getUserCompany();
    }

    /**
     * Get seller telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        if (str_contains($this->helperSeller->getPostValue('telephone'), "+")) {
            $telephone = $this->helperSeller->getPostValue('telephone');
        } else {
            $telephone = $this->getCountryDialCode() . $this->helperSeller->getPostValue('telephone');
        }

        return $telephone ?: $this->helperSeller->getUserTelephone();
    }

    /**
     * Get seller country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->helperSeller->getPostValue('country_dial_code');
    }

    /**
     * Get seller country dial code
     *
     * @return string
     */
    public function getCountryDialCode()
    {
        if ($this->getCountryCode()) {
            $countryDialCode = $this->helperSeller->getCountryPhoneCode(strtoupper($this->getCountryCode()));
        } else {
            $countryDialCode = '';
        }
        $countryDialCode = str_replace("(", "", $countryDialCode);
        $countryDialCode = str_replace(")", "", $countryDialCode);

        return $countryDialCode;
    }

    /**
     * Get seller street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->helperSeller->getPostValue("post_street") ?: $this->helperSeller->getUserStreet();
    }

    /**
     * Get seller region id
     *
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->helperSeller->getPostValue("region_id") ?: $this->helperSeller->getUserRegionId();
    }

    /**
     * Get seller region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->helperSeller->getPostValue('region') ?: $this->helperSeller->getUserRegion();
    }

    /**
     * Get seller postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->helperSeller->getPostValue('postcode') ?: $this->helperSeller->getUserPostcode();
    }

    /**
     * Get seller city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->helperSeller->getPostValue('city') ?: $this->helperSeller->getUserCity();
    }
}
