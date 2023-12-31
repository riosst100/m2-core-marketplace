<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Model\Rewrite\CatalogInventory\Stock;

class Item extends \Magento\CatalogInventory\Model\Stock\Item
{
    public function getIsInStock()
    {
        if (!$this->getManageStock()) {
            return true;
        }
        return (bool) $this->_getData(static::IS_IN_STOCK);
    }

    /**
     * @return float
     */
    public function getQty()
    {
        $fullActionName = $this->helper()->getFullActionName();
        $productId = $this->getProductId();
        $qty = $this->helper()->getAssignProductQty($productId);
        if ($fullActionName == 'marketplace_product_edit') {
            return $this->_getData(static::QTY) - $qty;
        }
        return null === $this->_getData(static::QTY) ? null : floatval($this->_getData(static::QTY));
    }

    public function helper()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Lofmp\PriceComparison\Helper\Data');
        return $helper;
    }
}
