<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\RuleRepositoryInterface;
use Lofmp\Rma\Api\Data\RuleSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\RuleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Rule as ResourceRule;
use Lofmp\Rma\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RuleRepository implements ruleRepositoryInterface
{

    protected $resource;

    protected $ruleFactory;

    protected $ruleCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataRuleFactory;

    private $storeManager;


    /**
     * @param ResourceRule $resource
     * @param RuleFactory $ruleFactory
     * @param RuleInterfaceFactory $dataRuleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRule $resource,
        RuleFactory $ruleFactory,
        RuleInterfaceFactory $dataRuleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRuleFactory = $dataRuleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\Rma\Api\Data\RuleInterface $rule)
    {
        /* if (empty($rule->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $rule->setStoreId($storeId);
        } */
        try {
            $rule->getResource()->save($rule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rule: %1',
                $exception->getMessage()
            ));
        }
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ruleId)
    {
        $rule = $this->ruleFactory->create();
        $rule->getResource()->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('rule with id "%1" does not exist.', $ruleId));
        }
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->ruleCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Lofmp\Rma\Api\Data\RuleInterface $rule)
    {
        try {
            $rule->getResource()->delete($rule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        return $this->delete($this->getById($ruleId));
    }
}
