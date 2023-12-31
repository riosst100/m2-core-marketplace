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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Block;

use Lofmp\SellerBadge\Model\SellerBadge\Image;
use Lofmp\SellerBadge\Helper\Data;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\CollectionFactory as SellerBadgeManagerCollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

abstract class AbstractBadge extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SellerBadgeManagerCollectionFactory
     */
    protected $sellerBadgeManagerCollectionFactory;

    /**
     * @var Image
     */
    protected $badgeImage;

    /***
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Badge constructor.
     * @param Context $context
     * @param Data $helperData
     * @param Registry $coreRegistry
     * @param SellerBadgeManagerCollectionFactory $sellerBadgeManagerCollectionFactory
     * @param Image $badgeImage
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        Registry $coreRegistry,
        SellerBadgeManagerCollectionFactory $sellerBadgeManagerCollectionFactory,
        Image $badgeImage,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->badgeImage = $badgeImage;
        $this->helperData = $helperData;
        $this->_coreRegistry = $coreRegistry;
        $this->sellerBadgeManagerCollectionFactory = $sellerBadgeManagerCollectionFactory;
    }

    /**
     * @return mixed
     */
    abstract public function canDisplay();

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helperData->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
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
     * @param $badge
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBadgeImage($badge)
    {
        if ($this->badgeImage->getUrl($badge, \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface::IMAGE)) {
            return $this->badgeImage->getUrl($badge, \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface::IMAGE);
        }
        return null;
    }

    /**
     * @param mixed|int|string|null $sellerId
     * @return \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\Collection
     */
    public function getBadgeCollection($sellerId = null)
    {
        if (!$sellerId) {
            $sellerId = $this->getRequest()->getParam('seller_id');
        }
        if (!empty($sellerId) && is_object($sellerId)) {
            $sellerId = (int)$sellerId->getId();
        }
        return $this->getSellerBadgeManagerCollection()->addBadgeBySellerId($sellerId);
    }

    /**
     * @param $badge
     * @return string
     */
    public function getBadgeTitle($badge): string
    {
        if ($badge->getDescription()) {
            return $badge->getBadgeName() . ': ' . $badge->getDescription();
        }
        return $badge->getBadgeName();
    }

    /**
     * @return \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\Collection
     */
    public function getSellerBadgeManagerCollection()
    {
        return $this->sellerBadgeManagerCollectionFactory->create();
    }
}
