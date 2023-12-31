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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lof\PreOrder\Model;

use Magento\Framework\ObjectManagerInterface;
use Lof\PreOrder\Api\Data\ItemInterface;

class Item extends \Magento\Framework\Model\AbstractModel implements ItemInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $registry);
    }
    protected function _construct()
    {

        $this->_init('Lof\PreOrder\Model\ResourceModel\Item');
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($item_id)
    {
        return $this->setData(self::ITEM_ID, $item_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPreorderPercent($preorder_percent)
    {
        return $this->setData(self::PREORDER_PERCENT, $preorder_percent);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreorderPercent()
    {
        return $this->getData(self::PREORDER_PERCENT);
    }
}
