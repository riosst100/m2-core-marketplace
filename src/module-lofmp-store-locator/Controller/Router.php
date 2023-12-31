<?php
/**
 * LandOfCoder
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
 * @category   LandOfCoder
 * @package    Lof_StoreLocator
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;


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
     * @var \Lof\StoreLocator\Model\StoreLocator
     */
    protected $_storeLocator;

    /**
     * @param ActionFactory
     * @param ResponseInterface
     * @param ManagerInterface
     * @param StoreManagerInterface
     * @param \Lof\StoreLocator\Helper\Data
     * @param \Lof\StoreLocator\Model\StoreLocator
     * @param \Magento\User\Model\UserFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Registry $registry,
        \Lofmp\StoreLocator\Helper\Data $dataHelper,
        \Lofmp\StoreLocator\Model\StoreLocator $storeLocator
        ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager  = $eventManager;
        $this->response      = $response;
        $this->storeManager  = $storeManager;
        $this->_dataHelper   = $dataHelper;
        $this->_storeLocator = $storeLocator;
        $this->_coreRegistry = $registry;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_dataHelper = $this->_dataHelper;
        $store = $this->storeManager->getStore();

        if (!$this->dispatched) {

            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'lof_storelocator_controller_router_match_before',
                [
                'router' => $this,
                'condition' => $condition
                ]
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

            $urlKeys      = explode("/", $urlKey);
            $urlKeysOrgin = $urlKeys;
            $enable       = $_dataHelper->getConfig('general/enable');
            $route        = $_dataHelper->getConfig('general/route');

           
            // STORELOCATOR PAGE
            if ($enable && (count($urlKeys) == 1) && ($route==$urlKeys[0])) {
               
              
                $request->setModuleName('storelocator')
                ->setControllerName('index')
                ->setActionName('index');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }

            // DETAILS PAGE
            if ($enable && count($urlKeys) == 2 && $route == $urlKeys[0]) {
                 
                $alias = str_replace("-", " ", $urlKeys[1]);
               
                if($this->_dataHelper->getId($alias)){
                   $storeLocatorId = $this->_dataHelper->getId($alias);
                     $request->setModuleName('storelocator')
                    ->setControllerName('index')
                    ->setActionName('details')
                    ->setParam('id',$storeLocatorId);
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                   
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