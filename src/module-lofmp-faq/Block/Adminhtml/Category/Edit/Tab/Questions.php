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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\Faq\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Catalog\Model\Product;

class Questions extends Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
        \Lofmp\Faq\Model\Question $question,
        array $data = []
    ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_question = $question;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cross_sell_product_grid');
        $this->setDefaultSort('question_id');
        $this->setUseAjax(true);
        if ($this->getCategory() && $this->getCategory()->getCategoryId()) {
            $this->setDefaultFilter(['in_questions' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retrieve currently edited product model
     *
     * @return Product
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Add filter
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_questions') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('question_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('question_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_question->getCollection();
        if ($this->isReadonly()) {
            $questionIds = $this->_getSelectedProducts();
            if (empty($questionIds)) {
                $questionIds = [0];
            }
            $collection->addFieldToFilter('question_id', ['in' => $questionIds]);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getCategory() && $this->getCategory()->getCrosssellReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
                'in_questions',
                [
                    'type' => 'checkbox',
                    'name' => 'in_questions',
                    'values' => $this->_getSelectedProducts(),
                    'align' => 'center',
                    'index' => 'question_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
            );
        }

        $this->addColumn(
            'question_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'question_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'question_title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'question_is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => [0 => __('Disabled'), 1 => __('Enabled')],
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'question_position',
            [
                'header'                    => __('Position'),
                'name'                      => 'question_position',
                'type'                      => 'number',
                'validate_class'            => 'validate-number',
                'index'                     => 'position',
                'header_css_class'          => 'col-position',
                'column_css_class'          => 'col-position',
                'editable'                  => true,
                'edit_only'                 => true,
                'sortable'                  => false,
                'filter_condition_callback' => [$this, 'filterProductPosition']
            ]
            );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? $this->_getData(
            'grid_url'
        ) : $this->getUrl(
            'lofmpfaq/category/questionsGrid',
            ['_current' => true]
        );
    }

    /**
     * Retrieve selected crosssell products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsCrossSell();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedQuestionProducts());
        }
        return $products;
    }

    /**
     * Retrieve crosssell products
     *
     * @return array
     */
    public function getSelectedQuestionProducts()
    {
        $products = [];
        if(!empty($this->_coreRegistry->registry('current_category')->getData('questions'))){
            foreach ($this->_coreRegistry->registry('current_category')->getData('questions') as $product) {
                $products[$product['question_id']] = ['question_position' => $product['position']];
            }
        }
        return $products;
    }

    /**
     * Apply `position` filter to cross-sell grid.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended $column
     * @return $this
     */
    public function filterProductPosition($collection, $column)
    {
        $collection->addLinkAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        return $this;
    }
}
