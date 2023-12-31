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

namespace Lof\MarketPermissions\Model\Team;

use Lof\MarketPermissions\Api\Data\StructureInterface;

/**
 * Class for creating a team entity.
 */
class Create
{
    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Team
     */
    private $teamResource;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $SellerRepository;

    /**
     * @param \Lof\MarketPermissions\Model\ResourceModel\Team $teamResource
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     * @param \Lof\MarketPermissions\Api\SellerRepositoryInterface $SellerRepository
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourceModel\Team $teamResource,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager,
        \Lof\MarketPermissions\Api\SellerRepositoryInterface $SellerRepository
    ) {
        $this->teamResource = $teamResource;
        $this->structureManager = $structureManager;
        $this->SellerRepository = $SellerRepository;
    }

    /**
     * Creates a team for a seller which id is specified. Validates that the team is new and was not saved before.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @param int $sellerId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create(\Lof\MarketPermissions\Api\Data\TeamInterface $team, $sellerId)
    {
        if ($team->getId()) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not create team'));
        }
        $seller = $this->SellerRepository->get($sellerId);
        $SellerTree = $this->structureManager->getTreeByCustomerId($seller->getCustomerId());
        $this->teamResource->save($team);
        $this->structureManager->addNode(
            $team->getId(),
            StructureInterface::TYPE_TEAM,
            $SellerTree->getId()
        );
    }
}
