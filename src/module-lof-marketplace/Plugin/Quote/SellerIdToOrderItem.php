<?php

namespace Lof\MarketPlace\Plugin\Quote;

class SellerIdToOrderItem

{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setLofSellerId($item->getLofSellerId());
        return $orderItem;
    }
}
