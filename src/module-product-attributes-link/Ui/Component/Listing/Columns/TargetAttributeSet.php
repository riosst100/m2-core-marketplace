<?php

namespace CoreMarketplace\ProductAttributesLink\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class TargetAttributeSet extends Column
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
                if (isset($item['target_attribute_set'])) {
                    $item[$fieldName] = $item['target_attribute_set'];
                }
            }
        }
        return $dataSource;
    }
}
