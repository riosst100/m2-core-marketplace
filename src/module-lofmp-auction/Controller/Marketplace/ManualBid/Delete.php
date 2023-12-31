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

use Lof\Auction\Model\ProductFactory;
use Lof\MarketPlace\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Lof\Auction\Model\AmountFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Ui\Component\MassAction\Filter;

class Delete extends \Lofmp\Auction\Controller\Marketplace\Delete
{
    /**
     * @var AmountFactory
     */
    private $amountFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param Filter $filter
     * @param Data $helper
     * @param AmountFactory $amountFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        Filter $filter,
        Data $helper,
        AmountFactory $amountFactory,
        ProductFactory $productFactory
    ) {
        $this->amountFactory = $amountFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $helper, $productFactory);
    }

    /**
     * Default Product rate
     *
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $fields = $this->getRequest()->getParams();
            if (!empty($fields)) {
                $model = $this->amountFactory->create()
                    ->load($fields['id']);
                if (!empty($model)) {
                    if ($this->validate($model->getAuctionId())) {
                        $model->delete();
                        $this->messageManager->addSuccessMessage(__('Auto bid is successfully Deleted!'));
                    }
                    return $resultRedirect->setPath('*/*/index');
                } else {
                    $this->messageManager->addError(__('No record Found!'));
                    return $resultRedirect->setPath('*/*/index');
                }
            } else {
                $this->messageManager->addSuccess(__('Please try again!'));
                return $resultRedirect->setPath('*/*/index');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
