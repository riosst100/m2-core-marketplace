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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Controller\Marketplace;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class AbstractAction.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPlace::index';

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    protected $sellerContext;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * AbstractAction constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->logger = $logger;
        $this->sellerContext = $sellerContext;
    }

    /**
     * Authenticate customer.
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->sellerContext->isModuleActive()) {
            return $this->_redirect('noroute');
        }

        if (!$this->sellerContext->getCustomerSession()->isLoggedIn()) {
            $this->_actionFlag->set('', 'no-dispatch', true);

            return $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        } elseif (!$this->sellerContext->isSellerActive()) {
            return $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        }

        if ($request->getFullActionName() === 'permissions_index_index'
            || $request->getFullActionName() === 'permissions_role_index'
            || $request->getFullActionName() === 'permissions_users_index') {
            $this->sellerContext->initSellerAdmin();
        }

        if (!$this->isAllowed()) {
            $this->_actionFlag->set('', 'no-dispatch', true);

            if ($this->sellerContext->isCurrentUserSellerUser()) {
                return $this->_redirect('permissions/accessdenied');
            }

            return $this->_redirect('noroute');
        }

        return parent::dispatch($request);
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
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->sellerContext->getCustomerSession()->setIsUrlNotice($this->_actionFlag->get(
            '',
            self::FLAG_IS_URLS_CHECKED
        ));
        return $this->getResponse();
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->sellerContext->isResourceAllowed(static::SELLER_RESOURCE);
    }

    /**
     * Returns JSON error.
     *
     * @param string $message
     * @param array $payload
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \InvalidArgumentException
     */
    protected function jsonError($message, array $payload = [])
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData(
            [
                'status' => 'error',
                'message' => $message,
                'payload' => $payload
            ]
        );

        return $resultJson;
    }

    /**
     * Returns JSON success.
     *
     * @param array $payload
     * @param string $message
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \InvalidArgumentException
     */
    protected function jsonSuccess(array $payload, $message = '')
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData(
            [
                'status' => 'ok',
                'message' => $message,
                'data' => $payload
            ]
        );

        return $resultJson;
    }

    /**
     * Handle error message.
     *
     * @param string|null $errorMessage
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function handleJsonError($errorMessage = null)
    {
        $errorMessage = $errorMessage ?: __('Something went wrong.');
        $this->messageManager->addErrorMessage($errorMessage);

        return $this->jsonError($errorMessage);
    }

    /**
     * Handle success message.
     *
     * @param string $successMessage
     * @param array $payload
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function handleJsonSuccess(string $successMessage, array $payload = [])
    {
        $this->messageManager->addSuccessMessage($successMessage);

        return $this->jsonSuccess($payload, $successMessage);
    }
}
