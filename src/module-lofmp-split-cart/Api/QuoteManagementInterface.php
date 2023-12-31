<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Api;

interface QuoteManagementInterface
{
    /**
     * @param int $cartId
     * @param int $sellerId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function getSplitCart(
        $cartId
    );
}


