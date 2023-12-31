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

namespace Lof\Auction\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * Class MaxabsenteeGrid
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class MaxabsenteeGrid extends Product
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Get crosssell products grid
     *
     * @return Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getparam('auction_id');
        $form = $this->_objectManager->create('Lof\Auction\Model\Product');
        $form->load($id);

        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("current_bids", $form);

        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('lofauction.form.edit.tab.maxabsentee')
            ->setProductsRelated($this->getRequest()->getPost('relatedabsentees', null));
        return $resultLayout;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::manual_bids_grid');
    }
}
