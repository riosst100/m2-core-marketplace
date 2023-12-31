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

namespace Lof\MarketPermissions\Controller\Marketplace\Team;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Controller for deleting a team.
 */
class Delete extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /**
     * @var \Lof\MarketPermissions\Api\TeamRepositoryInterface
     */
    private $teamRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * Delete constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->teamRepository = $teamRepository;
        $this->structureManager = $structureManager;
    }

    /**
     * Delete team action.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());
        $teamId = $this->getRequest()->getParam('team_id');
        if (!in_array($teamId, $allowedIds['teams'])) {
            return $this->jsonError(__('You are not allowed to do this.'));
        }

        try {
            $this->teamRepository->deleteById($teamId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonError(__('Something went wrong.'));
        }

        return $this->jsonSuccess([], __('The team was successfully deleted.'));
    }
}
