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

namespace Lof\MarketPlace\Model\Config\Shipping\Methods;

use Magento\Framework\Api\AttributeValueFactory;

// @phpstan-ignore-next-line
class AbstractModel extends \Lof\MarketPlace\Model\FlatAbstractModel
{
    const SHIPPING_SECTION = 'shipping';

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @var array
     */
    protected $_fields = [];

    /**
     * @var string
     */
    protected $_codeSeparator = '-';

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketplaceHelperData;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * AbstractModel constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelperData
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
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_objectManager = $objectInterface;
        $this->_marketplaceHelperData = $marketplaceHelperData;
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
     * @return array
     */
    public function getFields()
    {
        $this->_fields = [];
        $this->_fields['active'] = [
            'type' => 'select',
            'values' => [
                ['label' => __('Yes'), 'value' => 1],
                ['label' => __('No'), 'value' => 0]
            ]
        ];
        return $this->_fields;
    }

    /**
     * @param $key
     * @return \Magento\Framework\Phrase
     */
    public function getLabel($key)
    {
        switch ($key) {
            case 'active':
                return __('Active');
            default:
                return $key;
        }
    }

    /**
     * @param $methodData
     * @return bool
     */
    public function validateSpecificMethod($methodData)
    {
        if (count($methodData) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
