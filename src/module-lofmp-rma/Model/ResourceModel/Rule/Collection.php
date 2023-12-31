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



namespace Lofmp\Rma\Model\ResourceModel\Rule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger        = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager  = $eventManager;
        $this->storeManager  = $storeManager;
        $this->connection    = $connection;
        $this->resource      = $resource;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\Rule', 'Lofmp\Rma\Model\ResourceModel\Rule');
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Lofmp\Rma\Model\Rule $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Lofmp\Rma\Model\Rule $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

     /************************/
}
