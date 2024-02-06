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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param RegionFactory $regionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        RegionFactory $regionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->regionFactory = $regionFactory;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
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

    public function setStoreBySellerCountry($sellerCountryID = null) 
    {
        $selectedWebsiteCode = $sellerCountryID ?? 'base';

        $storeId = null;
        $websiteId = $this->getWebsiteIdByCode($selectedWebsiteCode);
        if ($websiteId) {
            $storeId = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        }
        
        if ($storeId) {
            $this->storeManager->setCurrentStore($storeId);
        }

        return true;
    }

    public function getWebsiteIdByCode($code)
    {
        $websiteId = null;

        try {
            $website = $this->websiteRepository->get($code);
            $websiteId = (int)$website->getId();
        } catch (\Exception $exception) {}

        return $websiteId;
    }
}