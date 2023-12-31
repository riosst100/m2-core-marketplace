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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lof\Auction\Model;

use Lof\Auction\Api\Data\MaxAbsenteeBidInterfaceFactory;
use Lof\Auction\Api\Data\MaxAbsenteeBidInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class MaxAbsenteeBid
 * @package Lof\Auction\Model
 */
class MaxAbsenteeBid extends AbstractModel
{
    protected $_eventPrefix = 'lof_auction_absentee_bid';

    protected $apiDataFactory;

    protected $dataObjectHelper;

    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * @param MaxAbsenteeBidInterfaceFactory $apiDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Lof\Auction\Model\ResourceModel\MaxAbsenteeBid $resource
     * @param \Lof\Auction\Model\ResourceModel\MaxAbsenteeBid\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        MaxAbsenteeBidInterfaceFactory $apiDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Lof\Auction\Model\ResourceModel\MaxAbsenteeBid $resource,
        \Lof\Auction\Model\ResourceModel\MaxAbsenteeBid\Collection $resourceCollection,
        array $data = []
    ) {
        $this->apiDataFactory = $apiDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Lof\Auction\Model\ResourceModel\MaxAbsenteeBid');
    }

    /**
     * Retrieve MaxAbsenteeBid model with MaxAbsenteeBid data
     * @return MaxAbsenteeBidInterface
     */
    public function getDataModel()
    {
        $modelData = $this->getData();
        
        $modelDataObject = $this->apiDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $modelDataObject,
            $modelData,
            MaxAbsenteeBidInterface::class
        );
        
        return $modelDataObject;
    }
}
