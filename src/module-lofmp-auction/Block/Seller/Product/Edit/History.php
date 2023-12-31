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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\Auction\Block\Seller\Product\Edit;

use Lof\Auction\Block\Adminhtml\Product\Edit\Tab\Renderer\CustomerName;
use Lof\Auction\Model\HistoryFactory;
use Magento\Backend\Block\Template\Context;
use Lofmp\Auction\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\LinkFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Registry;

/**
 * Class History
 * Lof\Auction\Block\Seller\Product\Edit
 */
class History extends Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Type
     */
    protected $_type;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var Visibility
     */
    protected $_visibility;
    /**
     * @var \Lof\Auction\Helper\Data
     */
    protected $helper;
    /**
     * @var HistoryFactory
     */
    private $_history;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param LinkFactory $linkFactory
     * @param CollectionFactory $setsFactory
     * @param ProductFactory $productFactory
     * @param Type $type
     * @param Status $status
     * @param Visibility $visibility
     * @param Registry $coreRegistry
     * @param HistoryFactory $history
     * @param \Lof\Auction\Helper\Data $helper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        LinkFactory $linkFactory,
        CollectionFactory $setsFactory,
        ProductFactory $productFactory,
        Type $type,
        Status $status,
        Visibility $visibility,
        Registry $coreRegistry,
        HistoryFactory $history,
        \Lof\Auction\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_history = $history;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('history_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getProduct() && $this->getProduct()->getProductId()) {
            $this->setDefaultFilter(['in_bids' => 1]);
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
    public function getProduct()
    {
        return $this->_coreRegistry->registry('lofauction_product');
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->getProduct()->getId()) {
            return parent::toHtml();
        } else {
            return "";
        }
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_history->create()
            ->getCollection()->addFieldToFilter('auction_id', $this->getProduct()->getId());
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
        return $this->getProduct() && $this->getProduct()->getCrosssellReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'customer_id',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_id',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
                'renderer' => CustomerName::class
            ]
        );
        $this->addColumn(
            'auction_amount',
            [
                'header' => __('Auction Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'auction_amount',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header'    => __('Is Spam'),
                'index'     => 'status',
                'align'     => 'left',
                'type'      => 'select',
                'width'     => '150px',
                'options'   => ['1' => __('No'), '0' => __('Yes')]
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'set_spam',
            [
                'header'    => __('Action'),
                'name'      => 'lof_spam',
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption'     => __('Marked As Spam'),
                        'url'         => ['base' => 'lofmpauction/product/setSpam'],
                        'field'       => 'id',
                        'data-column' => 'action',
                    ],
                ],
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'entity_id',
                'is_system' => true,
                'column_css_class'=>'hidden',
                'header_css_class'=>'hidden',
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
            'lofmpauction/product/history',
            [
                'id' => $this->getProduct()->getId()
            ]
        );
    }
}
