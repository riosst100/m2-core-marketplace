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

class PendingProductAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->getPendingProductAttributes();
        $options = [];
        $options[] = [
            'label' => __('Use Default Config'),
            'value' => 0
        ];
        foreach ($attributes as $key => $title) {
            $options[] = [
                'label' => $title,
                'value' => $key
            ];
        }
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
            foreach ($optionsArray as $val) {
                $options[$val['value']] = $val['label'];
            }
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getPendingProductAttributes()
    {
        return [
            'attribute_set_id' => __('Attribute Set'),
            'name' => __('Product Name'),
            'sku' => __('SKU'),
            'price' => __('Price'),
            'tax_class_id' => __('Tax Class'),
            'qty' => __('Quantity'),
            'is_in_stock' => __('Stock Status'),
            'weight' => __('Weight'),
            'visibility' => __('Visibility'),
            'category_ids' => __('Categories'),
            'short_description' => __('Short Description'),
            'description' => __('Description'),
            'images' => __('Images'),
            'url_key' => __('URL Key'),
        ];
    }
}
