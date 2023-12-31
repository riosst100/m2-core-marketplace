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

use Lof\MarketPlace\Model\Rating as SellerRating;

class Rating extends \Magento\Framework\View\Element\Template
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
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $page;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Rating\Collection|null
     */
    protected $_ratingCollection = null;

    /**
     * Rating constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Theme\Block\Html\Pager $page
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Rating $rating,
        \Magento\Theme\Block\Html\Pager $page,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->rating = $rating;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->page = $page;
        $this->logger = $logger;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    Public function getCustomCollection()
    {
        if ($this->getCurrentSeller() && !$this->_ratingCollection) {
            $sellerId = $this->getCurrentSeller()->getData('seller_id');

            //get values of current page
            $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
            //get values of current limit
            $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;
            /** @var \Lof\MarketPlace\Model\ResourceModel\Rating\Collection $collection */
            $collection = $this->rating->getCollection();
            $collection->addFieldToFilter("seller_id", $sellerId);
            $collection->addFieldToFilter("status", SellerRating::STATUS_ACCEPT);
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);

            $sortingBy = $this->_helper->getConfig("general_settings/rating_sortby");
            $sortingBy = $sortingBy ? $sortingBy : "latest"; //support: latest, bestRating, oldest, helpful, verified, recommended
            switch ($sortingBy) {
                case "bestRating":
                    $collection->getSelect()->order("main_table.rating DESC");
                    break;
                case "oldest":
                    $collection->getSelect()->order("main_table.created_at ASC");
                    break;
                case "helpful":
                    $collection->getSelect()->order("main_table.plus_review DESC");
                    break;
                case "verified":
                    $collection->getSelect()->order("main_table.verified_buyer DESC");
                    break;
                case "recommended":
                    $collection->getSelect()->order("main_table.is_recommended DESC");
                    break;
                case "latest":
                default:
                    $collection->getSelect()->order("main_table.created_at DESC");
                    break;
            }

            $this->_ratingCollection = $collection;
        }
        return $this->_ratingCollection;
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
    public function getRating()
    {
        if ($this->getCurrentSeller()) {
            $seller_id = $this->getCurrentSeller()->getData('seller_id');
        } else {
            $seller_id = $this->getSellerId();
        }
        $rating = $this->rating->getCollection()
            ->addFieldToFilter('seller_id', $seller_id)
            ->addFieldToFilter('status', SellerRating::STATUS_ACCEPT);

        return $rating;
    }

    /**
     * @return int
     */
    public function getCountRating()
    {
        return $this->getRating()->getSize();
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        $seller = $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getFirstItem()->getData();
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
        if ($count != 0) {
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
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        switch ($status) {
            case SellerRating::STATUS_PENDING:
                return __('Pending');
            case SellerRating::STATUS_ACCEPT:
                return __('Accepted');
            default:
                return __('Accepted');
        }
    }
}
