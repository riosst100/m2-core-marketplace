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
 * @package    Lofmp_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\PreOrder\Controller\Marketplace\PreOrder;

use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\PreOrder\Helper\Data;
use Lofmp\PreOrder\Controller\Marketplace\Preorder;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Lofmp\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class MassNotify extends Preorder implements HttpGetActionInterface, HttpPostActionInterface
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Data
     */
    protected $_preorderHelper;

    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var SellerFactory
     */
    private $sellerFactory;

    /**
     * @var Url
     */
    private $_frontendUrl;

    /**
     * MassNotify constructor.
     * @param Action\Context $context
     * @param Data $preorderHelper
     * @param Filter $filter
     * @param CollectionFactory $preorderCollection
     * @param Data $helper
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param Url $frontendUrl
     */
    public function __construct(
        Action\Context $context,
        Data $preorderHelper,
        Filter $filter,
        CollectionFactory $preorderCollection,
        Data $helper,
        Session $customerSession,
        SellerFactory $sellerFactory,
        Url $frontendUrl
    ) {
        $this->filter = $filter;
        $this->_preorderHelper = $preorderHelper;
        $this->_preorderCollection = $preorderCollection;
        $this->helper = $helper;
        $this->session = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        parent::__construct($context);
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return ResponseInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * phpcs:disable Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
     */
    public function execute()
    {
        if (!$this->helper->allowSellerManage()) {
            $this->messageManager->addError(__("The feature is not available at now."));
            return $this->_redirect('catalog/dashboard');
        }
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()
            ->load($customerId, 'customer_id')
            ->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $helper = $this->_preorderHelper;
            $info = $emailIds = [];

            $collection = $this->filter
                ->getCollection($this->_preorderCollection->create());

            foreach ($collection as $item) {
                $info[] = $item->getProductId();
                $emailIds[] = $item->getCustomerEmail();
            }
            $collectionCount = count($collection);
            if ($collectionCount >= 1) {
                for ($i = 0; $i < $collectionCount; $i++) {
                    $stockDetails = $helper->getStockDetails($info[$i]);
                    $emailId = [];
                    if ($stockDetails['is_in_stock'] == 1) {
                        $emailId[] = $emailIds[$i];
                        $helper->sendNotifyEmail($emailId, $stockDetails['name']);
                        $this->messageManager->addSuccess(__('Email sent succesfully.'));
                    }
                }
            }
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');

        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
