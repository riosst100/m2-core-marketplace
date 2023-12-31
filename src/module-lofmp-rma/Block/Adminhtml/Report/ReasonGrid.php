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

namespace Lofmp\Rma\Block\Adminhtml\Report;

class ReasonGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner factory
     * @var \Magenhub\Chris\Model\ChrisFactory
     */
    protected $_chrisFactory;

    /**
     * Registry object
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * ReasonGrid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Lofmp\Rma\Model\ResourceModel\Item\Collection $RmaFactory
     * @param \Lofmp\Rma\Model\ResourceModel\Status\Collection $StatusFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Lofmp\Rma\Model\ResourceModel\Item\Collection $RmaFactory,
        \Lofmp\Rma\Model\ResourceModel\Status\Collection $StatusFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_RmaFactory = $RmaFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_StatusFactory = $StatusFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('LofmpGrid');
        $this->setSaveParametersInSession(false);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (isset($this->getRequest()->getParams()['type'])) {
            $params = $this->getRequest()->getParams();
            $endate = str_replace('.', '-', $params['to']);
            $startdate = str_replace('.', '-', $params['from']);
            $name = $params['pr'];
        } else {
            $type = 'month';
            $startdate = date("Y-m-d", strtotime("-1 month"));
            $endate = date("Y-m-d");
            $name = '';
        }

        $collection = $this->_RmaFactory
            ->addFieldToFilter(
                'main_table.reason_id',
                ['notnull' => true]
            )
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($startdate)
            ->addDateToFilter($endate)
            ->_getReasonSelectedColumns($name);
        if ($name) {
            $collection->addFieldToFilter('product.value', ['like' => '%' . $name . '%']);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chris_id',
            [
                'header' => __('Reason '),
                'type' => 'text',
                'index' => 'reason_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '30px',
            ]
        );
        if (($this->getRequest()->getParam('pr') != '')) {
            $this->addColumn(
                'item',
                [
                    'header' => __('Item'),
                    'index' => 'product_name',
                    'class' => 'xxx',
                    'width' => '50px',

                ]
            );
        }
        $this->addColumn(
            'rma',
            [
                'header' => __('Total Rma'),
                'index' => 'total_rma_cnt',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'request',
            [
                'header' => __('Total Request'),
                'index' => 'total_requested_cnt',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'return',
            [
                'header' => __('Total Return'),
                'index' => 'total_returned_cnt',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        /*$status = $this->_StatusFactory->getData();
        foreach ($status as $value) {
             $this->addColumn(
        $value['name'], [
        'header' => __($value['name']),
        'class' => 'xxx',
        'width' => '100px',
        'index' => $value['status_id'].'_cnt'
        ]
        );
        }*/

        $this->addExportType('*/Report/ExportCsvRS', __('CSV'));
        $this->addExportType('*/Report/ExportExcelRS', __('Excel'));
        return parent::_prepareColumns();
    }
}
