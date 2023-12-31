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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Observer;

use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\ProcessWinner;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Reports Event observer model.
 */
class CatalogControllerProductView implements ObserverInterface
{

    /**
     * @var Configurable
     */
    protected $_configurableProTypeModel;
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var ProcessWinner
     */
    protected $_processWinner;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * CatalogControllerProductView constructor.
     * @param Configurable $configProTypeModel
     * @param Data $helperData
     * @param ProcessWinner $processWinner
     * @param ProductFactory $auctionProductFactory
     */
    public function __construct(
        Configurable $configProTypeModel,
        Data $helperData,
        ProcessWinner $processWinner,
        ProductFactory $auctionProductFactory
    ) {
        $this->_configurableProTypeModel = $configProTypeModel;
        $this->_helperData = $helperData;
        $this->_processWinner = $processWinner;
        $this->_auctionProductFactory = $auctionProductFactory;
    }

    /**
     * View Catalog Product View observer.
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $auctionConfig = $this->_helperData->getAuctionConfiguration();
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        if ($product && $auctionConfig['enable']) {
            $productId = $product->getId();
            if ($product->getTypeId() == 'configurable') {
                $childPro = $this->_configurableProTypeModel->getChildrenIds($productId);
                $childProIds = isset($childPro[0]) ? $childPro[0] : [0];
                $auctionActPro = $this->_auctionProductFactory->create()->getCollection()
                    ->addFieldToFilter('product_id', ['in' => $childProIds])
                    ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS);
                if (count($auctionActPro)) {
                    foreach ($auctionActPro as $value) {
                        $this->_processWinner->processFindWinner($value->getProductId());
                    }
                }
            } else {
                $this->_processWinner->processFindWinner($productId);
            }
        }
        return $this;
    }
}
