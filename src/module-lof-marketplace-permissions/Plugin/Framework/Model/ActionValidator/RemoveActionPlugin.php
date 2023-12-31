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

namespace Lof\MarketPermissions\Plugin\Framework\Model\ActionValidator;

use Lof\MarketPermissions\Model\Seller\Structure;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Model\AbstractModel;

class RemoveActionPlugin
{
    /**
     * Customer model class name.
     *
     * @var string
     */
    private $customerModel = \Magento\Customer\Model\Customer::class;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Structure
     */
    private $structureManager;

    /**
     * RemoveActionPlugin constructor.
     *
     * @param UserContextInterface $userContext
     * @param Structure $structureManager
     */
    public function __construct(UserContextInterface $userContext, Structure $structureManager)
    {
        $this->userContext = $userContext;
        $this->structureManager = $structureManager;
    }

    /**
     * Around isAllowed.
     *
     * @param \Magento\Framework\Model\ActionValidator\RemoveAction $subject
     * @param \Closure $proceed
     * @param AbstractModel $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsAllowed(
        \Magento\Framework\Model\ActionValidator\RemoveAction $subject,
        \Closure $proceed,
        AbstractModel $model
    ) {
        if ($model instanceof $this->customerModel) {
            $customerId = $model->getId();
            $currentCustomerId = $this->userContext->getUserId();
            $allowedIds = $this->structureManager->getAllowedIds($currentCustomerId);

            if ($customerId && $currentCustomerId && $customerId != $currentCustomerId
                && in_array($customerId, $allowedIds['users'])) {
                return true;
            }
        }

        return $proceed($model);
    }
}
