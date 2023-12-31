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

namespace Lof\MarketPlace\Controller\Marketplace\Messageadmin;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\ResourceModel\MessageAdmin\CollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory as DetailCollectionFactory;

class MassDelete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var DetailCollectionFactory
     */
    protected $detailCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Magento\Framework\App\Action\Session|\Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SalesFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DetailCollectionFactory $detailCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DetailCollectionFactory $detailCollectionFactory
    ) {
        parent::__construct($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->detailCollectionFactory = $detailCollectionFactory;
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
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|\Exception
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller && $seller->getId() ? $seller->getStatus() : 0;

        if ($customerSession->isLoggedIn() && $status == 1) {
            $sellerId = $seller->getId();
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collection->addFieldToFilter("seller_id", (int)$sellerId);
            $i=0;
            $messageIds = [];
            /** delete mesasge listing */
            foreach ($collection as $item) {
                if ((int)$item->getSellerId() == (int)$sellerId) {
                    $i++;
                    $messageIds[] = $item->getId();
                    $item->delete();
                }
            }
            /** Delete message details */
            $detailCollection = $this->detailCollectionFactory->create()
                                ->addFieldToFilter("message_admin", 1)
                                ->addFieldToFilter("message_id", ["in" => $messageIds]);
            foreach ($detailCollection as $item) {
                $item->delete();
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $i));
            $this->_redirect('catalog/message/admin');
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
