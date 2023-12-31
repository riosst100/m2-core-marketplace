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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Block\Adminhtml\Product;

use Lof\Auction\Model\Product;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;

/**
 * CMS block chooser for Wysiwyg CMS widget
 */
class Chooser extends \Lof\Auction\Block\Widget\Grid\Extended
{


    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;


    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Visibility $productVisibility
     * @param Session $customerSession
     * @param \Magento\Contact\Helper\Data $contactHelper
     * @param CustomerFactory $customerFactory
     * @param CurrentCustomer $currentCustomer
     * @param Product $auctionProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Visibility $productVisibility,
        Session $customerSession,
        \Magento\Contact\Helper\Data $contactHelper,
        CustomerFactory $customerFactory,
        CurrentCustomer $currentCustomer,
        Product $auctionProduct,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $backendHelper, $customerSession, $contactHelper, $customerFactory, $currentCustomer, $auctionProduct, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('block_identifier');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('catalog/product_featured/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Chooser'
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($product = $element->getFeaturedProduct()) {
            $chooser->setLabel($this->escapeHtml($product->getName()));
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                ' .
            $chooserJsObject .
            '.setElementValue(blockId);
                ' .
            $chooserJsObject .
            '.setElementLabel(blockTitle);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare Cms static blocks collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection =$this->_collectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'status'])
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for Cms blocks grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            ['header' => __('ID'), 'align' => 'right', 'index' => 'entity_id', 'width' => 50]
        );

        $this->addColumn('chooser_title', ['header' => __('Product Name'), 'align' => 'left', 'index' => 'name']);

        $this->addColumn(
            'chooser_identifier',
            ['header' => __('SKU'), 'align' => 'left', 'index' => 'sku']
        );

        $this->addColumn(
            'chooser_is_active',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [0 => __('Disabled'), 1 => __('Enabled')]
            ]
        );


        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('catalog/product_featured/chooser', ['_current' => true]);
    }
}
