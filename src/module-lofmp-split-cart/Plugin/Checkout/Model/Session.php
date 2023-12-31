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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Plugin\Checkout\Model;

class Session
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Lofmp\SplitCart\Api\QuoteRepositoryInterface
     */
    protected $splitQuoteRepository;

    /**
     * @var \Lofmp\SplitCart\Helper\ConfigData
     */
    protected $moduleConfig;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Lofmp\SplitCart\Api\QuoteRepositoryInterface $splitQuoteRepository
     * @param \Lofmp\SplitCart\Helper\ConfigData $configData
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Lofmp\SplitCart\Api\QuoteRepositoryInterface $splitQuoteRepository,
        \Lofmp\SplitCart\Helper\ConfigData $configData,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->_request = $request;
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->moduleConfig = $configData;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param \Magento\Checkout\Model\Session $subject
     * @param \Magento\Quote\Model\Quote $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetQuote(\Magento\Checkout\Model\Session $subject, $result)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return $result;
        }

        $route = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();

        if ($route == 'checkout' && $controller != 'cart') {
            try {
                if ($result && $result->hasItems()) {
                    $splitQuote = $this->splitQuoteRepository->getSplitCart($result->getId());
                    $quote = $this->quoteFactory->create()->load($splitQuote->getQuoteId());
                    return $quote;
                }
            } catch (\Exception $e) {
                return $result;
            }
        }

        return $result;
    }
}
