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

namespace Lof\Auction\Block\Widget\Grid;

use Lof\Auction\Model\Product;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Extended
 * @package Lof\Auction\Block\Widget\Grid
 */
class Extended extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Contact\Helper\Data
     */
    protected $contactHelper;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var Product
     **/
    protected $auctionProduct;

    /**
     * Extended constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param Session $customerSession
     * @param \Magento\Contact\Helper\Data $contactHelper
     * @param CustomerFactory $customerFactory
     * @param CurrentCustomer $currentCustomer
     * @param Product $auctionProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Session $customerSession,
        \Magento\Contact\Helper\Data $contactHelper,
        CustomerFactory $customerFactory,
        CurrentCustomer $currentCustomer,
        Product $auctionProduct,
        array $data = []
    ) {
        $this->auctionProduct = $auctionProduct;
        $this->currentCustomer = $currentCustomer;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->contactHelper = $contactHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     */
    public function getAuction()
    {
        return $this->auctionProduct->getCollection();
    }
}
