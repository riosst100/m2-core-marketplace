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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SellerBadge\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * Module Config Path
     */
    const XML_PATH_MODULE_ENABLE = 'lofmpsellerbadge/general/enable';

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param IndexerFactory $indexerFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        IndexerFactory $indexerFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * @param $key
     * @param null $store
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        return $this->scopeConfig->getValue(
            $key,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $path
     * @param null $id
     * @return bool
     */
    public function hasFlagConfig($path, $id = null): bool
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return $this->hasFlagConfig(self::XML_PATH_MODULE_ENABLE, $storeId);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @throws \Throwable
     */
    public function reindexSellerBadge()
    {
        $indexerIds = [
            'lofmp_sellerbadge_manager',
        ];

        foreach ($indexerIds as $indexerId) {
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    }
}
