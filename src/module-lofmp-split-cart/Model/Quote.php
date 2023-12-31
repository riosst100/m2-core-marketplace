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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Model;

use Lofmp\SplitCart\Api\Data\QuoteInterface;
use Lofmp\SplitCart\Api\Data\QuoteInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Quote extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var QuoteInterfaceFactory
     */
    protected $quoteDataFactory;

    /**
     * @var string
     */
    protected $_eventPrefix = 'lofmp_splitcart_quote';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param QuoteInterfaceFactory $quoteDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Lofmp\SplitCart\Model\ResourceModel\Quote $resource
     * @param \Lofmp\SplitCart\Model\ResourceModel\Quote\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        QuoteInterfaceFactory $quoteDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Lofmp\SplitCart\Model\ResourceModel\Quote $resource,
        \Lofmp\SplitCart\Model\ResourceModel\Quote\Collection $resourceCollection,
        array $data = []
    ) {
        $this->quoteDataFactory = $quoteDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve quote model with quote data
     * @return QuoteInterface
     */
    public function getDataModel()
    {
        $quoteData = $this->getData();

        $quoteDataObject = $this->quoteDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteDataObject,
            $quoteData,
            QuoteInterface::class
        );

        return $quoteDataObject;
    }
}
