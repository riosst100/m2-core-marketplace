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



namespace Lofmp\Rma\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;
use Lofmp\Rma\Model\QuickResponseFactory;

class Grid extends GridExtended
{
    public function __construct(
        QuickResponseFactory $templateFactory,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->templateFactory = $templateFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('template_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->templateFactory->create()
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id', [
            'header'       => __('ID'),
            'index'        => 'template_id',
            'filter_index' => 'main_table.template_id',
        ]);
        $this->addColumn('name', [
            'header'       => __('Title'),
            'index'        => 'name',
            'filter_index' => 'main_table.name',
        ]);
        $this->addColumn('is_active', [
            'header'       => __('Active'),
            'index'        => 'is_active',
            'filter_index' => 'main_table.is_active',
            'type'         => 'options',
            'options'      => [
                0 => __('No'),
                1 => __('Yes'),
            ],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template_id');
        $statuses = [
            ['label' => '', 'value' => ''],
            ['label' => __('Disabled'), 'value' => 0],
            ['label' => __('Enabled'), 'value' => 1],
        ];
        $this->getMassactionBlock()->addItem('is_active', [
            'label'      => __('Change status'),
            'url'        => $this->getUrl('*/*/massChange', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name'   => 'is_active',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => __('Status'),
                    'values' => $statuses,
                ],
            ],
        ]);
        $this->getMassactionBlock()->addItem('delete', [
            'label'   => __('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @param \Lofmp\Rma\Model\QuickResponse $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
