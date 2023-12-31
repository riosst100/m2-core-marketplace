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

namespace Lof\MarketPlace\Model\Config\Shipping;

use Magento\Framework\Api\AttributeValueFactory;

// @phpstan-ignore-next-line
class Address extends \Lof\MarketPlace\Model\FlatAbstractModel
{
    /**
     * @var string
     */
    protected $_code = 'address';

    /**
     * @var array
     */
    protected $_fields = [];

    /**
     * @var string
     */
    protected $_codeSeparator = '-';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketplaceHelperData;
    /**
     * @var \Lof\MarketPlace\Helper\Seller
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Seller|mixed|array|object|null
     */
    protected $_currentSeller = null;

    /**
     * Address constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelperData
     * @param \Lof\MarketPlace\Helper\Seller $sellerHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData,
        \Lof\MarketPlace\Helper\Seller $sellerHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_objectManager = $objectInterface;
        $this->_marketplaceHelperData = $marketplaceHelperData;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId) {
            return $this->_marketplaceHelperData->getStore($storeId);
        } else {
            return $this->_marketplaceHelperData->getStore();
        }
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Get the code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }
    /**
     * Get the code separator
     *
     * @return string
     */
    public function getCodeSeparator()
    {
        return $this->_codeSeparator;
    }
    /**
     * @return \Lof\MarketPlace\Model\Seller|array|mixed|null
     */
    public function getSeller()
    {
        if (!$this->_currentSeller) {
            return $this->_sellerHelper->getSellerByCustomer();
        } else {
            return $this->_currentSeller;
        }
    }

    /**
     * @param \Lof\MarketPlace\Model\Seller|mixed|object|array
     * @return $this
     */
    public function setSeller($seller = null)
    {
        $this->_currentSeller = $seller;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $seller = $this->getSeller();
        $countries = $this->_objectManager->get(\Magento\Config\Model\Config\Source\Locale\Country::class)
            ->toOptionArray();
        if ($seller) {
            $sellerCountry = [
                0 => [
                    "value" => $seller['country_id'],
                    "label" => $seller['country']
                ]
            ];
            $countries = array_merge($sellerCountry, $countries);
            $this->_fields = [];
            $this->_fields['country_id'] = [
                'type' => 'select',
                'required' => true,
                'values' => $countries
            ];
            $this->_fields['region_id'] = [
                'type' => 'select',
                'required' => true,
                'values' => [
                    [
                        'label' => $seller['region'],
                        'value' => $seller['region_id']
                    ]
                ]
            ];
            $this->_fields['region'] = [
                'type' => 'text',
                'required' => true,
                'values' => $seller['region']
            ];
            $this->_fields['city'] = [
                'type' => 'text',
                'required' => true,
                'values' => $seller['city']
            ];
            $this->_fields['postcode'] = [
                'type' => 'text',
                'required' => true,
                'values' => $seller['postcode']
            ];
            $this->_fields['postcode']['after_element_html'] = "";
        } else {
            $this->_fields['country_id'] = [
                'type' => 'select',
                'required' => true,
                'values' => $countries
            ];
            $this->_fields['region_id'] = [
                'type' => 'select',
                'required' => true,
                'values' => [['label' => __('Please select region, state or province'), 'value' => '']]
            ];
            $this->_fields['region'] = ['type' => 'text', 'required' => true];
            $this->_fields['city'] = ['type' => 'text', 'required' => true];
            $this->_fields['postcode'] = ['type' => 'text', 'required' => true];
            $this->_fields['postcode']['after_element_html'] = "";
        }

        return $this->_fields;
    }

    /**
     * @param $key
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel($key)
    {
        switch ($key) {
            case 'label':
                return __('Origin Shipping Address');
            case 'country_id':
                return __('Country');
            case 'region_id':
                return __('State/Province');
            case 'region':
                return '';
            case 'city':
                return __('City');
            case 'postcode':
                return __('Zip/Postal Code');
            default:
                return $key;
        }
    }
}
