<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Model\Config\Source;

class SuggestionCriteria implements \Magento\Framework\Option\ArrayInterface {

    /**
     * @var array
     */
    protected $optionArray;

    /**
     * @var array
     */
    protected $condition;

    const DEFAULT_CONDITION = 'created_at';
    const CREATED_AT = 'created_at';
    const TOP_RATED = 'top_rated';
    const RANDOM = 'random';
    const FEATURED = 'featured';
    const DEALS = 'deals';
    const NEW_ARRIVAL = 'new_arrival';

    /**
     *
     */
    public function __construct(){
        $this->optionArray = [
            ['value' => 'latest', 'label' => __('Latest')],
            ['value' => 'top_rated', 'label' => __('Top Rated')],
            ['value' => 'random', 'label' => __('Random')],
            ['value' => 'featured', 'label' => __('Featured - Filter by attribute featured')],
            ['value' => 'new_arrival', 'label' => __('New Arrival')],
            ['value' => 'deals', 'label' => __('Deals - Use Special Price attribute')]
        ];

        $this->condition = [
            'latest' => 'created_at',
            'top_rated' => 'top_rated',
            'random' => 'random',
            'featured' => 'featured',
            'new_arrival' => 'new_arrival',
            'deals' => 'deals'
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        return $this->optionArray;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function optionToCondition($key) {
        if(array_key_exists($key, $this->condition))
            return $this->condition[$key];
        return self::DEFAULT_CONDITION;
    }
}