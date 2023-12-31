<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Model\Config\Model;

use Lof\MarketPlace\Model\ResourceModel\Seller\Collection;
use Magento\Framework\Data\OptionSourceInterface;

class Sellers implements OptionSourceInterface
{

    /**
     * @var Collection
     */
    private $sellerCollection;

    public function __construct(
        Collection $collection
    ) {
        $this->sellerCollection = $collection;
    }

    /**
     * Get Auction type labels array for option element.
     *
     * @return array
     */
    public function getOptions()
    {
        $collection = $this->sellerCollection;
        $sellers = [];
        foreach ($collection as $seller) {
            $sellers[] = [
                'value' => $seller->getData('seller_id'),
                'label' => $seller->getName()
            ];
        }
        return $sellers;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
