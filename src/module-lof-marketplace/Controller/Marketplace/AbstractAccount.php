<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace;

use Magento\Framework\App\Action\Action;

/**
 * AbstractAccount class is deprecated, in favour of Composition approach to build Controllers
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @deprecated 103.0.0
 * @see \Magento\Customer\Controller\AccountInterface
 */
abstract class AbstractAccount extends Action implements AccountInterface /** @phpstan-ignore-line */
{
}
