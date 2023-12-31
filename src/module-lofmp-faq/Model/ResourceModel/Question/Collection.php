<?php

namespace Lofmp\Faq\Model\ResourceModel\Question;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'question_id';

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
        $this->_init('Lofmp\Faq\Model\Question', 'Lofmp\Faq\Model\ResourceModel\Question');
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('question_id', 'title');
    }

    public function getCategoryTitle(){
        $this->getSelect()->join(
            ['category' => $this->getTable('lofmp_faq_category')],
            'main_table.category_id = category.category_id',
            ['category_title' => 'title']
        );
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['category' => $this->getTable('lofmp_faq_category')],
            'main_table.category_id = category.category_id',
            ['category_title' => 'title']
        )->join(
            ['seller_table' => $this->getTable('lof_marketplace_seller')],
            'main_table.seller_id = seller_table.seller_id',
            ['seller_name' => 'seller_table.name']
        );
    }
}