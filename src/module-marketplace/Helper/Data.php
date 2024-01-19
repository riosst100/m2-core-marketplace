<?php

namespace CoreMarketplace\MarketPlace\Helper;

use Magento\Directory\Model\RegionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        RegionFactory $regionFactory
    ) {
        $this->regionFactory = $regionFactory;
        parent::__construct($context);
    }

    public function getRegionData($regionCode)
    {
        $regionColl = $this->regionFactory->create()
        ->getCollection()
        ->addFieldToFilter('code', $regionCode);
        if ($regionColl->getSize()) {
            return $regionColl->getFirstItem();
        }

        return null;
    }
}