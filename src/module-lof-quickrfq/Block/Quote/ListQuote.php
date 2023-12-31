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
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlInterface;

/**
 * Class ListQuote
 * @package Lof\Quickrfq\Block\Quote
 */
class ListQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        UrlInterface $url,
        CustomerFactory $customerFactory,
        QuoteCollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customer = $customerFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
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
     * @return string
     */
    public function getHrefUrl()
    {
        return $this->url->getUrl('quickrfq/frontend');
    }

    /**
     * @return mixed
     */
    public function getListQuoteCustomer()
    {
        $page     = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $customerId = $this->customerSession->getId();
        return $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerId)
            ->setOrder('create_date', 'DESC')
            ->setPageSize($pageSize)
            ->setCurPage($page);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $quoteId
     * @return string
     */
    public function getDetailLink($quoteId)
    {
        return $this->url->getUrl('*/*/view', [ 'quickrfq_id' => $quoteId ]);
    }



    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $collection = $this->getListQuoteCustomer();
        if ($collection) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'quick.rfq.history.pager'
            )->setCollection(
                $collection
            );
            $this->setChild('pager', $pager);
            $collection->load();
        }
        return $this;
    }
}
