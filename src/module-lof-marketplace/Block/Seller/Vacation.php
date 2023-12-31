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

namespace Lof\MarketPlace\Block\Seller;

class Vacation extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\VacationFactory
     */
    protected $vacation;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var
     */
    protected $detail;

    /**
     * @var \Lof\MarketPlace\Helper\DateTime
     */
    protected $_helperDateTime;

    /**
     * @var mixed|array
     */
    protected $_vacationData = [];

    /**
     * Vacation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\VacationFactory $vacation
     * @param \Lof\MarketPlace\Helper\DateTime $helperDateTime
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\VacationFactory $vacation,
        \Lof\MarketPlace\Helper\DateTime $helperDateTime,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->vacation = $vacation;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->session = $customerSession;
        $this->_helperDateTime = $helperDateTime;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed|string
     */
    public function getSellerId()
    {
        $seller = $this->getSeller();
        $sellerId = $seller?$seller->getId():0;
        if (!$sellerId) {
            $seller = $this->_sellerFactory->getCollection()
                ->addFieldToFilter('customer_id', $this->session->getId())->getData();

            foreach ($seller as $_seller) {
                $sellerId = (int)$_seller['seller_id'];
            }
        }

        return $sellerId;
    }

    /**
     * @return mixed|null
     */
    public function getSeller()
    {
        return $this->_coreRegistry->registry('current_seller');
    }

    /**
     * @return \Lof\MarketPlace\Model\Vacation
     */
    public function getVacation()
    {
        $sellerId = $this->getSellerId();
        if (!isset($this->_vacationData[$sellerId])) {
            $this->_vacationData[$sellerId] = $this->vacation->create()->load($sellerId, 'seller_id');
        }
        return $this->_vacationData[$sellerId];
    }

    /**
     * @return Vacation
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Vacation Settings'));
        return parent::_prepareLayout();
    }
}
