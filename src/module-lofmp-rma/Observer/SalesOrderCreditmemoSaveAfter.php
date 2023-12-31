<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoSaveAfter implements ObserverInterface
{
    /**
     * SalesOrderCreditmemoSaveAfter constructor.
     *
     * @param \Lofmp\Rma\Model\RmaFactory $rmaFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lofmp\Rma\Model\ItemFactory $itemFactory
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Lofmp\Rma\Model\RmaFactory $rmaFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\Rma\Model\ItemFactory $itemFactory,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->calculate = $calculate;
        $this->helper = $helper;
        $this->itemFactory = $itemFactory;
        $this->rmaFactory = $rmaFactory;
        $this->_resource = $resource;
        $this->_request = $request;
        $this->backendSession = $backendSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getDataObject();
        $session = $this->backendSession;
        $data = $this->_request->getPost();
        $id = $creditmemo->getId();
        $rmaId = $session->getRmaId();
        if (!$rmaId && isset($data['rma_id'])) {
            $rmaId = (int)$data['rma_id'];
        }
        if ($rmaId) {
            $objArray = [
                'rc_rma_id' => $rmaId,
                'rc_credit_memo_id' => $id,
            ];
            $this->_resource->getConnection()->insert(
                $this->_resource->getTableName('lofmp_rma_rma_creditmemo'),
                $objArray
            );

            //Updated seller commission
            foreach ($creditmemo->getItems() as $item) {
                $productId = $item->getProductId();
                if ($item->getData('qty') <= 0) {
                    continue;
                }

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
                $sellerId = $product->getSellerId();
                $commission = $this->helper->getCommission($sellerId, $item->getProductId());

                $priceCommission = $this->calculate->calculate($commission, $item);
                $admin_commission = $item->getData('row_total')
                    + $item->getData('tax_amount')
                    - $item->getData('discount_amount')
                    - $priceCommission;
                $sellerItem = $this->itemFactory->create()->getCollection()
                    ->addFieldToFilter('rma_id', $rmaId)
                    ->addFieldToFilter('product_id', $productId)
                    ->getFirstItem();

                if ($sellerItem && $sellerItem->getItemId()) {
                    $itemData = $sellerItem->getData();
                    $itemFactory = $this->itemFactory->create()->load($itemData['item_id']);
                    $sellerCommission = $sellerItem->getData('seller_commission') + $priceCommission;
                    $adminCommission = $sellerItem->getData('admin_commission') + $admin_commission;
                    $qtyReturned = $item->getData('qty') + $sellerItem->getData('qty_returned');
                    $itemFactory->setSellerCommission($sellerCommission);
                    $itemFactory->setAdminCommission($adminCommission);
                    $itemFactory->setQtyReturned($qtyReturned);
                    $itemFactory->save();
                }
            }
        }
    }
}
