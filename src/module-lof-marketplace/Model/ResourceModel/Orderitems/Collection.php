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

namespace Lof\MarketPlace\Model\ResourceModel\Orderitems;

class Collection extends \Lof\MarketPlace\Model\ResourceModel\AbstractReport\Orderitemscollection
{
    /**
     * @var string
     */
    protected $_date_column_filter = "main_table.created_at";

    /**
     * @var string
     */
    protected $_period_type = '';

    /**
     * @param string $column_name
     * @return $this
     */
    public function setDateColumnFilter($column_name = '')
    {
        if ($column_name) {
            $this->_date_column_filter = $column_name;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDateColumnFilter()
    {
        return $this->_date_column_filter;
    }

    /**
     * Set status filter
     *
     * @param null $from
     * @return $this
     */
    public function addDateFromFilter($from = null)
    {
        $this->_from_date_filter = $from;
        return $this;
    }

    /**
     * Set status filter
     * @param null $to
     * @return $this
     */
    public function addDateToFilter($to = null)
    {
        $this->_to_date_filter = $to;
        return $this;
    }

    /**
     * @return $this|Collection
     */
    public function applyCustomFilter()
    {
        $this->_applyDateFilter();
        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _applyDateFilter()
    {
        if ($this->_to_date_filter && $this->_from_date_filter) {
            $dateStart = $this->_localeDate->convertConfigTimeToUtc($this->_from_date_filter, 'Y-m-d 00:00:00');
            $endStart = $this->_localeDate->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = ['from' => $dateStart, 'to' => $endStart, 'datetime' => true];

            $this->addFieldToFilter($this->getDateColumnFilter(), $dateRange);
        }

        return $this;
    }
}
