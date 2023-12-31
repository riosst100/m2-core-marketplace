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

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var
     */
    protected $_chrisFactory;

    /**
     * Registry object
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context $context [description]
     * @param \Magento\Backend\Helper\Data $backendHelper [description]
     * @param \Magenhub\Chris\Model\ChrisFactory $chrisFactory [description]
     * @param \Magento\Framework\Registry $coreRegistry [description]
     * @param array $data [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Lofmp\Rma\Model\ResourceModel\Rma\Collection $RmaFactory,
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
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (isset($this->getRequest()->getParams()['type'])
        ) {
            $params = $this->getRequest()->getParams();
            $type = $params['type'];
            $endate = str_replace('.', '-', $params['to']);
            $startdate = str_replace('.', '-', $params['from']);
        } else {
            $type = 'month';
            $startdate = date("Y-m-d", strtotime("-1 month"));
            $endate = date("Y-m-d");
        }
        $collection = $this->_RmaFactory
            ->setPeriodType($type)
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($startdate)
            ->addDateToFilter($endate)
            ->_getSelectedColumns();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'time',
            [
                'header' => __('Time'),
                'type' => 'text',
                'index' => 'time',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '30px',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Total Rma'),
                'index' => 'total_rma_cnt',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $status = $this->_StatusFactory->getData();
        foreach ($status as $value) {
            $this->addColumn(
                $value['name'],
                [
                    'header' => __($value['name']),
                    'class' => 'xxx',
                    'width' => '100px',
                    'index' => $value['status_id'] . '_cnt'
                ]
            );
        }

        $this->addExportType('*/Report/ExportCsv', __('CSV'));
        $this->addExportType('*/Report/ExportExcel', __('Excel'));

        return parent::_prepareColumns();
    }
}
