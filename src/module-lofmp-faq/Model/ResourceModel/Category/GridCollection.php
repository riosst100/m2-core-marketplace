<?php

namespace Lofmp\Faq\Model\ResourceModel\Category;

class GridCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy,$eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Faq\Model\Category', 'Lofmp\Faq\Model\ResourceModel\Category');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        // $this->getSelect()->joinLeft(
        //     ['parent_category' => $this->getMainTable()],
        //     'main_table.parent_id = parent_category.category_id',
        //     ['parent_title' => 'COALESCE(parent_category.title, "None")']
        // );
        // )->join(
        //     ['seller_table' => $this->getTable('lof_marketplace_seller')],
        //     'main_table.seller_id = seller_table.seller_id',
        //     ['seller_name' => 'seller_table.name']
        // );
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performSellerAfterLoad('lof_marketplace_seller', 'seller_id');

        return parent::_afterLoad();
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function performSellerAfterLoad($tableName, $columnName = "seller_id")
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();

            foreach ($this as &$item) {
                $entityId = $item->getData($columnName);

                if (!$entityId) {
                    $item->setData('seller_name', "NaN");
                    continue;
                }
                $select = $connection->select()->from(['lof_marketplace_seller' => $this->getTable($tableName)])
                                        ->where($columnName . ' = (?)', $entityId);
                $result = $connection->fetchAssoc($select);
                if ($result && isset($result[$entityId])) {
                    $item->setData('seller_name', $result[$entityId]["name"]);
                } else {
                    $item->setData('seller_name', "NaN");
                }
            }
        }
    }
}
