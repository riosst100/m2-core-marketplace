<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Api;

use Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface SellerCustomerRepositoryInterface
{
    /**
     * @param \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface $page
     * @return mixed
     */
    public function save(SubscriptionCustomerInterface $page);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface $page
     * @return mixed
     */
    public function delete(SubscriptionCustomerInterface $page);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * Current customer add a seller to the favorite list
     * @param int $customer_id
     * @param int $seller_id
     * @return mixed
     */
    public function addSeller($customer_id,$seller_id);

    /**
     * Remove sellers to customer's favorite list
     * @param int $customer_id
     * @param int[] $seller_ids
     * @return mixed
     */
    public function removeSellers($customer_id,array $seller_ids);

    /**
     * Get list favorites from customer
     * @param int $customer_id
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function customerGetList($customer_id,SearchCriteriaInterface $criteria);

    /**
     * Get list favorites from customer
     * @param int $customer_id
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function sellerGetList($customer_id,SearchCriteriaInterface $criteria);

    /**
     * Check if the seller is on the customer's favorite list or not
     * @param int $customer_id
     * @param int $seller_id
     * @return bool
     */
    public function checkSeller($customer_id,$seller_id);
}
