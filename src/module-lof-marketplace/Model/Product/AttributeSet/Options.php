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

namespace Lof\MarketPlace\Model\Product\AttributeSet;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var
     */
    protected $options;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $product;

    /**
     * Options constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
        $this->helper = $helper;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (null == $this->options) {
            $this->options = $this->collectionFactory->create()
                ->setEntityTypeFilter($this->product->getTypeId())
                ->addFieldToFilter('attribute_set_id', ['nin' => $this->helper->getAttributeSetRestriction()])
                ->toOptionArray();
        }
        return $this->options;
    }
}
