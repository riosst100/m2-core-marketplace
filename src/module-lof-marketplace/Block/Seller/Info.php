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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Seller;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * Info constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Rating $rating,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->rating = $rating;
        $this->orderitems = $orderitems;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        return $this->_sellerFactory->getCollection();
    }

    /**
     * @return int
     */
    public function getTotalSales()
    {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()
            ->addFieldToFilter('seller_id', $this->getCurrentSeller()->getData('seller_id'))
            ->addFieldToFilter('status', 'complete');
        foreach ($orderitems as $_orderitems) {
            $total = $total + $_orderitems->getProductQty();
        }

        return $total;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }

    /**
     * @return string|void
     */
    public function _toHtml()
    {
        if ($this->getCurrentSeller()->getData('status') == 0) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getRating()
    {
        return $this->rating->getCollection()
            ->addFieldToFilter('seller_id', $this->getCurrentSeller()->getData('seller_id'))
            ->addFieldToFilter('status', 'accept');
    }

    /**
     * @return int
     */
    public function getCountRating()
    {
        return $this->getRating()->getSize();
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRate()
    {
        $count = $total_rate = 0;
        $rate1 = $rate2 = $rate3 = $rate4 = $rate5 = 0;
        foreach ($this->getRating() as $rating) {
            if ($rating->getData('rate1') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate1');
                if ($rating->getData('rate1') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate1') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate1') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate1') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate1') == 5) {
                    $rate5++;
                }
            }

            if ($rating->getData('rate2') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate2');
                if ($rating->getData('rate2') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate2') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate2') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate2') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate2') == 5) {
                    $rate5++;
                }
            }

            if ($rating->getData('rate3') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate3');
                if ($rating->getData('rate3') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate3') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate3') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate3') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate3') == 5) {
                    $rate5++;
                }
            }
        }

        $data = [];
        if ($count > 0) {
            $average = ($total_rate / $count);
        } else {
            $average = 0;
        }

        $data['count'] = $count;
        $data['total_rate'] = $total_rate;
        $data['average'] = $average;
        $data['rate'] = [];
        $data['rate'][1] = $rate1;
        $data['rate'][2] = $rate2;
        $data['rate'][3] = $rate3;
        $data['rate'][4] = $rate4;
        $data['rate'][5] = $rate5;

        return $data;
    }

    /**
     * @param $social
     * @return bool
     */
    public function isAllowedSocial($social)
    {
        return $this->_helper->isAllowedSocial($social);
    }
}
