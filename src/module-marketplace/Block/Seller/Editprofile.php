<?php

namespace CoreMarketplace\MarketPlace\Block\Seller;

use Magento\Framework\View\Element\Template;

class Editprofile extends \Lof\MarketPlace\Block\Seller\Editprofile
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
     * @var \Magento\Directory\Model\Config\Source\Country\Full
     */
    protected $countryFull;

    /**
     * @param Template\Context $context
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Helper\Seller $sellerHelper
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Directory\Model\Config\Source\Country\Full $countryFull
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Seller $sellerHelper,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Directory\Model\Config\Source\Country\Full $countryFull,
        array $data = []
    ) {
        $this->_countryFull = $countryFull;
        $this->_country = $country;
        $this->_helper = $helper;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct(
            $context,
            $helper,
            $sellerHelper,
            $country,
            $data
        );
    }

    public function getOperatingStatusList() 
    {
        $statusList = [
            [
                'value' => 'closed',
                'label' => __('Closed')
            ],
            [
                'value' => 'open',
                'label' => __('Open')
            ]
        ];

        return $statusList;
    }

    public function getDayNames() 
    {
        $dayNames = [
            [
                'code' => 'mon',
                'label' => 'Monday'
            ],
            [
                'code' => 'tue',
                'label' => 'Tuesday'
            ],
            [
                'code' => 'wed',
                'label' => 'Wednesday'
            ],
            [
                'code' => 'thu',
                'label' => 'Thursday'
            ],
            [
                'code' => 'fri',
                'label' => 'Friday'
            ],
            [
                'code' => 'sat',
                'label' => 'Saturday'
            ],
            [
                'code' => 'sun',
                'label' => 'Sunday'
            ]
        ];

        return $dayNames;
    }

    /**
     * get list countries
     * @return array|mixed|null
     */
    public function getShipToCountries()
    {
        return $this->_countryFull->toOptionArray(true);
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
