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

namespace Lof\MarketPlace\Controller\Group;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_groupModel;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\Group $groupModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\Group $groupModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Lof\MarketPlace\Helper\Data $sellerHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_groupModel = $groupModel;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_sellerHelper = $sellerHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool|\Lof\MarketPlace\Model\Group|\Lof\MarketPlace\Model\Seller
     */
    public function _initGroup()
    {
        $groupId = (int)$this->getRequest()->getParam('group_id', false);
        if (!$groupId) {
            return false;
        }
        try {
            $group = $this->_groupModel->load($groupId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_group_seller', $group);
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->_sellerHelper->getConfig('general_settings/enable')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $group = $this->_initGroup();
        if ($group) {
            $page = $this->resultPageFactory->create();
            $page->getConfig()->addBodyClass('group-' . $group->getUrlKey());
            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        return null;
    }
}
