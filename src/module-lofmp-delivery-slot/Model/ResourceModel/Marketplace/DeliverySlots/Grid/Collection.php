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
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\DeliverySlot\Model\ResourceModel\Marketplace\DeliverySlots\Grid;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * Lofmp\DeliverySlot\Model\ResourceModel\Marketplace\DeliverySlots\Grid
 */
class Collection extends \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\Grid\Collection
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
     * Collection constructor.
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param Session $session
     * @param SellerFactory $sellerFactory
     * @param $mainTable
     * @param $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        Session $session,
        SellerFactory $sellerFactory,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->session = $session;
        $this->sellerFactory = $sellerFactory;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $eventPrefix,
            $eventObject,
            $resourceModel,
            $model,
            $connection,
            $resource
        );
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
            $this->addFieldToFilter('seller_id', $sellerId);
        }
        parent::_renderFiltersBefore();
    }
}
