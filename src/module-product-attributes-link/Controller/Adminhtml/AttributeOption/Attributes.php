<?php

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\AttributeOption;

class Attributes extends \CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index
{
    /**
     * Attribute Option Mapping list page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Attributes - Attribute Options Mapping'));
        return $resultPage;
    }
}
