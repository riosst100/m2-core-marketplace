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

use Lof\MarketPlace\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Lof\Auction\Model\ProductFactory;
use Magento\Customer\Model\Url;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Delete
 * Lofmp\Auction\Controller\Marketplace\Product
 */
class Delete extends \Lofmp\Auction\Controller\Marketplace\Delete
{
    /**
     * @var ProductFactory
     */
    private $_mpproductModel;

    /**
     * Delete constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param Filter $filter
     * @param Data $helper
     * @param ProductFactory $auctionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        Filter $filter,
        Data $helper,
        ProductFactory $auctionFactory
    ) {
        $this->_mpproductModel = $auctionFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $helper, $auctionFactory);
    }
    /**
     * Default Product rate
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $fields = $this->getRequest()->getParams();
            if (!empty($fields)) {
                $productModel = $this->_mpproductModel->create()
                    ->load($fields['id']);
                if (!empty($productModel)) {
                    if ($this->validate($productModel->getAuctionId())) {
                        $productModel->delete();
                        $this->messageManager->addSuccess(__('Product auction is successfully Deleted!'));
                    }
                    return $resultRedirect->setPath('lofmpauction/product/index');
                } else {
                    $this->messageManager->addError(__('No record Found!'));
                    return $resultRedirect->setPath('lofmpauction/product/index');
                }
            } else {
                $this->messageManager->addSuccess(__('Please try again!'));
                return $resultRedirect->setPath('lofmpauction/product/index');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('lofmpauction/product/index');
        }
    }
}
