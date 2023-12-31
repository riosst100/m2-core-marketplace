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

namespace Lof\MarketPermissions\Model\ResourceModel\Permission;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Permission collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'permission_id';

    /**
     * Custom option hash for permissions.
     *
     * @param string $valueField
     * @param string $labelField
     * @return array
     */
    public function toOptionHash($valueField = 'resource_id', $labelField = 'permission')
    {
        return $this->_toOptionHash($valueField, $labelField);
    }

    /**
     * Standard collection initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\MarketPermissions\Model\Permission::class,
            \Lof\MarketPermissions\Model\ResourceModel\Permission::class
        );
    }
}
