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

namespace Lof\MarketPlace\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassRejectAbstract extends \Magento\Backend\App\Action
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
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productRepository;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param \Magento\Catalog\Model\Product $productRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param CollectionFactory $collectionFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Sender $sender
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Catalog\Model\Product $productRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Helper\Image $imageHelper,
        CollectionFactory $collectionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Sender $sender
    ) {
        $this->sender = $sender;
        $this->helper = $helper;
        $this->filter = $filter;
        $this->_productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->imageHelper = $imageHelper;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $item) {
            $product = $this->_productRepository->load($item->getData('product_id'));
            $product->setApproval(3)
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
                ->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $this->_productRepository->save($product);
            $sellerProductModel = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class)
                ->load($item->getEntityId());
            $sellerProductModel->setStatus(3)
                ->setId($item->getEntityId())
                ->save();

            $seller = $this->_objectManager->create(\Lof\MarketPlace\Model\Seller::class)
                ->load($item->getData('seller_id'), 'seller_id');
            $data = $item->getData();
            $approvedProduct = $this->productRepositoryInterface->getById($data['product_id'], false);
            $data['email'] = $seller->getEmail();
            $data['url'] = $seller->getUrl();
            $data['seller_name'] = $seller->getName();
            $data['name'] = $product->getName();
            $data['frontend_request_url'] = $approvedProduct->getUrlModel()->getUrlInStore($approvedProduct, ['_escape' => true]);
            $data['image'] = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
            if ($this->helper->getConfig('email_settings/enable_send_email')) {
                $this->sender->unapproveProduct($data);
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been rejected.', $collection->getSize())
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }


}
