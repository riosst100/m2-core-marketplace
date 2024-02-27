<?php

namespace CoreMarketplace\ProductAttributesLink\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
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
                if (isset($item['category'])) {
                    $item[$fieldName] = $item['category'];
                }
            }
        }
        return $dataSource;
    }
}
