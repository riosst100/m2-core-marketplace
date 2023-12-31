<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Quickrfq\Block\Marketplace\Quickrfq;

use Lof\Quickrfq\Model\Quickrfq;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

class Index extends Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Session
     */
    private $session;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param Quickrfq $quickrfq
     * @param Session $customerSession
     * @param ResourceConnection $resource
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\Quickrfq\Model\Quickrfq $quickrfq,
        Session $customerSession,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->quickrfq = $quickrfq;
        $this->_helper        = $helper;
        $this->_coreRegistry  = $registry;
        $this->_resource      = $resource;
        $this->session           = $customerSession;
        parent::__construct($context);
    }
    /**
     *  get Quickrfq Colection
     *
     * @return Object
     */
    public function getQuickrfqCollection()
    {
        $store            = $this->_storeManager->getStore();
        $quickrfqCollection = $this->quickrfq->getCollection();
        return $quickrfqCollection;
    }

    public function getQuickrfq()
    {
        if ($this->getCurrentQuickrfq()) {
            $quickrfq_id = $this->getCurrentQuickrfq()->getData('quickrfq_id');
        } else {
            $quickrfq_id = $this->getQuickrfqId();
        }
        $quickrfq = $this->quickrfq->getCollection()->addFieldToFilter('seller_id', $this->_helper->getSellerId());
        return $quickrfq;
    }

    public function getCurrentQuickrfq()
    {
        $quickrfq = $this->_coreRegistry->registry('current_Quickrfq');
        if ($quickrfq) {
            $this->setData('current_Quickrfq', $quickrfq);
        }
        return $quickrfq;
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
