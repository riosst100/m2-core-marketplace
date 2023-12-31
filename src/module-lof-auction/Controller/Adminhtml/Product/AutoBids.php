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
 * Class AutoBids
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class AutoBids extends Product
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
     * Get messages grid and serializer block
     *
     * @return Layout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {

        $id = $this->getRequest()->getparam('id');
        $form = $this->_objectManager->create('Lof\Auction\Model\Product');
        $form->load($id);
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("current_autobids", $form);

        $this->productBuilder->build($this->getRequest());
        return $this->resultLayoutFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::auto_bids');
    }
}
