<?php
/**
 * Lofmp
 *
 * This source file is subject to the Lofmp Software License, which is available at https://lof.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Lofmp
 * @package   lof/module-rma
 * @version   1.1.21
 * @copyright Copyright (C) 2017 Lofmp (https://lof.com/)
 */

namespace Lofmp\Rma\Controller\Marketplace\Rma;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * View constructor.
     *
     * @param \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Customer\Model\Session $sellerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\Session $sellerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->registry = $registry;
        $this->rmaRepository = $rmaRepository;
        $this->_session = $sellerSession;
        $this->_sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;

        parent::__construct($context);
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $rma = $this->rmaRepository->getById($this->getRequest()->getParam('id'));
        $this->registry->register('current_rma', $rma);
        $sellerId = $this->_session->getId();
        $status = $this->_sellerFactory->create()->load($sellerId, 'customer_id')->getStatus();

        if ($this->_session->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($this->_session->isLoggedIn() && $status == 0) {
            $this->_redirect($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirect($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
