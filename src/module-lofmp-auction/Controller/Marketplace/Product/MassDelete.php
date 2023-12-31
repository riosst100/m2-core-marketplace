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

namespace Lofmp\Auction\Controller\Marketplace\Product;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\Auction\Model\ProductFactory;
use Magento\Customer\Model\Url;

class MassDelete extends \Lofmp\Auction\Controller\Marketplace\Delete
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
     * @param ProductFactory $auctionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Data $helper,
        Session $customerSession,
        Url $customerUrl,
        ProductFactory $auctionFactory
    ) {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $helper, $auctionFactory);
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
