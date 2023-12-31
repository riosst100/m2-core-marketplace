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

namespace Lof\MarketPermissions\Controller\Marketplace\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class Index.
 */
class Index extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_view';

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    protected $sellerContext;

    /**
     * @var \Lof\MarketPermissions\Api\SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->sellerContext = $sellerContext;
        $this->sellerManagement = $sellerManagement;
    }

    /**
     * Seller dashboard.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function execute()
    {
        if ($this->sellerContext->getCustomerSession()->isLoggedIn()) {
            $this->_view->loadLayout();
            $this->_view->loadLayoutUpdates();
            $this->_view->renderLayout();
        }
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        $seller = $this->sellerManagement->getByCustomerId($this->sellerContext->getCustomerId());
        return !$seller || parent::isAllowed();
    }
}
