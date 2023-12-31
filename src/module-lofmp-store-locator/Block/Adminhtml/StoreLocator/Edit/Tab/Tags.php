<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab;

class Tags extends \Magento\Backend\Block\Widget\Grid\Extended
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

    protected $_tagFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Backend\Helper\Data
     * @param \Magento\Catalog\Model\Product\LinkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory
     * @param \Magento\Catalog\Model\Product\Type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status
     * @param \Magento\Catalog\Model\Product\Visibility
     * @param \Magento\Framework\Registry
     * @param \Lofmp\StoreLocator\Model\Category
     * @param array
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
        \Lofmp\StoreLocator\Model\Tag $tagFactory,
         \Lofmp\StoreLocator\Helper\Data $dataHelper,
        array $data = []
        ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_tagFactory = $tagFactory;
        $this->dataHelper = $dataHelper; 
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
        $this->setId('storelocator_tags_grid');
        $this->setDefaultSort('tag_id');
        $this->setUseAjax(true);
        if ($this->getTag() && $this->getTag()->getTagId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('current_storelocator');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedTags();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.tag_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('main_table.tag_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_tagFactory->getCollection();

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedTags();
            if (empty($productIds)) {
                $productIds = [0];
            }
            $collection->addFieldToFilter('main_table.tag_id', ['in' => $productIds]);
        }
        $tag = $this->getTag();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn(
           'in_products',
           [
               'type' => 'checkbox',
               'name' => 'in_products',
               'values' => $this->getSelectedTags(),
               'align' => 'center',
               'index' => 'tag_id',
               'header_css_class' => 'col-select',
               'column_css_class' => 'col-select',
               'field_name' => 'selectedTags[]',
           ]
           );

        $this->addColumn(
            'gtag_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'tag_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
            );
        $this->addColumn(
            'gname',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
            );
  

        $this->addColumn(
            'gaction',
            [
                'header' => __('Action'),
                'type' => 'action',
                'renderer'  => 'Lofmp\StoreLocator\Block\Adminhtml\Tag\Renderer\TagAction',
            ]
            );
        // $this->addColumn(
        //     'position',
        //     [
        //         'header'                    => __('Position'),
        //         'name'                      => 'position',
        //         'type'                      => 'number',
        //         'validate_class'            => 'validate-number',
        //         'index'                     => 'position',
        //         'header_css_class'          => 'col-position',
        //         'column_css_class'          => 'col-position',
        //         'editable'                  => true,
        //         'edit_only'                 => true,
        //         'sortable'                  => false,
        //         'filter_condition_callback' => [$this, 'filterProductPosition']
        //     ]
        //     );

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        $tag = $this->getTag();
        return $this->_getData(
            'grid_url'
            ) ? $this->_getData(
            'grid_url'
            ) : $this->getUrl(
            'storelocator/*/tagsGrid/tag_id/'.$tag->getTagId(),
            ['_current' => true]
            );
    }

    protected function _getSelectedTags()
    {
        $products = $this->getProductsUpsell();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedTags());
        }
        return $products;
    }

    public function getSelectedTags()
    {
        $tag = array();
        $id = $this->getRequest()->getParam('storelocator_id');
        if ($id) {
             $tag = $this->dataHelper->get_tagid($id);
           
        }
        return $tag;
    }

    /**
     * Apply `position` filter to cross-sell grid.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended $column
     * @return $this
     */
    public function filterProductPosition($collection, $column)
    {
        $condition = $column->getFilter()->getCondition();
        $tag = $this->getTag();
        $condition['tag_id'] = $tag->getTagId();
        $collection->addLinkTagToFilter($column->getIndex(), $condition);
        return $this;
    }
}
