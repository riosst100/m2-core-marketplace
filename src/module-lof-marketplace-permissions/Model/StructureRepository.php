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

use Lof\MarketPermissions\Api\StructureRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Lof\MarketPermissions\Model\ResourceModel\Structure as ResourceStructure;

/**
 * Repository for basic structure entity CRUD operations.
 */
class StructureRepository
{
    /**
     * @var \Lof\MarketPermissions\Api\Data\StructureInterface[]
     */
    private $instances = [];

    /**
     * @var \Lof\MarketPermissions\Model\StructureFactory
     */
    private $structureFactory;

    /**
     * @var ResourceStructure
     */
    private $structureResource;

    /**
     * @var \Lof\MarketPermissions\Model\Structure\SearchProvider
     */
    private $searchProvider;

    /**
     * @param \Lof\MarketPermissions\Model\StructureFactory           $structureFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\Structure    $structureResource
     * @param \Lof\MarketPermissions\Model\Structure\SearchProvider   $searchProvider
     */
    public function __construct(
        \Lof\MarketPermissions\Model\StructureFactory $structureFactory,
        \Lof\MarketPermissions\Model\ResourceModel\Structure $structureResource,
        \Lof\MarketPermissions\Model\Structure\SearchProvider $searchProvider
    ) {
        $this->structureFactory = $structureFactory;
        $this->structureResource = $structureResource;
        $this->searchProvider = $searchProvider;
    }

    /**
     * Create structure service.
     *
     * @param \Lof\MarketPermissions\Api\Data\StructureInterface $structure
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Lof\MarketPermissions\Api\Data\StructureInterface $structure)
    {
        try {
            $this->structureResource->save($structure);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save seller: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$structure->getId()]);
        return $structure->getId();
    }

    /**
     * Get structure service.
     *
     * @param int $structureId
     * @return \Lof\MarketPermissions\Api\Data\StructureInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($structureId)
    {
        if (!isset($this->instances[$structureId])) {
            /** @var \Lof\MarketPermissions\Api\Data\StructureInterface $structure */
            $structure = $this->structureFactory->create();
            $structure->load($structureId);
            if (!$structure->getId()) {
                throw new NoSuchEntityException(
                    __(
                        'No such entity with %fieldName = %fieldValue',
                        ['fieldName' => 'id', 'fieldValue' => $structureId]
                    )
                );
            }
            $this->instances[$structureId] = $structure;
        }
        return $this->instances[$structureId];
    }

    /**
     * Delete structure service.
     *
     * @param \Lof\MarketPermissions\Api\Data\StructureInterface $structure
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(\Lof\MarketPermissions\Api\Data\StructureInterface $structure)
    {
        try {
            $structureId = $structure->getId();
            $this->structureResource->delete($structure);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete structure with id %1',
                    $structure->getId()
                ),
                $e
            );
        }
        unset($this->instances[$structureId]);
        return true;
    }

    /**
     * Delete structure by ID service.
     *
     * @param int $structureId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($structureId)
    {
        $structure = $this->get($structureId);
        return $this->delete($structure);
    }

    /**
     * Load Structure data collection by given search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPermissions\Api\Data\StructureSearchResultsInterface
     * @throws \InvalidArgumentException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->searchProvider->getList($searchCriteria);
    }
}
