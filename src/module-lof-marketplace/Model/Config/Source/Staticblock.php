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

class Staticblock implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $_groupModel;

    /**
     * @param \Magento\Cms\Model\Block $blockModel
     */
    public function __construct(
        \Magento\Cms\Model\Block $blockModel
    ) {
        $this->_groupModel = $blockModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_groupModel->getCollection();
        $blocks = [];
        foreach ($collection as $_block) {
            $blocks[] = [
                'value' => $_block->getId(),
                // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                'label' => addslashes($_block->getTitle())
            ];
        }

        $blocks[] = [
            'value' => 'pretext_html',
            'label' => __('Pretext HTML')
        ];
        array_unshift($blocks, [
            'value' => '',
            'label' => __('-- Please Select --'),
        ]);

        return $blocks;
    }
}
