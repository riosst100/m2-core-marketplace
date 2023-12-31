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
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model\Framework\Validator;

use Lof\MarketPlace\Api\Data\SellerInterface;

/**
 * Responsible for seller validation
 * Extension point for base validation
 *
 * @api
 */
interface SellerValidatorInterface
{
    /**
     * Validate Seller
     *
     * @param SellerInterface|\Magento\Framework\Model\AbstractModel $seller
     *
     * @return bool
     */
    public function validate($seller): bool;
}
