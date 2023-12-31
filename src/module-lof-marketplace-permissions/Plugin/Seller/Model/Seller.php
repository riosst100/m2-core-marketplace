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

namespace Lof\MarketPermissions\Plugin\Seller\Model;

class Seller
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Lof\MarketPermissions\Api\StatusServiceInterface
     */
    private $moduleConfig;

    /**
     * Seller constructor.
     * @param \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->resource = $resource;
    }

    /**
     * @param \Lof\MarketPlace\Model\Seller $subject
     * @param \Closure $proceed
     * @param $modelId
     * @param null $field
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundLoad(
        \Lof\MarketPlace\Model\Seller $subject,
        \Closure $proceed,
        $modelId,
        $field = null
    ) {

        if (!$this->moduleConfig->isActive()) {
            return $proceed($modelId, $field);
        }

        if ($field == \Lof\MarketPermissions\Api\Data\SellerInterface::CUSTOMER_ID) {
            $connection = $this->resource->getConnection();
            $select = $connection->select()
                ->from(
                    ['seller_customer' => 'lof_marketplace_advanced_customer_entity'],
                    ['seller_id']
                )
                ->where('seller_customer.customer_id = ?', $modelId)
                ->where('seller_customer.status = ?', 1);
            $sellerId = $this->resource->getConnection()->fetchOne($select);

            if (!$sellerId) {
                return $proceed($modelId, $field);
            }

            $seller = $subject->load($sellerId, 'seller_id');

            $modelId = $seller->getCustomerId();

        }

        return $proceed($modelId, $field);
    }
}
