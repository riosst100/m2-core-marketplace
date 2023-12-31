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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lofmp\SellerMembership\Model\ResourceModel\Membership;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

;

class Collection extends AbstractCollection
{
     /**
      * Store manager
      *
      * @var \Magento\Store\Model\StoreManagerInterface
      */
    protected $_storeManager;
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface     $entityFactory
     * @param \Psr\Log\LoggerInterface                                      $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface  $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                     $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null           $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null     $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_storeManager = $storeManager;
    }

     /**
      * @var string
      */
    protected $_idFieldName = 'membership_id';

    protected function _construct()
    {
        $this->_init('Lofmp\SellerMembership\Model\Membership', 'Lofmp\SellerMembership\Model\ResourceModel\Membership');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}
