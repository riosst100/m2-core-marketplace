<?php

namespace CoreMarketplace\SellerMembership\Controller\Buy;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\NotFoundException;

class Index extends \Lofmp\SellerMembership\Controller\Buy\Index
{
    /**
     * Display customer wishlist
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
