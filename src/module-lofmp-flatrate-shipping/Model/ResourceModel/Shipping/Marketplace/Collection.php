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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Model\ResourceModel\Shipping\Marketplace;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;

class Collection extends \Lofmp\FlatRateShipping\Model\ResourceModel\Shipping\Grid\Collection
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var SellerFactory
     */
    private $sellerFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Session $session
     * @param SellerFactory $sellerFactory
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param null|string $resourceModel
     * @param string $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Session $session,
        SellerFactory $sellerFactory,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $mainTable,
            $eventPrefix,
            $eventObject,
            $resourceModel,
            $model,
            $connection,
            $resource
        );
        $this->session = $session;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * Render.
     */
    public function _renderFiltersBefore()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()
            ->load($customerId, 'customer_id');
        $sellerId = $seller->getId();
        if ($sellerId) {
            $this->addFieldToFilter('main_table.partner_id', $sellerId);
        }
        parent::_renderFiltersBefore();
    }
}
