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

class AllowedSocials implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->getAllowedSocials();
        $options = [];
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
    public function getAllowedSocials()
    {
        return [
            'twitter' => __('Twitter'),
            'facebook' => __('Facebook'),
            'google' => __('Google Plus'),
            'youtube' => __('Youtube'),
            'vimeo' => __('Vimeo'),
            'linkedin' => __('Linkedin'),
            'instagram' => __('Instagram'),
            'pinterest' => __('Pinterest'),
        ];
    }
}
