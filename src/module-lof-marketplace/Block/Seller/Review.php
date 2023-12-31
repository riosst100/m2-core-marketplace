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

class Review extends \Magento\Framework\View\Element\Template
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
     * @var \Lof\MarketPlace\Model\Review
     */
    protected $review;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var  \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Review constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Review $review
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Review $review,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->review = $review;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getReview()
    {
        if ($this->getCurrentSeller()) {
            $seller_id = $this->getCurrentSeller()->getData('seller_id');
        } else {
            $seller_id = $this->getSellerId();
        }
        $review = $this->review->getCollection()->addFieldToFilter('seller_id', $seller_id);
        return $review;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        $seller = $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getFirstItem()
            ->getData();
        $seller_id = $seller['seller_id'];

        return $seller_id;
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
        foreach ($this->getReview() as $review) {
            if ($review->getData('rate1') > 0) {
                $count++;
                $total_rate = $total_rate + $review->getData('rate1');
                if ($review->getData('rate1') == 1) {
                    $rate1++;
                } elseif ($review->getData('rate1') == 2) {
                    $rate2++;
                } elseif ($review->getData('rate1') == 3) {
                    $rate3++;
                } elseif ($review->getData('rate1') == 4) {
                    $rate4++;
                } elseif ($review->getData('rate1') == 5) {
                    $rate5++;
                }
            }

            if ($review->getData('rate2') > 0) {
                $count++;
                $total_rate = $total_rate + $review->getData('rate2');
                if ($review->getData('rate2') == 1) {
                    $rate1++;
                } elseif ($review->getData('rate2') == 2) {
                    $rate2++;
                } elseif ($review->getData('rate2') == 3) {
                    $rate3++;
                } elseif ($review->getData('rate2') == 4) {
                    $rate4++;
                } elseif ($review->getData('rate2') == 5) {
                    $rate5++;
                }
            }

            if ($review->getData('rate3') > 0) {
                $count++;
                $total_rate = $total_rate + $review->getData('rate3');
                if ($review->getData('rate3') == 1) {
                    $rate1++;
                } elseif ($review->getData('rate3') == 2) {
                    $rate2++;
                } elseif ($review->getData('rate3') == 3) {
                    $rate3++;
                } elseif ($review->getData('rate3') == 4) {
                    $rate4++;
                } elseif ($review->getData('rate3') == 5) {
                    $rate5++;
                }
            }
        }

        $data = [];
        $average = ($total_rate / $count);
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
     * @return Review
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Reviews'));
        return parent::_prepareLayout();
    }
}
