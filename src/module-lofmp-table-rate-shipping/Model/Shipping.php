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

use Lofmp\TableRateShipping\Api\Data\TableRateShippingInterfaceFactory;
use Lofmp\TableRateShipping\Api\Data\TableRateShippingInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Api\DataObjectHelper;

class Shipping extends AbstractModel implements TableRateShippingInterface, IdentityInterface
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
     * Shipping constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DataObjectHelper $dataObjectHelper
     * @param TableRateShippingInterfaceFactory $tableRateShippingInterfaceFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DataObjectHelper $dataObjectHelper,
        TableRateShippingInterfaceFactory $tableRateShippingInterfaceFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->dataObjectHelper = $dataObjectHelper;
        $this->tableRateShippingInterfaceFactory = $tableRateShippingInterfaceFactory;
        parent::__construct($context, $registry);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lofmp\TableRateShipping\Model\ResourceModel\Shipping::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getLofmpshippingId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getLofmpshippingId()
    {
        return $this->getData(self::LOFMPSHIPPING_ID);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setLofmpshippingId($id)
    {
        return $this->setData(self::LOFMPSHIPPING_ID, $id);
    }

    /**
     * @return array
     */
    public function getCustomAttributes()
    {
        $customAttributes = parent::getCustomAttributes();
        return $customAttributes ? $customAttributes : [];
    }
}
