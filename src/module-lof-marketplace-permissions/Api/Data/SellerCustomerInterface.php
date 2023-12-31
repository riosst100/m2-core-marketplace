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

namespace Lof\MarketPermissions\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Extended customer custom attributes interface.
 */
interface SellerCustomerInterface extends ExtensibleDataInterface
{
    /**
     * Customer id key.
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * seller id key.
     */
    const SELLER_ID = 'seller_id';

    /**
     * Job title key.
     */
    const JOB_TITLE = 'job_title';

    /**
     * Status key.
     */
    const STATUS = 'status';

    /**
     * Telephone key.
     */
    const TELEPHONE = 'telephone';

    /**
     * Status inactive value.
     */
    const STATUS_INACTIVE = 0;

    /**
     * Status active value.
     */
    const STATUS_ACTIVE = 1;

    /**
     * Seller admin type value.
     */
    const TYPE_SELLER_ADMIN = 0;

    /**
     * Seller user type value.
     */
    const TYPE_SELLER_USER = 1;

    /**
     * Individual user type value.
     */
    const TYPE_INDIVIDUAL_USER = 2;

    /**
     * Get customer ID.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get seller ID.
     *
     * @return int|null
     */
    public function getSellerId();

    /**
     * Get get job title.
     *
     * @return string|null
     */
    public function getJobTitle();

    /**
     * Get customer status.
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Get get telephone.
     *
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set customer ID.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    public function setCustomerId($id);

    /**
     * Set seller ID.
     *
     * @param int $sellerId
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    public function setSellerId($sellerId);

    /**
     * Set job title.
     *
     * @param string $jobTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    public function setJobTitle($jobTitle);

    /**
     * Set customer status.
     *
     * @param int $status
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    public function setStatus($status);

    /**
     * Set telephone.
     *
     * @param string $telephone
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    public function setTelephone($telephone);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Lof\MarketPermissions\Api\Data\SellerCustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\MarketPermissions\Api\Data\SellerCustomerExtensionInterface $extensionAttributes
    );
}
