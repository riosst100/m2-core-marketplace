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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lof\MarketPermissions\Model\Action;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Thrown when impossible to invite seller to a seller without confirmation.
 */
class InviteConfirmationNeededException extends LocalizedException
{
    /**
     * @var CustomerInterface
     */
    private $forCustomer;

    /**
     * @inheritDoc
     */
    public function __construct(Phrase $phrase, CustomerInterface $customer, \Exception $cause = null, int $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
        $this->forCustomer = $customer;
    }

    /**
     * Customer to be invited.
     *
     * @return CustomerInterface
     */
    public function getForCustomer(): CustomerInterface
    {
        return $this->forCustomer;
    }
}
