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

namespace Lof\MarketPlace\Block\Adminhtml\Catalog\Form;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

class SellerContent extends \Magento\Backend\Block\Template
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param SellerFactory $seller
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        SellerFactory $seller,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->seller = $seller;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getRequest()->getControllerModule() === 'Lof_MarketPlace') {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerDashboardUrl()
    {
        return $this->_urlBuilder->getUrl(
            'lofmarketplace/seller/edit',
            ['seller_id' => $this->getSeller()->getData('seller_id')]
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerShopUrl()
    {
        return $this->getSeller() ? $this->getSeller()->getUrl() : '';
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSeller()
    {
        $productId = $this->getRequest()->getParam('id');
        try {
            $product = $this->_productRepository->getById($productId);
            $sellerId = $product->getSellerId();
            if ($sellerId) {
                return $this->seller->create()->load($sellerId);
            }
        } catch (\Exception $e) {
            //Do nothing
        }
        return null;
    }

    /**
     * @return null
     */
    public function getCacheLifetime()
    {
        return null;
    }
}
