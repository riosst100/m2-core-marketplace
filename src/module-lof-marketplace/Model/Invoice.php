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

namespace Lof\MarketPlace\Model;

class Invoice extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_invoice';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'invoice';
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Invoice::class);
    }
}
