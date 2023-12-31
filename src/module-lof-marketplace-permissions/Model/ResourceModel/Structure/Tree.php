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

namespace Lof\MarketPermissions\Model\ResourceModel\Structure;

use Magento\Framework\Data\Tree\Dbp;

/**
 * Tree for seller hierarchy.
 */
class Tree extends Dbp
{
    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @throws \DomainException
     * @throws \Exception
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_coreResource = $resource;
        parent::__construct(
            $resource->getConnection(),
            $resource->getTableName('lof_marketplace_structure'),
            [
                self::ID_FIELD      => 'structure_id',
                self::PATH_FIELD    => 'path',
                self::ORDER_FIELD   => 'position',
                self::LEVEL_FIELD   => 'level'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function move($node, $newParent, $prevNode = null)
    {
        parent::move($node, $newParent, $prevNode);
        $this->_conn->update(
            $this->_table,
            ['parent_id' => $newParent->getId()],
            $this->_conn->quoteInto("{$this->_idField} = ?", $node->getId())
        );
    }

    /**
     * Sets whether the tree is loaded from database.
     *
     * @param bool $loaded
     * @return void
     */
    public function setLoaded($loaded)
    {
        $this->_loaded = $loaded;
    }
}
