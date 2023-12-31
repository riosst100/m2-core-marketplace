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

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupModel;

    /**
     * @var \Lof\MarketPlace\Model\Vacation
     */
    protected $vacation;

    /**
     * @var \Lof\MarketPlace\Helper\DateTime
     */
    protected $_helperDateTime;

    /**
     * @var mixed|array
     */
    protected $_vacationData = [];

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Group $groupModel
     * @param \Lof\MarketPlace\Model\Vacation $vacation
     * @param \Lof\MarketPlace\Helper\DateTime $helperDateTime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Group $groupModel,
        \Lof\MarketPlace\Model\Vacation $vacation,
        \Lof\MarketPlace\Helper\DateTime $helperDateTime,
        array $data = []
    ) {
        $this->vacation = $vacation;
        $this->_sellerHelper = $sellerHelper;
        $this->_catalogLayer = $layerResolver->get();
        $this->_coreRegistry = $registry;
        $this->_groupModel = $groupModel;
        $this->_helperDateTime = $helperDateTime;
        parent::__construct($context, $data);
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $seller
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $sellerRoute = $this->_sellerHelper->getConfig('general_settings/route');
        $sellerRoute = $sellerRoute ? $sellerRoute : "lofmarketplace/index/index";
        $allSellerPageTitle = $this->_sellerHelper->getConfig('seller_list_page/page_title');
        $showSellerGroupName = $this->_sellerHelper->getConfig('seller_profile_page/show_seller_group_name');

        $seller = $this->getCurrentSeller();
        if ($seller->getData('shop_title')) {
            $page_title = $seller->getData('shop_title');
        } else {
            $page_title = $seller->getName();
        }
        $group = false;
        if ($groupId = $seller->getGroupId()) {
            $group = $this->_groupModel->load($groupId);
        }
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
                    'label' => $allSellerPageTitle,
                    'title' => $allSellerPageTitle,
                    'link' => $baseUrl . $sellerRoute
                ]
            );
            if ($showSellerGroupName) {
                if ($group && $group->getStatus()) {
                    $breadcrumbsBlock->addCrumb(
                        'group',
                        [
                            'label' => $group->getName(),
                            'title' => $group->getName(),
                            'link' => $group->getUrl()
                        ]
                    );
                }
            }

            $breadcrumbsBlock->addCrumb(
                'seller',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link' => ''
                ]
            );
        }
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
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getVacation()
    {
        $seller = $this->getCurrentSeller();
        $seller_id = $seller->getData('seller_id');
        if (!isset($this->_vacationData[$seller_id])) {
            $today = $this->_helperDateTime->getTimezoneDateTime();
            $this->_vacationData[$seller_id] = $this->vacation->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('seller_id', $seller_id)
                ->addFieldToFilter('from_date', ["lteq" => $today])
                ->addFieldToFilter('to_date', ["gt" => $today])
                ->getFirstItem();
        }
        return $this->_vacationData[$seller_id];
    }

    /**
     * @return View
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $seller = $this->getCurrentSeller();
        if ($seller->getData('shop_title')) {
            $page_title = $seller->getData('shop_title');
        } else {
            $page_title = $seller->getName();
        }
        $meta_description = $seller->getMetaDescription();
        $meta_keywords = $seller->getMetaKeywords();
        $this->_addBreadcrumbs();
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
     * @return string|void
     */
    public function _toHtml()
    {
        if ($this->getCurrentSeller()->getData('status') == 0) {
            return;
        }

        return parent::_toHtml();
    }
}
