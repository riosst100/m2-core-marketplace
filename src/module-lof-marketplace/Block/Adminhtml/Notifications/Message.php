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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Adminhtml\Notifications;

class Message extends \Magento\Framework\View\Element\Template
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
        parent::__construct($context);
    }

    /**
     * @return int|void
     */
    public function countUnread()
    {
        $collection = $this->message->getCollection()->addFieldToFilter('is_read', 0);
        return $collection->count();
    }

    /**
     * @return int|void
     */
    public function countPendingSellers()
    {
        $collection = $this->sellerFactory->create()->getCollection()->addFieldToFilter('status', 2);
        return $collection->count();
    }

    /**
     * @return int|void
     */
    public function countPendingProducts()
    {
        $collection = $this->sellerProductFactory->create()->getCollection()->addFieldToFilter('status', 1);
        return $collection->count();
    }

    /**
     * @return int|void
     */
    public function countWithdrawl()
    {
        $collection = $this->withdrawlFactory->create()->getCollection()->addFieldToFilter('status', 0);
        return $collection->count();
    }

    /**
     * @return int|void
     */
    public function countApprovedSellers()
    {
        $collection = $this->sellerFactory->create()->getCollection()->addFieldToFilter('status', 1);
        return $collection->count();
    }

}
