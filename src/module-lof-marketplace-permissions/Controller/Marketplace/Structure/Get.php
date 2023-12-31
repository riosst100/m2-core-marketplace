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

namespace Lof\MarketPermissions\Controller\Marketplace\Structure;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Get extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_view';

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Get constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Get tree
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        /** @var \Lof\MarketPermissions\Block\Marketplace\Management $block */
        $block = $this->layoutFactory->create()->createBlock(\Lof\MarketPermissions\Block\Marketplace\Management::class);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'status' => 'ok',
                'message' => __('The tree retrieved successfully.'),
                'data' => $block->getTree()
            ]
        );
        return $resultJson;
    }
}
