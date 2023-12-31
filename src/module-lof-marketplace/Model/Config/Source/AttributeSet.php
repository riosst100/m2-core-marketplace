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

namespace Lof\MarketPlace\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Grid\CollectionFactory;

/**
 * Class AttributeSet
 * @package Lofmp\AttributeSets\Model\Config\Source
 */
class AttributeSet implements ArrayInterface
{
    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * flag load attribute set
     *
     * @var bool
     */
    protected $_flag = false;

    /**
     * AttributeSet constructor.
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
    }

    /**
     * Define in register catalog_product entity type code as entityType
     *
     * @return void
     */
    protected function _setTypeId()
    {
        if (!$this->_flag) {
            $this->_coreRegistry->register(
                'entityType',
                $this->_objectManager->create(\Magento\Catalog\Model\Product::class)->getResource()->getTypeId()
            );
            $this->_flag = true;
        }
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_coreRegistry->registry("entityType") == null) {
            $this->_setTypeId();
        }
        $setCollection = $this->collectionFactory->create();
        $setCollection->setOrder('attribute_set_name','ASC');
        $options = [];
        $options2 = [];
        foreach ($setCollection as $item) {
            if ($item->getAttributeSetId() == self::DEFAULT_ATTRIBUTE_SET_ID) {
                $options[] = [
                    'label' => $item->getAttributeSetName(),
                    'value' => $item->getAttributeSetId()
                ];
            } else {
                $options2[] = [
                    'label' => $item->getAttributeSetName(),
                    'value' => $item->getAttributeSetId()
                ];
            }
        }
        $options = array_merge($options, $options2);
        return $options;
    }

    /**
     * @return array
     */
    public function toOptions()
    {
        $optionsArray = $this->toOptionArray();
        $options = [];
        if ($optionsArray) {
            foreach ($optionsArray as $key => $val) {
                $options[$val["value"]] = $val['label'];
            }
        }
        return $options;
    }
}
