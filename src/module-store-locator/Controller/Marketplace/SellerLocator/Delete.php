<?php

namespace CoreMarketplace\StoreLocator\Controller\Marketplace\SellerLocator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Delete extends Action
{
    /**
     * @var \Lofmp\StoreLocator\Model\StoreLocatorFactory
     */
    protected $storeLocatorFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    public function __construct(
        Context $context,
        \Lofmp\StoreLocator\Model\StoreLocatorFactory $storeLocatorFactory,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->storeLocatorFactory = $storeLocatorFactory;
        $this->_frontendUrl = $frontendUrl;
        parent::__construct (
            $context
        );
    }

    public function execute()
    {
        $collection = $this->storeLocatorFactory->create()
        ->getCollection()
        ->addFieldToFilter('storelocator_id', $this->getRequest()->getParam('id'));
        if ($collection->getSize()) {
            $collection->getFirstItem()->delete();

            $this->messageManager->addSuccessMessage(__('Store Locator deleted successfuly!'));
        } else {
            $this->messageManager->addErrorMessage(__('Store not found.'));
        }

        return $this->_redirect ( "catalog/sellerlocator/grid" );
    }

    public function getFrontendUrl($route = '', $params = []) 
    {
        return $this->_frontendUrl->getUrl($route,$params);
    }
}
