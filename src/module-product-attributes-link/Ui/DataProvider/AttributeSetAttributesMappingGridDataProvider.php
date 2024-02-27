<?php

namespace CoreMarketplace\ProductAttributesLink\Ui\DataProvider;

use CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory;

class AttributeSetAttributesMappingGridDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product link collection
     *
     * @var \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * SellerDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->eavAttribute = $eavAttribute;

        $this->collection->addFieldToFilter('mapping_type', 'attribute_set');
        $this->collection->addFieldToFilter('main_table.attribute_code', ['neq' => null]);
        $this->collection->getSelect()->joinLeft(
            ['mpalg' => 'marketplace_product_attributes_link_group'],
            'mpalg.entity_id=main_table.group_id',
            ['group' => 'mpalg.name']
        );
        $this->collection->getSelect()->joinLeft(
            ['eas' => 'eav_attribute_set'],
            'eas.attribute_set_id=main_table.target_attribute_set_id',
            ['target_attribute_set' => 'eas.attribute_set_name']
        );
        $this->collection->getSelect()->joinLeft(
            ['ea' => 'eav_attribute'],
            'ea.attribute_code=main_table.attribute_code',
            ['attribute_name' => 'ea.frontend_label']
        );
        $this->collection->getSelect()->joinLeft(
            ['eaov' => 'eav_attribute_option_value'],
            'eaov.option_id=main_table.attribute_option_id',
            ['attribute_option' => 'eaov.value']
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
