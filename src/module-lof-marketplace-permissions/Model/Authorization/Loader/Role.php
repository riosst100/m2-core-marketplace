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

namespace Lof\MarketPermissions\Model\Authorization\Loader;

/**
 * Access Control List loader.
 */
class Role implements \Magento\Framework\Acl\LoaderInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Role\Collection
     */
    private $collection;

    /**
     * @var \Magento\Authorization\Model\Acl\Role\UserFactory
     */
    private $roleFactory;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @param \Magento\Authorization\Model\Acl\Role\UserFactory $roleFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\Role\Collection $collection
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     */
    public function __construct(
        \Magento\Authorization\Model\Acl\Role\UserFactory $roleFactory,
        \Lof\MarketPermissions\Model\ResourceModel\Role\Collection $collection,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser
    ) {
        $this->roleFactory = $roleFactory;
        $this->collection = $collection;
        $this->sellerUser = $sellerUser;
    }

    /**
     * Populate ACL with roles from external storage.
     *
     * @param \Magento\Framework\Acl $acl
     * @return void
     */
    public function populateAcl(\Magento\Framework\Acl $acl)
    {
        $sellerId = $this->sellerUser->getCurrentSellerId();
        if ($sellerId) {
            $this->collection->addFieldToFilter(\Lof\MarketPermissions\Api\Data\RoleInterface::SELLER_ID, $sellerId);
            $roles = $this->collection->getItems();
            /** @var \Lof\MarketPermissions\Api\Data\RoleInterface $role */
            foreach ($roles as $role) {
                $acl->addRole($this->roleFactory->create(['roleId' => $role->getId()]));
            }
        }
    }
}
