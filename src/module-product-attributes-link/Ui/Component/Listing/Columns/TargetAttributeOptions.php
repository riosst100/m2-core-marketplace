<?php

namespace CoreMarketplace\ProductAttributesLink\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class TargetAttributeOptions extends Column
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $components = [],
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['target_attribute_options'])) {

                    $targetAttributeOptionsName = [];

                    $targetAttributeOptionsIds = $item['target_attribute_options'] ? explode(",", $item['target_attribute_options']) : null;
                    if ($targetAttributeOptionsIds) {
                        $attributeCode = $item['target_attribute_code'];
                        foreach($targetAttributeOptionsIds as $optionId) {
                            $targetAttributeOptionsName[]  = $this->getAttributeOptionLabelById($attributeCode, $optionId);
                        }
                    }

                    $item[$fieldName] = $targetAttributeOptionsName ? implode("<br />", $targetAttributeOptionsName) : '';
                }
            }
        }
        return $dataSource;
    }

    public function getAttributeOptionLabelById($attributeCode, $optionId) 
    {
        $label = '';

        $attr = $this->productFactory->create()
        ->getResource()
        ->getAttribute($attributeCode);
        if ($attr->usesSource()) {
            $label = $attr->getSource()->getOptionText($optionId);
        }

        return $label;
    }
}
