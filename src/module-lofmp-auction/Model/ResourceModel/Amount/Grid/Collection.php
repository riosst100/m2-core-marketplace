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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Model\ResourceModel\Amount\Grid;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * Lofmp\Auction\Model\ResourceModel\Amount\Grid
 */
class Collection extends \Lof\Auction\Model\ResourceModel\Amount\Grid\Collection
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
     * @var \Lof\Auction\Model\ResourceModel\Product\Collection
     */
    private $auctionCollection;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param $mainTable
     * @param $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param Session $session
     * @param SellerFactory $sellerFactory
     * @param \Lof\Auction\Model\ResourceModel\Product\Collection $auctionCollection
     * @param string $model
     * @param null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        Session $session,
        SellerFactory $sellerFactory,
        \Lof\Auction\Model\ResourceModel\Product\Collection $auctionCollection,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        AbstractDb $resource = null
    ) {
        $this->session = $session;
        $this->sellerFactory = $sellerFactory;
        $this->auctionCollection = $auctionCollection;
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
            $auctionIds = $this->auctionCollection->addFieldToFilter('seller_id', $sellerId)->getAllIds();
            $this->addFieldToFilter('auction_id', ['in' => $auctionIds]);
        }
        parent::_renderFiltersBefore();
    }
}
