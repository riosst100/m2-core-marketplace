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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;
use Lofmp\SellerMembership\Helper\Data;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param ManagerInterface $eventManager
     * @param Data $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        Data $dataHelper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->_coreRegistry = $registry;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_dataHelper = $this->_dataHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            //$origUrlKey = $urlKey;

            /**
             * @var DataObject $condition
             */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'lofmp_sellermembership_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_dataHelper->getConfig('buy_membership_page/route');
            if ($route) {
                if ($urlKey == $route) {
                    $request->setModuleName('lofmpmembership')
                            ->setControllerName('buy')
                            ->setActionName('index');
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);

                    $this->dispatched = true;

                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                    );
                }
            }
        }
    }
}
