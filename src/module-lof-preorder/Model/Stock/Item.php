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

namespace Lof\PreOrder\Model\Stock;

class Item extends \Magento\CatalogInventory\Model\Stock\Item
{
    /**
     * Retrieve Stock Availability.
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        $isInStock = $this->_getData(static::IS_IN_STOCK);
        $productId = $this->getProductId();
        $helper = $this->helper();
        if ($helper->isPreorder($productId, $isInStock)) {
            return true;
        } elseif ($helper->isConfigPreorder($productId, $isInStock)) {
            return true;
        } else {
            if (!$this->getManageStock()) {
                return true;
            }

            return (bool) $this->_getData(static::IS_IN_STOCK);
        }
    }

    public function getQty()
    {
        $isInStock = $this->_getData(static::IS_IN_STOCK);
        $productId = $this->getProductId();
        $helper = $this->helper();
        if ($helper->isAdminArea()) {
            return parent::getQty();
        } else {
            if ($helper->isPreorder($productId, $isInStock)) {
                return 99999;
            } elseif ($helper->isConfigPreorder($productId, $isInStock)) {
                return 99999;
            }
        }
        return parent::getQty();
    }

    public function helper()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Lof\PreOrder\Helper\Data');
        return $helper;
    }
}
