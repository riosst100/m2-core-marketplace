<?php

namespace Lofmp\SellerMembershipLimit\Plugin\Block\Membership\Product;

class ListProduct
{
    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $group;

    /**
     * ListProduct constructor.
     * @param \Lof\MarketPlace\Model\Group $group
     */
    public function __construct(
        \Lof\MarketPlace\Model\Group $group
    ) {
        $this->group = $group;
    }

    /**
     * @param \Lofmp\SellerMembership\Block\Membership\Product\ListProduct $subject
     * @param $title
     * @return array[]
     */
    public function afterGetExtraOptions(
        \Lofmp\SellerMembership\Block\Membership\Product\ListProduct $subject,
        $extraOptions,
        $groupId
    ) {
        $group = $this->getGroup($groupId)->getData();
        $limitProductPerDuration = $group['limit_product_duration'];
        $limitAuctionPerDuration = $group['limit_auction_duration'];
        if ($limitProductPerDuration) {
            $extraOptions[] = [
                'value' => $limitProductPerDuration,
                'title' => __('Limit Product per Duration'),
            ];
        }

        if ($limitAuctionPerDuration) {
            $extraOptions[] = [
                'value' => $limitAuctionPerDuration,
                'title' => __('Limit Auctions per Duration'),
            ];
        }

        return $extraOptions;
    }

    /**
     * @param $groupId
     * @return \Magento\Framework\DataObject
     */
    public function getGroup($groupId)
    {
        $group = $this->group->getCollection()->addFieldToFilter('group_id', $groupId)->getFirstItem();
        return $group;
    }
}
