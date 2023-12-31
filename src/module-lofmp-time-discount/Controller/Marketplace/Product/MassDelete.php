<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Controller\Marketplace\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Lofmp\TimeDiscount\Model\ResourceModel\Product\CollectionFactory;
use Lof\MarketPlace\Helper\Data;

class MassDelete extends \Lofmp\TimeDiscount\Controller\Marketplace\Product
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
     * @param Context
     * @param Filter
     * @param CollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        Data $helper
    ) {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
        $i=0;
        foreach ($collection as $item) {
            if ($item->getSellerId() == $this->helper->getSellerId()) {
                $i++;
                $item->delete();
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $i));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
