<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Resolution;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Grid constructor.
     *
     * @param \Lofmp\Rma\Model\ResolutionFactory $resolutionFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Lofmp\Rma\Model\ResolutionFactory $resolutionFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->resolutionFactory = $resolutionFactory;
        $this->context = $context;
        $this->backendHelper = $backendHelper;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->resolutionFactory->create()
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('resolution_id', [
            'header' => __('ID'),
            'index' => 'resolution_id',
            'filter_index' => 'main_table.resolution_id',
        ]);
        $this->addColumn('name', [
            'header' => __('Title'),
            'index' => 'name',
            'frame_callback' => [$this, '_renderCellName'],
            'filter_index' => 'main_table.name',
        ]);
        $this->addColumn('sort_order', [
            'header' => __('Sort Order'),
            'index' => 'sort_order',
            'filter_index' => 'main_table.sort_order',
        ]);
        $this->addColumn('is_active', [
            'header' => __('Active'),
            'index' => 'is_active',
            'filter_index' => 'main_table.is_active',
            'type' => 'options',
            'options' => [
                0 => __('No'),
                1 => __('Yes'),
            ],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param string $renderedValue
     * @param string $item
     * @param string $column
     * @param string $isExport
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _renderCellName($renderedValue, $item, $column, $isExport)
    {
        return $item->getName();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('resolution_id');
        $this->getMassactionBlock()->setFormFieldName('resolution_id');
        $statuses = [
            ['label' => '', 'value' => ''],
            ['label' => __('Disabled'), 'value' => 0],
            ['label' => __('Enabled'), 'value' => 1],
        ];
        $this->getMassactionBlock()->addItem('is_active', [
            'label' => __('Change status'),
            'url' => $this->getUrl('*/*/massChange', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name' => 'is_active',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Status'),
                    'values' => $statuses,
                ],
            ],
        ]);
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
