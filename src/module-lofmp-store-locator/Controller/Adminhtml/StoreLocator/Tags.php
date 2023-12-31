<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Controller\Adminhtml\StoreLocator;

class Tags extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Get post grid and serializer block
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getparam('tag_id');
        $storelocator = $this->_objectManager->create('Lofmp\StoreLocator\Model\StoreLocator');
        $storelocator->load($id);
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("current_storelocator", $storelocator);
        

        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('storelocator.edit.tab.tags')
            ->setProducts($this->getRequest()->getPost('tags', null));

        return $resultLayout;
    }
}
