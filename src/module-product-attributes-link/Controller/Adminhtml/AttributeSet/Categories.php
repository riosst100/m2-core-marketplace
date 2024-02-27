<?php

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\AttributeSet;

class Categories extends \CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index
{
    /**
     * Attribute Set Mapping list page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Categories - Attribute Set Mapping'));
        return $resultPage;
    }
}
