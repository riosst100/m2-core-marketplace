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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Block\Shipping;

use Magento\Customer\Model\Customer;
use Magento\Catalog\Block\Product\AbstractProduct;
use Lofmp\TableRateShipping\Model\ShippingmethodFactory;
use Magento\Directory\Model\ResourceModel\Country;
use Lofmp\TableRateShipping\Model\ShippingFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipping extends AbstractProduct
{
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_urlHelper;

    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var ShippingFactory
     */
    protected $_mpshippingModel;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param Customer $customer
     * @param \Magento\Customer\Model\Session $session
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param Country\CollectionFactory $countryCollectionFactory
     * @param ShippingFactory $mpshippingModel
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        Customer $customer,
        \Magento\Customer\Model\Session $session,
        ShippingmethodFactory $shippingmethodFactory,
        Country\CollectionFactory $countryCollectionFactory,
        ShippingFactory $mpshippingModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_postDataHelper = $postDataHelper;
        $this->_urlHelper = $urlHelper;
        $this->_customer = $customer;
        $this->_session = $session;
        $this->request = $context->getRequest();
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_mpshippingModel = $mpshippingModel;
    }

    /**
     * @return false|mixed|string
     */
    public function getShippingId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_session->getCustomerId();
    }

    /**
     * @param $shipping_id
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShipping($shipping_id)
    {
        $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter('lofmpshipping_id', $shipping_id);
        return $querydata;
    }

    /**
     * @param int $partnerId
     * @return \Lofmp\TableRateShipping\Model\Shipping
     */
    public function getShippingCollection($partnerId = null)
    {
        $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShippingMethodCollection()
    {
        $shippingMethodCollection = $this->_mpshippingMethod
            ->create()
            ->getCollection();
        return $shippingMethodCollection;
    }

    /**
     * @return \Lofmp\TableRateShipping\Model\Shippingmethod
     */
    public function getShippingMethod()
    {
        $shippingMethodCollection = $this->_mpshippingMethod
            ->create();
        return $shippingMethodCollection;
    }

    /**
     * @param $methodId
     * @param $partnerId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShippingforShippingMethod($methodId, $partnerId)
    {
        $querydata = $this->_mpshippingModel
            ->create()
            ->getCollection()
            ->addFieldToFilter(
                'shipping_method_id',
                ['eq' => $methodId]
            )
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * @param $shippingMethodId
     * @return string
     */
    public function getShippingMethodName($shippingMethodId)
    {
        $methodName = '';
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $shippingMethodId);
        foreach ($shippingMethodModel as $shippingMethod) {
            $methodName = $shippingMethod->getMethodName();
        }
        return $methodName;
    }

    /**
     * @return array
     */
    public function getCountryOptionArray()
    {
        $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        if (!empty($options[0]["value"])) {
            $optionsDefault = [];
            $optionsDefault[] = [
                "value" => "",
                "label" => __("---Please select Country---")
            ];
            $options = array_merge($optionsDefault, $options);
        } else {
            $options[0]["label"] = __("---Please select Country---");
        }
        return $options;
    }

    /**
     * @return Country\Collection
     */
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory
            ->create()
            ->loadByStore();
        return $collection;
    }

    /**
     * Retrieve list of top destinations countries.
     *
     * @return array
     */
    protected function getTopDestinations()
    {
        $destinations = (string)$this->_scopeConfig->getValue(
            'general/country/destinations',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }
}
