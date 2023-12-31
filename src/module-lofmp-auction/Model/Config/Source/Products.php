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

namespace Lofmp\Auction\Model\Config\Source;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Products
 * Lofmp\Auction\Model\Config\Source
 */
class Products implements OptionSourceInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var CollectionFactory
     */
    private $auctionCollection;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Seller
     */
    private $seller;

    /**
     * AuctionStatus constructor.
     * @param Data $data
     * @param CollectionFactory $auctionCollection
     * @param Session $session
     * @param Seller $seller
     */
    public function __construct(
        Data $data,
        CollectionFactory $auctionCollection,
        Session $session,
        Seller $seller
    ) {
        $this->helper = $data;
        $this->auctionCollection = $auctionCollection;
        $this->session = $session;
        $this->seller = $seller;
    }

    /**
     * Get Auction type labels array for option element.
     *
     * @return array
     */
    public function getOptions()
    {
        $collection = $this->auctionCollection->create();
        $customerId = $this->session->getCustomerId();
        $seller = $this->seller->load($customerId, 'customer_id');
        $sellerId = $seller->getData('seller_id');
        if ($sellerId) {
            $collection->addFieldToFilter('seller_id', $sellerId);
        }
        $res = [];
        foreach ($collection as $key => $auction) {
            $productId = $auction->getProductId();
            $productName = $this->helper->getProductById($productId)->getName();
            $res[] = ['value' => $productId, 'label' => $productName];
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
