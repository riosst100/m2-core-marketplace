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

namespace Lof\MarketPermissions\Model;

class ResourcePool
{
    /**
     * @var array
     */
    private $resources;

    /**
     * @param array $resources
     */
    public function __construct(
        $resources = []
    ) {
        $this->resources = $resources;
    }

    /**
     * Get default resources.
     *
     * @return array
     */
    public function getDefaultResources()
    {
        return $this->resources;
    }
}
