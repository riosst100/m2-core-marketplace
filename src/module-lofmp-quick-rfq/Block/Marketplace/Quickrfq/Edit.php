<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
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

use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\Seller;
use Lof\Quickrfq\Model\Quickrfq;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template\Context;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_sellerCollection;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper = null;

    /**
     * @var ResourceConnection
     */
    protected $_resource = null;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * @var ResourceConnection
     */
    protected $request;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    /**
     * @var Quickrfq
     */
    private $quickrfq;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $sellerHelper
     * @param Seller $sellerCollection
     * @param Quickrfq $quickrfq
     * @param ResourceConnection $resource
     * @param Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $sellerHelper,
        Seller $sellerCollection,
        Quickrfq $quickrfq,
        ResourceConnection $resource,
        DateTime $dateTime,
        Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTime = $dateTime;
        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->_coreRegistry = $registry;
        $this->request = $resource;
        $this->storeManager =  $context->getStoreManager();
        $this->storeManager =  $request;
        $this->quickrfq =  $quickrfq;
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Update Quote'));
        return parent::_prepareLayout();
    }

    public function convertGmtTimeStamp($date)
    {
        $timeStamp = $this->dateTime->gmtTimestamp($date);
        return $timeStamp;
    }
    public function getQuickrfqByID()
    {
        $data = null;
        $quote_id = $this->getRequest()->getParam('quickrfq_id');
        $model = $this->quickrfq->load($quote_id, "quickrfq_id");
        if ($model->getId()) {
            $data = $model->getData();
        }
        return $data;
    }
}
