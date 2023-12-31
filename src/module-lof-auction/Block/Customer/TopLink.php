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

namespace Lof\Auction\Block\Customer;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\Product;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class TopLink
 * @package Lof\Auction\Block\Customer
 */
class TopLink extends Link
{

    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Product
     **/
    protected $auctionProduct;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Product $auctionProduct
     * @param array
     */
    public function __construct(
        Context $context,
        Data $helper,
        Product $auctionProduct,
        array $data = []
    ) {
        $this->auctionProduct = $auctionProduct;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getHref()
    {
        $route = $this->helper->getConfig("general_settings/route");
        if ($route) {
            $url = $this->getUrl($route);
        } else {
            $url = $this->getUrl('lofauction/bid/index');
        }

        return $url;
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     */
    public function getAuction()
    {
        return $this->auctionProduct->getCollection();
    }

    /**
     * Render block HTML
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function _toHtml()
    {
        return '<li><a href="' . $this->getHref() . '"> ' . $this->getLabel() . ' </a></li>';
    }

    /**
     * Function to Get Label on Top Link
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Auctions');
    }
}
