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

namespace Lof\MarketPlace\Block\Seller;

use Magento\Framework\View\Element\Template;

class Editprofile extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\MarketPlace\Helper\Seller
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @param Template\Context $context
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Helper\Seller $sellerHelper
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Seller $sellerHelper,
        \Magento\Directory\Model\Config\Source\Country $country,
        array $data = []
    ) {
        $this->_country = $country;
        $this->_helper = $helper;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct($context, $data);
    }

    /**
     * get list countries
     * @return array|mixed|null
     */
    public function getCountries($default_country_code = "US")
    {
        $default_country_code = $default_country_code ? $default_country_code : "US";
        return $this->_country->toOptionArray(false, $default_country_code);
    }

    /**
     * @return array|mixed|null
     */
    public function getSeller()
    {
        return $this->_sellerHelper->getSellerByCustomer();
    }

    /**
     * @return array|mixed|null
     */
    public function getCustomer()
    {
        return $this->_sellerHelper->getCurrentCustomer();
    }

    /**
     * @return Editprofile
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Edit Profile'));
        return parent::_prepareLayout();
    }
}
