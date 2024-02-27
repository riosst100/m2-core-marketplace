<?php

namespace CoreMarketplace\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;

class AttributeSet extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AttributeSet
{
    /**
     * Return options for select
     *
     * @return array
     * @since 101.0.0
     */
    public function getOptions()
    {
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
        $collection = $this->attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter($this->locator->getProduct()->getResource()->getTypeId())
        ->addFieldToSelect('attribute_set_id', 'value')
        ->addFieldToSelect('attribute_set_name', 'label')
        ->setOrder(
            'attribute_set_name',
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
        );

        $collectionData = $collection->getData() ?? [];

        return $collectionData;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        if ($name = $this->getGeneralPanelName($meta)) {
            $meta[$name]['children']['attribute_set_id']['arguments']['data']['config']  = [
                'component' => 'Magento_Catalog/js/components/attribute-set-select',
                'disableLabel' => true,
                'filterOptions' => true,
                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                'formElement' => 'select',
                'componentType' => Field::NAME,
                'options' => $this->getOptions(),
                'visible' => 1,
                'required' => 1,
                'label' => __('Attribute Set'),
                'source' => $name,
                'dataScope' => 'attribute_set_id',
                'filterUrl' => $this->urlBuilder->getUrl('catalog/product/suggestAttributeSets', ['isAjax' => 'true']),
                'sortOrder' => $this->getNextAttributeSortOrder(
                    $meta,
                    [ProductAttributeInterface::CODE_STATUS],
                    self::ATTRIBUTE_SET_FIELD_ORDER
                ),
                'multiple' => false,
                'disabled' => $this->locator->getProduct()->isLockedAttribute('attribute_set_id'),
            ];
            $meta[$name]['children']['mapping_group_id']['arguments']['data']['config']  = [
                'formElement' => 'input',
                'componentType' => Field::NAME,
                'visible' => 0,
                'required' => 0,
                'label' => __('Mapping Group ID'),
                'source' => $name,
                'dataScope' => 'mapping_group_id'
            ];
            $meta[$name]['children']['mapping_priority']['arguments']['data']['config']  = [
                'formElement' => 'input',
                'componentType' => Field::NAME,
                'visible' => 0,
                'required' => 0,
                'label' => __('Mapping Priority'),
                'source' => $name,
                'dataScope' => 'mapping_priority'
            ];
        }

        return $meta;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    self::DATA_SOURCE_DEFAULT => [
                        'attribute_set_id' => $this->locator->getProduct()->getAttributeSetId()
                    ],
                ]
            ]
        );
    }
}
