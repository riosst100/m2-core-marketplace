<?php

namespace CoreMarketplace\MarketPlace\Block\Adminhtml\Notifications;

class Message extends \Lof\MarketPlace\Block\Adminhtml\Notifications\Message
{
    /**
     * @var \Lof\MarketPlace\Model\MessageAdmin
     */
    protected $message;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerProductFactory
     */
    protected $sellerProductFactory;

    /**
     * @var \Lof\MarketPlace\Model\WithdrawalFactory
     */
    protected $withdrawlFactory;

    /**
     * Message constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Lof\MarketPlace\Model\MessageAdmin $message
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory
     * @param \Lof\MarketPlace\Model\WithdrawalFactory $withdrawlFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\MarketPlace\Model\MessageAdmin $message,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory,
        \Lof\MarketPlace\Model\WithdrawalFactory $withdrawlFactory
    ) {
        $this->message = $message;
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->withdrawlFactory = $withdrawlFactory;
        parent::__construct(
            $context,
            $message,
            $sellerFactory,
            $sellerProductFactory,
            $withdrawlFactory
        );
    }

    /**
     * @return int|void
     */
    public function countPendingSellers()
    {
        $collection = $this->sellerFactory->create()
        ->getCollection()
        ->addFieldToFilter('status', 2)
        ->addFieldToFilter(['documents_verify_status', 'documents_verify_status'],
        [
            ['eq' => 2]
        ]);

        return $collection->count();
    }
}
