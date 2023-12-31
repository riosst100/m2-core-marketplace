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

namespace Lof\MarketPlace\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerCollection;

    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupCollection;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param ManagerInterface $eventManager
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection
     * @param \Lof\MarketPlace\Model\Group $groupCollection
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Lof\MarketPlace\Model\Group $groupCollection,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_sellerHelper = $sellerHelper;
        $this->_sellerCollection = $sellerCollection;
        $this->_groupCollection = $groupCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @param RequestInterface $request
     */
    public function match(RequestInterface $request)
    {
        $_sellerHelper = $this->_sellerHelper;

        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'lof_marketplace_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Redirect::class,
                    ['request' => $request]
                );
            }

            if (!$condition->getContinue()) {
                return null;
            }

            $routeListSeller = $_sellerHelper->getConfig('general_settings/route');

            if ($routeListSeller != '' && $urlKey == $routeListSeller) {
                $request->setModuleName('lofmarketplace')
                    ->setControllerName('index')
                    ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Forward::class,
                    ['request' => $request]
                );
            }

            $route2 = $_sellerHelper->getConfig('general_settings/change_route');
            $routeLogin = $route2 . '/seller/login';
            $routeCreate = $route2 . '/seller/create';

            if ($route2 != ' ' && ($urlKey == $routeLogin)) {
                $request->setModuleName('lofmarketplace')
                    ->setControllerName('seller')
                    ->setActionName('login');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Forward::class,
                    ['request' => $request]
                );
            }

            if ($route2 != ' ' && ($urlKey == $routeCreate)) {
                $request->setModuleName('lofmarketplace')
                    ->setControllerName('seller')
                    ->setActionName('create');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Forward::class,
                    ['request' => $request]
                );
            }

            $url_prefix = $_sellerHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_sellerHelper->getConfig('general_settings/url_suffix');

            $identifiers = explode('/', $urlKey);
            // phpcs:disable Magento2.PHP.ReturnValueCheck.ImproperValueTesting
            $checkSuffix = true;
            if ($url_suffix) {
                $checkSuffix = count($identifiers) == 2 && $identifiers[0] == $url_prefix && strpos($identifiers[1], $url_suffix) !== false ? true : false;
            }

            if ((count($identifiers) == 2 && $identifiers[0] == $url_prefix && $checkSuffix)
                || (trim($url_prefix) == '' && count($identifiers) == 1)
            ) {
                $sellerUrl = '';
                if (trim($url_prefix) == '' && count($identifiers) == 1) {
                    $sellerUrl = $url_suffix ? str_replace($url_suffix, '', $identifiers[0]) : $identifiers[0];
                }
                if (count($identifiers) == 2) {
                    $sellerUrl = $url_suffix ? str_replace($url_suffix, '', $identifiers[1]) : $identifiers[1];
                }
                $group = $this->_groupCollection->getCollection()
                    ->addFieldToFilter('status', ['eq' => 1])
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->getFirstItem();

                if ($group && $group->getId()) {
                    $request->setModuleName('lofmarketplace')
                        ->setControllerName('group')
                        ->setActionName('view')
                        ->setParam('group_id', $group->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        \Magento\Framework\App\Action\Forward::class,
                        ['request' => $request]
                    );
                }
            }

            // Check Seller Url Key
            if ((count($identifiers) == 2
                    && $identifiers[0] == $url_prefix
                    && $checkSuffix)
                || (trim($url_prefix) == '' && count($identifiers) == 1)
            ) {
                $sellerUrl = '';
                if (count($identifiers) == 2) {
                    $sellerUrl = $url_suffix ? str_replace($url_suffix, '', $identifiers[1]) : $identifiers[1];
                }

                if (trim($url_prefix) == '' && count($identifiers) == 1) {
                    $sellerUrl = $url_suffix ? str_replace($url_suffix, '', $identifiers[0]) : $identifiers[0];
                }

                $seller = $this->_sellerCollection->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->getFirstItem();
                $seller_stores = [];

                if ($seller) {
                    $seller_stores = $seller->getStore();
                }

                if ($seller
                    && $seller->getId()
                    && is_array($seller_stores)
                    && (
                        in_array($this->storeManager->getStore()->getId(), $seller_stores)
                        || in_array(0, $seller_stores)
                    )
                ) {
                    $request->setModuleName('lofmarketplace')
                        ->setControllerName('seller')
                        ->setActionName('view')
                        ->setParam('seller_id', $seller->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        \Magento\Framework\App\Action\Forward::class,
                        ['request' => $request]
                    );
                }
            }
        }
    }
}
