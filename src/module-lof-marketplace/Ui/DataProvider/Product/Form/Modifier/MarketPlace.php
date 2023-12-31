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

namespace Lof\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Lof\MarketPlace\Helper\Data as MarketPlaceHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

/**
 * Data provider for "Customizable Options" panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MarketPlace extends AbstractModifier
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketPlaceHelper;

    /**
     * Set collection factory
     *
     * @var AttributeSetCollectionFactory
     */
    protected $_attributeSetCollectionFactory;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * MarketPlace constructor.
     * @param LocatorInterface $locator
     * @param MarketPlaceHelper $marketPlaceHelper
     * @param AttributeSetCollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        LocatorInterface $locator,
        MarketPlaceHelper $marketPlaceHelper,
        AttributeSetCollectionFactory $attributeSetCollectionFactory
    ) {
        $this->locator = $locator;
        $this->_marketPlaceHelper = $marketPlaceHelper;
        $this->_attributeSetCollectionFactory = $attributeSetCollectionFactory;

        return $this;
    }

    /**
     * @var array
     */
    protected $_meta = [];

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;
        $this->removeNotUsedSections();
        $this->updateCustomOptionsJs();

        return $this->_meta;
    }

    /**
     * Remove not used sections
     */
    public function removeNotUsedSections()
    {
        if (isset($this->_meta['product-details']['children']['container_seller_id'])) {
            unset($this->_meta['product-details']['children']['container_seller_id']);
        }
        if (isset($this->_meta['product-details']['children']['container_approval'])) {
            unset($this->_meta['product-details']['children']['container_approval']);
        }
    }

    /**
     * Update custom options js
     */
    public function updateCustomOptionsJs()
    {
        $this->_meta['custom_options']['children']['options']['children']['record']
        ['children']['container_option']['children']['container_common']
        ['children']['type']['arguments']['data']['config']['component']
            = 'Lof_MarketPlace/js/custom-options-type';

        $this->_meta['custom_options']['children']['options']['children']['record']
        ['children']['container_option']['children']['container_common']
        ['children']['title']['arguments']['data']['config']['component']
            = 'Lof_MarketPlace/component/static-type-input';
    }
}
