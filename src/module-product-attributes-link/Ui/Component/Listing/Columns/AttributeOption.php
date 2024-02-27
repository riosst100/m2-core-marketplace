<?php

namespace CoreMarketplace\ProductAttributesLink\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class AttributeOption extends Column
{
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
                if (isset($item['attribute_option'])) {
                    $item[$fieldName] = $item['attribute_option'];
                }
            }
        }
        return $dataSource;
    }
}
