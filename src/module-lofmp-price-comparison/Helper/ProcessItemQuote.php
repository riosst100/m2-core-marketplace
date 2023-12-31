<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Helper;

use Magento\Framework\App\ObjectManager;

class ProcessItemQuote extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Request instance.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data
     *
     * */
    protected $_assignHelper;

    /**
     * @var \Lofmp\PriceComparison\Model\QuoteFactory
     */
    protected $_quote;

    /**
     * @var  \Lof\MarketPlace\Helper\Data
     */
    protected $marketData;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     * @param \Lof\MarketPlace\Helper\Data $marketData
     * @param \Lofmp\PriceComparison\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Lofmp\PriceComparison\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Data $marketData,
        \Lofmp\PriceComparison\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->marketData = $marketData;
        $this->_assignHelper = $helper;
        $this->_quote = $quoteFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context);
    }

    /**
     * execute process add item to price comparision quote
     *
     * @param int $productId
     * @param mixed $quote
     * @param int $assignId
     * @param int $sellerId
     * @param bool $isForce - force create new comparission quote, default = false
     * @return \Lofmp\PriceComparison\Model\Quote|null
     */
    public function execute($productId, $quote, $assignId = 0, $sellerId = 0, $isForce = false)
    {
        $quoteModel = null;
        if ($this->_assignHelper->isEnabled() && $productId) {
            $quoteId = $quote->getId();
            $ownerId = $this->_assignHelper->getSellerIdByProductId($productId);
            $customer_id = $this->marketData->getCustomerId();
            if (!$sellerId) {
                if ($assignId > 0) {
                    $sellerId = $this->_assignHelper->getAssignSellerIdByAssignId($assignId);
                } else {
                    $sellerId = $ownerId;
                }
            }
            $itemId = 0;
            $qty = 1;
            foreach ($quote->getAllVisibleItems() as $item) {
                $itemId = $item->getId();
                $qty = $item->getQty();
            }
            if ($isForce || $this->_assignHelper->isNewProduct($productId, $assignId)) {
                /** @var \Lofmp\PriceComparison\Model\Quote $quoteModel */
                $quoteModel = $this->_quote->create();
                $quoteData = [
                                'item_id' => $itemId,
                                'seller_id' => $sellerId,
                                'customer_id' => $customer_id,
                                'owner_id' => $ownerId,
                                'qty' => $qty,
                                'product_id' => $productId,
                                'assign_id' => $assignId,
                                'quote_id' => $quoteId,
                            ];
                $quoteModel->setData($quoteData)->save();
            }
            //else check update item qty
        }
        return $quoteModel;
    }

    /**
     * after split quote
     *
     * @param mixed $splitQuote
     * @param mixed $oldItems
     */
    public function afterSplitQuote($quote, $oldItems)
    {
        if ($quote && $oldItems) {
            foreach ($oldItems as $item) {
                $option = $item->getOptionByCode('info_buyRequest');
                $data = $option ? $this->serializer->unserialize($option->getValue()) : [];
                $assignId = isset($data["mpassignproduct_id"]) ? (int)$data["mpassignproduct_id"] : 0;
                $this->execute($item->getProductId(), $quote, $assignId, $item->getSellerId(), true);
            }
        }
    }
}
