<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;

class FrontendRma extends Rma implements \Lofmp\Rma\Api\Data\RmaFrontendInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->getData(self::KEY_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

}
