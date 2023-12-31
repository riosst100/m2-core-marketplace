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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class for deleting a team entity.
 */
class Delete
{
    /**
     * @var \Lof\MarketPermissions\Model\StructureRepository
     */
    private $structureRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Team
     */
    protected $teamResource;

    /**
     * @param \Lof\MarketPermissions\Model\ResourceModel\Team $teamResource
     * @param \Lof\MarketPermissions\Model\StructureRepository $structureRepository
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourceModel\Team $teamResource,
        \Lof\MarketPermissions\Model\StructureRepository $structureRepository,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager
    ) {
        $this->teamResource = $teamResource;
        $this->structureRepository = $structureRepository;
        $this->structureManager = $structureManager;
    }

    /**
     * Deletes a team.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @return void
     * @throws LocalizedException
     */
    public function delete(\Lof\MarketPermissions\Api\Data\TeamInterface $team)
    {
        $structure = $this->structureManager->getStructureByTeamId($team->getId());
        if ($structure) {
            $structureNode = $this->structureManager->getTreeById($structure->getId());
            if ($structureNode && $structureNode->hasChildren()) {
                throw new LocalizedException(
                    __(
                        'This team has child users or teams aligned to it and cannot be deleted.'
                        . ' Please re-align the child users or teams first.'
                    )
                );
            }
            $this->structureRepository->deleteById($structure->getId());
        }
        $this->teamResource->delete($team);
    }
}
