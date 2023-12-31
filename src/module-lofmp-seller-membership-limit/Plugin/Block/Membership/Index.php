<?php

namespace Lofmp\SellerMembershipLimit\Plugin\Block\Membership;

class Index
{
    /**
     * @param \Lofmp\SellerMembership\Block\Membership\Index $subject
     * @param $extraOptions
     * @param $membership
     * @return mixed
     */
    public function afterGetExtraOptions(
        \Lofmp\SellerMembership\Block\Membership\Index $subject,
        $extraOptions,
        $membership
    ) {
        $limitProductPerDuration = $membership->getData('limit_product_duration');
        $limitAuctionPerDuration = $membership->getData('limit_auction_duration');
        if ($limitProductPerDuration != null) {
            $extraOptions[] = [
                'value' => $limitProductPerDuration,
                'title' => __('Limit Product per Duration'),
            ];
        }

        if ($limitAuctionPerDuration != null) {
            $extraOptions[] = [
                'value' => $limitAuctionPerDuration,
                'title' => __('Limit Auctions per Duration'),
            ];
        }

        return $extraOptions;
    }
}
