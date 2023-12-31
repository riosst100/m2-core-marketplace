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

namespace Lof\Auction\Ui\Component\Listing\Column;

use Lof\Auction\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Status
 * @package Lof\Auction\Ui\Component\Listing\Column
 */
class StatusAuction extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var ProductRepositoryInterface
     */
    private $_auction;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $auction,
        array $components = [],
        array $data = []
    ) {
        $this->_auction = $auction;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $statuses = [
            '0' => __('TIME END'),
            '1' => __('PROCESSING'),
            '2' => __('WINNER NOT DEFINED'),
            '3' => __('CANCELED'),
            '4' => __('COMPLETED'),
        ];

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['auction_id'])) {
                    $auction = $this->_auction->getById($item['auction_id']);
                    $status = $auction->getAuctionStatus();
                } else {
                    $status = $item[$this->getData('name')];
                }
                $item[$this->getData('name')] = $statuses[$status];
            }
        }

        return $dataSource;
    }
}
