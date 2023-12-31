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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Block\Quote;

use Lof\Quickrfq\Model\ResourceModel\Quickrfq\CollectionFactory as QuoteCollectionFactory;
use Lof\Quickrfq\Model\ResourceModel\Message\CollectionFactory as MessageCollection;
use Lof\Quickrfq\Model\Attachment;
use Lof\Quickrfq\Model\Quickrfq;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class View
 * @package Lof\Quickrfq\Block\Quote
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var QuoteCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var CustomerFactory
     */
    private $customer;
    /**
     * @var Data
     */
    private $_pricingHelper;
    /**
     * @var ProductRepository
     */
    private $product;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var Attachment
     */
    private $attachment;
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var MessageCollection
     */
    private $messageCollection;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Data $pricingHelper
     * @param UrlInterface $url
     * @param ProductRepository $productRepository
     * @param CustomerFactory $customerFactory
     * @param QuoteCollectionFactory $collectionFactory
     * @param MessageCollection $messageCollection
     * @param Attachment $attachment
     * @param Quickrfq $quickrfq
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Data $pricingHelper,
        UrlInterface $url,
        ProductRepository $productRepository,
        CustomerFactory $customerFactory,
        QuoteCollectionFactory $collectionFactory,
        MessageCollection $messageCollection,
        Attachment $attachment,
        Quickrfq $quickrfq,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customer = $customerFactory;
        $this->product = $productRepository;
        $this->_pricingHelper = $pricingHelper;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->attachment = $attachment;
        $this->quickrfq = $quickrfq;
        $this->messageCollection = $messageCollection;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $quote = $this->getCurrentQuote();
        $this->pageConfig->getTitle()->set(__('My Request Quote Detail - SKU: %1', $quote->getProductSku()));   
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageRender()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        return $mediaDirectory;
    }

    /**
     * @return Quickrfq
     */
    public function getCurrentQuote()
    {
        $quickrfqId = $this->getRequest()->getParam('quickrfq_id');
        return $this->quickrfq->load($quickrfqId);
    }

    /**
     * @param $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($productId)
    {
        try {
            $product = $this->product->getById($productId);

            return $product && !empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getProductPriceHtml($price)
    {
        return $this->_pricingHelper->currency($price, true, false);
    }

    /**
     * @param $attachmentId
     * @return string
     */
    public function getAttachmentUrl($attachmentId)
    {
        return $this->getUrl('*/*/download', ['attachmentId' => $attachmentId]);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getAttachFiles()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->attachment->getCollection()->addFieldToFilter('quickrfq_id', $quoteId);
    }

    /**
     * @return mixed
     */
    public function getMessageCollection()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->messageCollection->create()->addFieldToFilter('quickrfq_id', $quoteId)->setOrder('created_at');
    }

    /**
     * @return string
     */
    public function getSendMessageLink()
    {
        return $this->url->getUrl('*/*/send', ['quickrfq_id' => $this->getRequest()->getParam('quickrfq_id')]);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _toHtml()
    {
        $customerEmail = $this->customerSession->getId();
        $grid_pagination = true;
        $template = 'Lof_Quickrfq::quote/view.phtml';
        $this->setTemplate($template);
        $item_per_page = 8;
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('email', $customerEmail)->getSelect()->order('id DESC');
        if ($grid_pagination) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'my.custom.pager');
            $pager->setLimit($item_per_page)->setCollection($collection);
            $this->setChild('pager', $pager);
        }
        $this->setCollection($collection);
        return parent::_toHtml();
    }
}
