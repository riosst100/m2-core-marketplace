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

namespace Lof\MarketPermissions\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * A repository for managing team entity.
 */
class TeamRepository implements \Lof\MarketPermissions\Api\TeamRepositoryInterface
{
    /**
     * @var \Lof\MarketPermissions\Api\Data\TeamInterface[]
     */
    private $instances = [];

    /**
     * @var \Lof\MarketPermissions\Model\TeamFactory
     */
    private $teamFactory;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Team
     */
    private $teamResource;

    /**
     * @var \Lof\MarketPermissions\Model\Team\Delete
     */
    private $teamDeleter;

    /**
     * @var \Lof\MarketPermissions\Model\Team\Create
     */
    private $teamCreator;

    /**
     * @var \Lof\MarketPermissions\Model\Team\GetList
     */
    private $getLister;

    /**
     * @param TeamFactory $teamFactory
     * @param ResourceModel\Team $teamResource
     * @param \Lof\MarketPermissions\Model\Team\Delete $teamDeleter
     * @param \Lof\MarketPermissions\Model\Team\Create $teamCreator
     * @param \Lof\MarketPermissions\Model\Team\GetList $getLister
     */
    public function __construct(
        \Lof\MarketPermissions\Model\TeamFactory $teamFactory,
        \Lof\MarketPermissions\Model\ResourceModel\Team $teamResource,
        \Lof\MarketPermissions\Model\Team\Delete $teamDeleter,
        \Lof\MarketPermissions\Model\Team\Create $teamCreator,
        \Lof\MarketPermissions\Model\Team\GetList $getLister
    ) {
        $this->teamFactory = $teamFactory;
        $this->teamResource = $teamResource;
        $this->teamDeleter = $teamDeleter;
        $this->teamCreator = $teamCreator;
        $this->getLister = $getLister;
    }

    /**
     * @inheritdoc
     */
    public function create(\Lof\MarketPermissions\Api\Data\TeamInterface $team, $sellerId)
    {
        $this->checkRequiredFields($team);
        try {
            $this->teamCreator->create($team, $sellerId);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not create team'),
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function save(\Lof\MarketPermissions\Api\Data\TeamInterface $team)
    {
        $this->checkRequiredFields($team);
        if (!$team->getId()) {
            throw new LocalizedException(__(
                '"%fieldName" is required. Enter and try again.',
                ['fieldName' => 'id']
            ));
        } else {
            $this->get($team->getId());
        }
        try {
            $this->teamResource->save($team);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not update team'),
                $e
            );
        }
        unset($this->instances[$team->getId()]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($teamId)
    {
        if (!isset($this->instances[$teamId])) {
            /** @var Team $team */
            $team = $this->teamFactory->create();
            $team->load($teamId);
            if (!$team->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __(
                        'No such entity with %fieldName = %fieldValue',
                        [
                            'fieldName' => 'id',
                            'fieldValue' => $teamId
                        ]
                    )
                );
            }
            $this->instances[$teamId] = $team;
        }
        return $this->instances[$teamId];
    }

    /**
     * @inheritdoc
     */
    public function delete(\Lof\MarketPermissions\Api\Data\TeamInterface $team)
    {
        try {
            $this->teamDeleter->delete($team);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    'Cannot delete team with id %1',
                    $team->getId()
                ),
                $e
            );
        }
        unset($this->instances[$team->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function deleteById($teamId)
    {
        $team = $this->get($teamId);
        $this->delete($team);
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        return $this->getLister->getList($criteria);
    }

    /**
     * Checks if entity has all the required fields.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @return void
     * @throws LocalizedException
     */
    private function checkRequiredFields(\Lof\MarketPermissions\Api\Data\TeamInterface $team)
    {
        if (!$team->getName()) {
            throw new LocalizedException(__(
                '"%fieldName" is required. Enter and try again.',
                ['fieldName' => 'name']
            ));
        }
    }
}
