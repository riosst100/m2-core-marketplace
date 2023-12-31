<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Block\Adminhtml\Product\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
     /**
      * @var \Lofmp\PriceComparison\Model\Config\Source\MethodTypes
      */
    protected $methodTypes;

    protected $_productloader;

    protected $sellerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Lofmp\PriceComparison\Model\Config\Source\Status $status,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->status = $status;
        $this->_systemStore = $systemStore;
        $this->_productloader = $_productloader;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofmppricecomparison_product');

        if ($this->_isAllowedAction('Lofmp_PriceComparison::product')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Information')]);
        
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
         $fieldset->addField(
             'product_id',
             'note',
             [
             'name' => 'product_id',
             'label' => __('Product Id'),
             'text'     => $model->getProductId(),
             'disabled' => $isElementDisabled
             ]
         );
         $urlProduct = $this->_urlBuilder->getUrl('catalog/product/edit', ['id' => $model->getProductId()]);
         $urlSeller = $this->_urlBuilder->getUrl('lofmarketplace/seller/edit', ['seller_id' => $model->getSellerId()]);
         $product = $this->_productloader->create()->load($model->getProductId());
         $seller = $this->sellerFactory->create()->load($model->getSellerId());
          $fieldset->addField(
              'product_name',
              'note',
              [
              'name' => 'product_name',
              'label' => __('Product Name'),
              'text'     => "<a href='".$urlProduct."' target='blank' title='".$product->getName()."'>".$product->getName()."</a>",
              'disabled' => $isElementDisabled
              ]
          );
          $fieldset->addField(
              'seller_name',
              'note',
              [
              'name' => 'seller_name',
              'label' => __('Seller Name'),
              'text'     => "<a href='".$urlSeller."' target='blank' title='".$product->getName()."'>".$seller->getName()."</a>",
              'disabled' => $isElementDisabled
              ]
          );
        $fieldset->addField(
            'qty',
            'note',
            [
            'name' => 'qty',
            'label' => __('Quantity'),
            'text'     => $model->getQty(),
            'disabled' => $isElementDisabled
            ]
        );
         $fieldset->addField(
             'price',
             'note',
             [
             'name' => 'price',
             'label' => __('Price'),
             'text'     => $model->getPrice(),
             'disabled' => $isElementDisabled
             ]
         );
           $fieldset->addField(
               'status',
               'select',
               [
               'name' => 'status',
               'label' => __('Status'),
               'title' => __('Status'),
               'values' => $this->status->toOptionArray(),
               'required' => true,
               'disabled' => $isElementDisabled
               ]
           );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Product Data');
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Product Data');
    }
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
