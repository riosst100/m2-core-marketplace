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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Model;

use Lofmp\TableRateShipping\Api\Data\TableRateShippingMethodInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Shippingmethod extends AbstractModel implements TableRateShippingMethodInterface, IdentityInterface
{
    const CACHE_TAG = 'lofmptablerateshipping';

    /**
     * @var string
     */
    protected $_cacheTag = 'lofmptablerateshipping';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'lofmptablerateshipping';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lofmp\TableRateShipping\Model\ResourceModel\Shippingmethod::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $id
     * @return TableRateShippingMethodInterface|Shippingmethod
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
