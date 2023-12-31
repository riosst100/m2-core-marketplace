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

namespace Lofmp\Auction\Controller\Marketplace\ManualBid;

use Exception;
use Lof\Auction\Model\ProductFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\MarketPlace\Helper\Data;

class MassDelete extends \Lofmp\Auction\Controller\Marketplace\Delete
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param Filter $filter
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        Filter $filter,
        Data $helper,
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $helper, $productFactory);
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
