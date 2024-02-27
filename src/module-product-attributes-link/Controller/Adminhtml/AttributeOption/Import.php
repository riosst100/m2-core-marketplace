<?php

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\AttributeOption;

class Import extends \CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Import Attributes Options Mapping'));
        return $resultPage;
    }
}
