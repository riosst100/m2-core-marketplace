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
 * Controller for managing teams from the storefront.
 */
class Manage extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /** @var \Lof\MarketPermissions\Api\TeamRepositoryInterface */
    private $teamRepository;

    /** @var \Lof\MarketPermissions\Api\Data\TeamInterfaceFactory */
    private $teamFactory;

    /** @var \Magento\Framework\Api\DataObjectHelper */
    private $objectHelper;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Manage constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     * @param \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository
     * @param \Lof\MarketPermissions\Api\Data\TeamInterfaceFactory $teamFactory
     * @param \Magento\Framework\Api\DataObjectHelper $objectHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager,
        \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository,
        \Lof\MarketPermissions\Api\Data\TeamInterfaceFactory $teamFactory,
        \Magento\Framework\Api\DataObjectHelper $objectHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->structureManager = $structureManager;
        $this->teamRepository = $teamRepository;
        $this->teamFactory = $teamFactory;
        $this->objectHelper = $objectHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Add/Edit team action.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $request = $this->getRequest();

        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());

        $teamId = $request->getParam('team_id');

        if ((int)$teamId) {
            return $this->edit($allowedIds, $teamId);
        } else {
            return $this->create($allowedIds);
        }
    }

    /**
     * Edit team.
     *
     * @param array $allowedIds
     * @param int $teamId
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function edit(array $allowedIds, $teamId)
    {
        $request = $this->getRequest();

        if (!in_array($teamId, $allowedIds['teams'])) {
            return $this->jsonError(__('You are not allowed to do this.'));
        }
        try {
            $team = $this->teamFactory->create();
            $this->objectHelper->populateWithArray(
                $team,
                $request->getParams(),
                \Lof\MarketPermissions\Api\Data\TeamInterface::class
            );
            $team->setId($teamId);
            $this->teamRepository->save($team);
            $team = $this->teamRepository->get($teamId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonError(__('Something went wrong.'));
        }
        $message = __('The team was successfully updated.');
        return $this->jsonSuccess($team->getData(), $message);
    }

    /**
     * Create team.
     *
     * @param array $allowedIds
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function create(array $allowedIds)
    {
        $request = $this->getRequest();
        $targetId = $request->getParam('target_id');
        if ($targetId && !in_array($targetId, $allowedIds['structures'])) {
            return $this->jsonError(__('You are not allowed to do this.'));
        }
        try {
            $team = $this->teamFactory->create();
            $this->objectHelper->populateWithArray(
                $team,
                $request->getParams(),
                \Lof\MarketPermissions\Api\Data\TeamInterface::class
            );
            $customer = $this->customerRepository->getById($this->sellerContext->getCustomerId());
            $sellerId = $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId();
            $this->teamRepository->create($team, $sellerId);
            if ($targetId) {
                $teamStructure = $this->structureManager->getStructureByTeamId($team->getId());
                $this->structureManager->moveNode($teamStructure->getId(), $targetId);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonError(__('Something went wrong.'));
        }
        $message = __('The team was successfully created.');
        return $this->jsonSuccess($team->getData(), $message);
    }
}
