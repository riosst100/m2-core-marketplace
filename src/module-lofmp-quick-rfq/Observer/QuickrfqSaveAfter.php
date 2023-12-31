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
 * @package    Lofmp_Quickrfq
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Quickrfq\Observer;

use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\Quickrfq\Helper\Data;
use Lof\Quickrfq\Helper\Data as QuickrfqData;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuickrfqSaveAfter
 * @package Lofmp\Quickrfq\Observer
 */
class QuickrfqSaveAfter implements ObserverInterface
{
    /**
     * @var Data
     */
    private $_helper;
    /**
     * @var SellerFactory
     */
    private $sellerFactory;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var QuickrfqData
     */
    private $quickrfqHelper;

    /**
     * RestoreQuote constructor.
     * @param ProductFactory $productFactory
     * @param SellerFactory $sellerFactory
     * @param QuickrfqData $quickrfqHelper
     * @param Data $helper
     */
    public function __construct(
        ProductFactory $productFactory,
        SellerFactory $sellerFactory,
        QuickrfqData $quickrfqHelper,
        Data $helper
    ) {
        $this->_helper = $helper;
        $this->sellerFactory = $sellerFactory;
        $this->productFactory = $productFactory;
        $this->quickrfqHelper = $quickrfqHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getConfig('general/enabled')) {
            $data = $observer->getData()['data'];
            $productId = $data['product_id'];
            $product = $this->productFactory->create()->load($productId);
            if ($product->getSellerId()) {
                $model = $observer->getModel();
                $seller = $this->sellerFactory->create()->load($product->getSellerId());
                $emailSeller = $seller->getEmail();
                $sellerName = $seller->getName();
                $model->setSellerId($product->getSellerId())
                    ->setSellerName($sellerName)
                    ->save();

                $templateAdmin = $observer->getTemplateAdmin();
                $templateCustomer = $observer->getTemplateCustomer();

                //send email to customer
                $data['is_admin']       = false;
                $data['product_name']   = $model->getProductName();
                $data['receiver_name']  = $sellerName;
                $this->quickrfqHelper->sendEmail($data, $data['customer_email'], $templateCustomer);

                //send email to seller
                $data['is_admin']       = true;
                $data['receiver_name']  = $model->getContactName();
                $this->quickrfqHelper->sendEmail($data, $emailSeller, $templateAdmin);

            }
        }
    }
}
