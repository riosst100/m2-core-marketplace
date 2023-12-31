<?php

namespace Lofmp\FeaturedProducts\Block\Marketplace\Product\Edit;

use Magento\Framework\UrlInterface;

class Form extends \Lof\MarketPlace\Block\Seller\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->urlBuilder = $urlBuilder;
    }

    protected function _prepareForm()
    {
        $data = $this->getProduct()->getData();
        $id = $data['product_id'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $featuredProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('Edit Featured Product'), 'class' => '']
        );

        $fieldset->addField(
            'name',
            'label',
            [
                'name' => 'name',
                'label' => __('Product Name'),
                'title' => __('Product Name'),
                'note' => '<a href="' . $featuredProduct->getProductUrl() . '" target="_blank">' . $featuredProduct->getName() . '</a>',
            ]
        );

        $fieldset->addField(
            'sku',
            'label',
            [
                'name' => 'name',
                'label' => __('SKU'),
                'title' => __('SKU'),
                'note' => $featuredProduct->getSku(),
            ]
        );
        $editUrl = $this->urlBuilder->getUrl(
            'catalog/product/edit',
            ['id' => $featuredProduct->getId()]
        );
        $fieldset->addField(
            'action',
            'label',
            [
                'name' => 'name',
                'label' => __('Action'),
                'title' => __('Action'),
                'note' => '<a href="' . $editUrl . '">' . __('Edit') . '</a>',
            ]
        );

        $fieldset->addField(
            'featured_from',
            'date',
            [
                'name' => 'featured_from',
                'label' => __('Featured From'),
                'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            ]
        );

        $fieldset->addField(
            'featured_to',
            'date',
            [
                'name' => 'featured_to',
                'label' => __('Featured To'),
                'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'class' => 'validate-number',
            ]
        );

        $form->addField(
            'id',
            'hidden',
            [
                'name' => 'id',
                'required' => true,
                'readonly' => true
            ]
        );

        // add dependence javascript block
//        $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
//        $this->setChild('form_after', $dependenceBlock);
//        $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');

        $form->setValues($data);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_featured_product');
    }
}
