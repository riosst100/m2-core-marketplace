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

namespace Lof\MarketPermissions\Plugin\Framework\App\Action;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Url;

class AbstractActionPlugin
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var CustomerLoginChecker
     */
    private $customerLoginChecker;

    /**
     * @var Url
     */
    private $_frontendUrl;

    /**
     * AbstractActionPlugin constructor.
     *
     * @param Url $frontendUrl
     * @param CustomerLoginChecker $customerLoginChecker
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Url $frontendUrl,
        CustomerLoginChecker $customerLoginChecker,
        ResultFactory $resultFactory
    ) {
        $this->_frontendUrl = $frontendUrl;
        $this->resultFactory = $resultFactory;
        $this->customerLoginChecker = $customerLoginChecker;
    }

    /**
     * Around dispatch plugin.
     *
     * @param AbstractAction $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(AbstractAction $subject, \Closure $proceed, RequestInterface $request)
    {
        if ($request->isPost() && $this->customerLoginChecker->isLoginAllowed()) {
            return $this->getLogoutResult($subject, $request);
        }

        return $proceed($request);
    }

    /**
     * Get logout result.
     *
     * @param AbstractAction $subject
     * @param RequestInterface $request
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \InvalidArgumentException
     */
    private function getLogoutResult(AbstractAction $subject, RequestInterface $request)
    {
        $result = $this->_redirectUrl($subject, $this->_frontendUrl->getUrl('customer/account/logout'));
        if ($request->isAjax()) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                'backUrl' => $this->_frontendUrl->getUrl('customer/account/logout')
            ]);
        }
        return $result;
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($subject, $url)
    {
        $subject->getResponse()->setRedirect($url);
        return $subject->getResponse();
    }
}
