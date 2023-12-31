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
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class CollectionPlugin
{
    /**
     * Const
     */
    const HAS_APPROVAL_PRODUCT_FILTER = 'has_approval_product_filter';

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * CollectionPlugin constructor.
     * @param UserContextInterface $userContext
     */
    public function __construct(
        UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
    }

    /**
     * Join shared catalog product item to product collection.
     *
     * @param ProductCollection $collection
     * @param bool $printQuery [optional]
     * @param bool $logQuery [optional]
     * @return array
     */
    public function beforeLoad(
        ProductCollection $collection,
        $printQuery = false,
        $logQuery = false
    ): array {
        if (!$collection->isLoaded()) {
            $this->addApprovalProductFilter($collection);
        }

        return [$printQuery, $logQuery];
    }

    /**
     * Join shared catalog product item to product collection.
     *
     * @param ProductCollection $collection
     * @return array
     */
    public function beforeGetSelectCountSql(ProductCollection $collection): array
    {
        $this->addApprovalProductFilter($collection);

        return [];
    }

    /**
     * Add shared catalog filter to collection
     *
     * @param ProductCollection $collection
     * @return void
     */
    private function addApprovalProductFilter(ProductCollection $collection): void
    {
        // avoid adding shared catalog filter on create/edit products by api
        if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_ADMIN
            || $this->userContext->getUserType() === UserContextInterface::USER_TYPE_INTEGRATION) {
            return;
        }

        if (!$collection->hasFlag(self::HAS_APPROVAL_PRODUCT_FILTER)) {
            $collection->addAttributeToFilter('approval', ['in' => [0, 2]]);
            $collection->setFlag(self::HAS_APPROVAL_PRODUCT_FILTER, true);
        }
    }
}
