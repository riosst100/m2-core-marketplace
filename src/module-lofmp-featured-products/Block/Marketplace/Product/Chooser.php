<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lofmp\FeaturedProducts\Block\Marketplace\Product;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var string
     */
    protected $_template = 'Lofmp_FeaturedProducts::widget/grid/extended.phtml';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory
     */
    protected $featuredProductCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory
     */
    protected $sellerProductCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory
     * @param \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $featuredProductCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory,
        \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $featuredProductCollectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->productVisibility = $productVisibility;
        $this->productCollectionFactory = $collectionFactory;
        $this->featuredProductCollectionFactory = $featuredProductCollectionFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->sellerProductCollectionFactory = $sellerProductCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('featuredproducts/product/chooser', ['uniq_id' => $uniqId]);

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
     * @return $this
     */
    protected function _prepareCollection()
    {
        $sellerId = $this->marketplaceHelper->getSellerId();
        $currentDate = date('Y-m-d');
        $includeIds = [];
        $excludeIds = [];

        $sellerProductCollection = $this->sellerProductCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        foreach ($sellerProductCollection as $item){
            $includeIds[] = $item->getProductId();
        }
        $featuredProductCollection = $this->featuredProductCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
            //->addFieldToFilter('featured_from', ['lteq' => $currentDate])
            //->addFieldToFilter('featured_to', ['gteq' => $currentDate]);
        foreach ($featuredProductCollection as $item){
            $excludeIds[] = $item->getProductId();
        }

        $includeIds = array_diff($includeIds, $excludeIds);

        $productCollection =$this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $includeIds]);
        $this->setCollection($productCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
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
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('featuredproducts/product/chooser', ['_current' => true]);
    }
}
