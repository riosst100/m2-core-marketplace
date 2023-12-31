<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\CouponCodeGraqhQl\Model\Resolver\LofCouponCodes;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

class Identity implements IdentityInterface
{

    private $cacheTag = \Magento\Framework\App\Config::CACHE_TAG;

    /**
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids =  empty($resolvedData['couponcode_id']) ?
                        [] : [$this->cacheTag, sprintf('%s_%s', $this->cacheTag, $resolvedData['couponcode_id'])];

                    return $ids;
    }
}

