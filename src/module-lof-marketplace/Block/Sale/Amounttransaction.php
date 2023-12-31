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

namespace Lof\MarketPlace\Block\Sale;

class Amounttransaction extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\Amounttransaction
     */
    protected $amounttransaction;

    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $seller;

    /**
     * Amounttransaction constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Amounttransaction $amounttransaction
     * @param \Lof\MarketPlace\Model\SellerFactory $seller
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Amounttransaction $amounttransaction,
        \Lof\MarketPlace\Model\SellerFactory $seller,
        array $data = []
    ) {
        $this->amounttransaction = $amounttransaction;
        $this->seller = $seller;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function isSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->seller->create()->load('customer_id', $customerId)->getStatus();
            return $status;
        }
        return false;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getAmounttransaction()
    {
        return $this->amounttransaction->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId());
    }

    /**
     * @return array|bool|mixed|null
     */
    public function getSellerId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $seller = $this->seller->create()->load($customerId, 'customer_id');
            return $seller->getData('seller_id');
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return Amounttransaction
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Amount Transactions'));
        return parent::_prepareLayout();
    }
}
