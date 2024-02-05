<?php

namespace CoreMarketplace\MarketPlace\Model\Config\Source;

class SellerType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        $options = [];

        if ($addEmpty) {
            $options[] = [
                'label' => __('-- Please Select a Agroup --'),
                'value' => 0
            ];
        }
        $options[] = [
            'label' => __('Individual'),
            'value' => 'individual'
        ];
        $options[] = [
            'label' => __('Company'),
            'value' => 'company'
        ];
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
