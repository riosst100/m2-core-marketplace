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

namespace Lof\MarketPlace\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Commission extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Commission constructor.
     * @param Context $context
     * @param Filter $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $collection = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class)->getCollection()
            ->addFieldToFilter('product_id', $data['product_id'])
            ->addFieldToFilter('seller_id', $data['seller_id']);

        if ($collection->getSize()) {
            $productIds = [$data['product_id']];
            $allStores = $this->_storeManager->getStores();
            $status = \Lof\MarketPlace\Model\SellerProduct::STATUS_DISABLED;

            $sellerSellerProduct = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class)
                ->getCollection();

            $coditionData = "`product_id`=" . $data['product_id'];

            $sellerSellerProduct->setSellerProductData(
                $coditionData,
                ['status' => $status]
            );

            foreach ($allStores as $storeId) {
                $this->_objectManager->get(
                    \Magento\Catalog\Model\Product\Action::class
                )->updateAttributes($productIds, ['status' => $status], $storeId);
            }

            $this->_objectManager->get(
                \Magento\Catalog\Model\Product\Action::class
            )->updateAttributes($productIds, ['status' => $status], 0);

            $catagoryModel = $this->_objectManager->get(\Magento\Catalog\Model\Category::class);

            $model = $this->_objectManager->get(
                \Magento\Catalog\Model\Product::class
            )->load($data['product_id']);

            $catarray = $model->getCategoryIds();
            $categoryname = '';
            foreach ($catarray as $keycat) {
                $categoriesy = $catagoryModel->load($keycat);
                if ($categoryname == '') {
                    $categoryname = $categoriesy->getName();
                } else {
                    $categoryname = $categoryname . ',' . $categoriesy->getName();
                }
            }

            $this->messageManager->addSuccessMessage(__('Product has been set commission.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::product');
    }
}
