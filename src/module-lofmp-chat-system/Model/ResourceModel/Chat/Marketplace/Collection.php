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
 * @package    Lofmp_ChatSystem
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\ChatSystem\Model\ResourceModel\Chat\Marketplace;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends \Lofmp\ChatSystem\Model\ResourceModel\Chat\Grid\Collection
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
     * DataProvider constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param SellerFactory $sellerFactory
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param null|string $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
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
     * @SuppressWarinings(PHPMD.CamelCaseMethodName)
     */
    public function _renderFiltersBefore()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()
            ->load($customerId, 'customer_id');
        $sellerId = $seller->getId();
        if ($sellerId) {
            $this->addFieldToFilter('main_table.seller_id', $sellerId);
        }
        parent::_renderFiltersBefore();
    }
}
