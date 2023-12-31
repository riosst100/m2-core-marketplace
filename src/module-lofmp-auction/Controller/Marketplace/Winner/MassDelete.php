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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Controller\Marketplace\Winner;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\RequestInterface;
use Lof\Auction\Model\ResourceModel\WinnerData\CollectionFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\MarketPlace\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Data $helper
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Data $helper,
        Session $customerSession,
        Url $customerUrl,
        ProductFactory $productFactory
    ) {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->auctionFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $urlModel = $this->_customerUrl;
        $loginUrl = $urlModel->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * @param $auctionId
     * @return bool
     */
    public function validate($auctionId)
    {
        $sellerId = $this->helper->getSellerId();
        $auction = $this->auctionFactory->create()->load($auctionId);
        if ($auction->getSellerId() != $sellerId) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to delete this'));
            return false;
        } else {
            return true;
        }
    }
    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $total = 0;
        foreach ($collection as $item) {
            if($this->validate($item->getAuctionId())){
                $item->delete();
                $total++;
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $total));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
