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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab\SellerConditions;

use Lofmp\SellerRule\Model\Rule\Condition\SellerGroupOptions;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackedHelperData;
use Lof\MarketPlace\Model\ResourceModel\Seller\Collection;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;

class SpecifiedGrid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var Collection
     */
    protected $sellerCollectionInstance;

    /**
     * @var SellerGroupOptions
     */
    protected $sellerGroups;

    /**
     * SpecifiedGrid constructor.
     *
     * @param Context $context
     * @param BackedHelperData $backendHelper
     * @param CollectionFactory $sellerCollectionFactory
     * @param SellerGroupOptions $sellerGroups
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackedHelperData $backendHelper,
        CollectionFactory $sellerCollectionFactory,
        SellerGroupOptions $sellerGroups,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerGroups = $sellerGroups;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('specifiedChooserGrid_' . $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
        $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
        $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return SpecifiedGrid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_sellers') {
            $selected = $this->getSelectedSellers();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('seller_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('seller_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /***
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getSellerCollectionInstance();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define Chooser Grid Columns and filters
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_sellers',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_sellers',
                'values' => $this->getSelectedSellers(),
                'align' => 'center',
                'index' => 'seller_id',
                'use_index' => true
            ]
        );

        $this->addColumn(
            'seller_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '30px', 'index' => 'seller_id']
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Seller Name'),
                'width' => '75px',
                'index' => "name"
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Seller Email'),
                'width' => '100px',
                'index' => 'email'
            ]
        );

        $this->addColumn(
            'group_id',
            [
                'header' => __('Seller Groups'),
                'width' => '100px',
                'index' => 'group_id',
                'type' => 'options',
                'options' => $this->sellerGroups->getHashSellerGroupOptions()
            ]
        );
        return parent::_prepareColumns();
    }

    /***
     * @return Collection
     */
    protected function getSellerCollectionInstance()
    {
        if (!$this->sellerCollectionInstance) {
            $this->sellerCollectionInstance = $this->sellerCollectionFactory->create();
        }
        return $this->sellerCollectionInstance;
    }

    /***
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }

    /***
     * @return array
     */
    protected function getSelectedSellers()
    {
        return $this->getRequest()->getPost('selected', []);
    }
}
