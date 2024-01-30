<?php

namespace CoreMarketplace\SellerIdentificationApproval\Block\Marketplace\Seller;

use Magento\Framework\DataObject;
use Lof\MarketPlace\Model\Seller;
use Magento\Customer\Model\Session;

class Notifications extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var Session
     */
    private $session;

    /**
     * Chat constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Seller $seller
     * @param Session $customerSession
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Seller $seller,
        Session $customerSession
    ) {
        $this->seller = $seller;
        $this->session = $customerSession;
        parent::__construct($context);
    }

    public function countUnread()
    {
        $seller = $this->getSeller();
        if ($seller) {
            return $seller->getDocumentsVerifyStatus() == 0 || $seller->getDocumentsVerifyStatus() == 3 ? 1 : 0;
        }
    }

    /**
     * @return DataObject
     */
    public function getSeller()
    {
        if ($sellerId = $this->getRequest()->getParam('seller_id')) {
            return $this->seller->load($sellerId);
        } else {
            return $this->seller->getCollection()->addFieldToFilter(
                'customer_id',
                $this->session->getId()
            )->getFirstItem();
        }
    }
}
