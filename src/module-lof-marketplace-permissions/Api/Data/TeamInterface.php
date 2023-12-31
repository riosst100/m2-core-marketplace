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

/**
 * Team interface
 *
 * @api
 * @since 100.0.0
 */
interface TeamInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TEAM_ID       = 'team_id';
    const NAME          = 'name';
    const DESCRIPTION   = 'description';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\TeamInterface
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return \Lof\MarketPermissions\Api\Data\TeamInterface
     */
    public function setName($name);

    /**
     * Set description
     *
     * @param string $description
     * @return \Lof\MarketPermissions\Api\Data\TeamInterface
     */
    public function setDescription($description);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Lof\MarketPermissions\Api\Data\TeamExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Lof\MarketPermissions\Api\Data\TeamExtensionInterface $extensionAttributes);
}
