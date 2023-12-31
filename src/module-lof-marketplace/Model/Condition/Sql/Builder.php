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

namespace Lof\MarketPlace\Model\Condition\Sql;

use Magento\Rule\Model\Condition\Combine;

class Builder extends \Magento\Rule\Model\Condition\Sql\Builder
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param Combine $combine
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function attachConditionToCollection(
        \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection,
        Combine $combine
    ): void {

        $this->_connection = $collection->getResource()->getConnection();
        $this->_joinTablesToCollection($collection, $combine);
        $whereExpression = (string)$this->_getMappedSqlCombination($combine);

        $whereExpression = str_replace('`at_sku`.`value`', 'e.sku', $whereExpression);
        $whereExpression = str_replace('`at_seller_id`.`value`', 'e.seller_id', $whereExpression);
        if (!empty($whereExpression)) {
            //Select ::where method adds braces even on empty expression
            $collection->getSelect()->where($whereExpression);
        }
    }
}
