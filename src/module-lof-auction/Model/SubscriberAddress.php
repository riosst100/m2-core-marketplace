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


namespace Lof\Auction\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class SubscriberAddress
 * @package Lof\Auction\Model
 */
class SubscriberAddress extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Lof\Auction\Model\ResourceModel\SubscriberAddress');
    }

    public function processSubscriber($object, $customer_email) {
        if($object && $object->getAuctionId() && $object->getCustomerId()){
            $foundSubscriber = $this->getCollection()
                        ->addFieldToFilter("customer_id", $object->getCustomerId())
                        ->addFieldToFilter("auction_id", $object->getAuctionId())
                        ->getFirstItem();

            if(!$foundSubscriber || ($foundSubscriber && !$foundSubscriber->getId())){
                $this->setAuctionId($object->getAuctionId());
                $this->setCustomerId($object->getCustomerId());
                $this->setCustomerEmail($customer_email);
                $this->setIsSent(0);
                $this->setId(null);
                $this->save();
            }
        }
    }
}
