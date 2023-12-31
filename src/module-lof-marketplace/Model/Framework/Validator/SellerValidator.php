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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @see \Lof\MarketPlace\Model\ResourceModel\Seller
 */
class SellerValidator implements SellerValidatorInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function validate($seller): bool
    {
        if (!$seller) {
            return false;
        }
        if (!(int)$seller->getCustomerId()) {
            return false;
        }
        if (!$seller->getEmail()) {
            return false;
        }
        if (!$seller->getUrlKey()) {
            return false;
        }
        return true;
    }
}
