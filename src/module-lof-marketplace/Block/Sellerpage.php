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

namespace Lof\MarketPlace\Block;

use Lof\MarketPlace\Model\Order;

class Sellerpage extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_seller;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;

    /**
     * @var mixed
     */
    protected $_collection;

    /**
     * @var mixed|array
     */
    protected $_sellerRate = [];

    /**
     * Sellerpage constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lof\MarketPlace\Model\Rating $rating,
        array $data = []
    ) {
        $this->_seller = $seller;
        $this->_coreRegistry = $registry;
        $this->_sellerHelper = $sellerHelper;
        $this->orderitems = $orderitems;
        $this->rating = $rating;
        parent::__construct($context, $data);
    }

    /**
     * @param int $sellerId
     * @return int
     */
    public function getTotalSales($sellerId)
    {
        $total = 0;
        if ((int)$this->_sellerHelper->getConfig("general_settings/show_total_sales")) {
            $orderitems = $this->orderitems->getCollection()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('status', Order::STATE_COMPLETED);

            foreach ($orderitems as $_orderitems) {
                $total = $total + $_orderitems->getProductQty();
            }
        }
        return $total;
    }

    /**
     * @param int $sellerId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getRating($sellerId)
    {
        return $this->rating->getCollection()->addFieldToFilter('seller_id', $sellerId);
    }

    /**
     * @param int $sellerId
     * @return int
     */
    public function getCountRating($sellerId)
    {
        return $this->getRating($sellerId)->getSize();
    }

    /**
     * @param int $sellerId
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRate($sellerId)
    {
        if (!isset($this->_sellerRate[$sellerId])) {
            $count = $total_rate = 0;
            $rate1 = $rate2 = $rate3 = $rate4 = $rate5 = 0;

            foreach ($this->getRating($sellerId) as $rating) {
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

            $this->_sellerRate[$sellerId] = $data;
        }
        return $this->_sellerRate[$sellerId];
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function _construct()
    {
        if (!$this->getConfig('general_settings/enable')) {
            return;
        }

        parent::_construct();
        $seller = $this->_seller;
        $sellerCollection = $seller->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore())
            ->setOrder('position', 'ASC');
        $this->setCollection($sellerCollection);

        $layout = $this->getConfig('seller_list_page/layout');
        if ($layout == 'grid') {
            $template = 'sellerlistpage_grid.phtml';
        } else {
            $template = 'sellerlistpage_list.phtml';
        }

        if (!$this->hasData('template')) {
            $this->setTemplate($template);
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = $this->_sellerHelper->getConfig('seller_list_page/page_title');

        if ($breadcrumbsBlock) {

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'lofmarketplace',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link' => ''
                ]
            );
        }
    }

    /**
     * Set seller collection
     * @param \Lof\MarketPlace\Model\Seller
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getConfig($key, $default = '')
    {
        $result = $this->_sellerHelper->getConfig($key);
        if (!$result) {

            return $default;
        }
        return $result;
    }

    /**
     * @return Sellerpage
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('seller_list_page/page_title');
        $meta_description = $this->getConfig('seller_list_page/meta_description');
        $meta_keywords = $this->getConfig('seller_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('lof-sellerlist');
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }

    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofmarketplace_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }

        return false;
    }

    /**
     * @return Sellerpage
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();

        if ($toolbar) {
            $itemsperpage = (int)$this->getConfig('seller_list_page/item_per_page', 12);
            $toolbar->setData('_current_limit', $itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }

        return parent::_beforeToHtml();
    }

    /**
     * @param mixed|string $social
     * @return bool
     */
    public function isAllowedSocial($social)
    {
        return $this->_sellerHelper->isAllowedSocial($social);
    }
}
