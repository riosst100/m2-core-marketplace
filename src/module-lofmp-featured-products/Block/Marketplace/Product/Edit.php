<?php

namespace Lofmp\FeaturedProducts\Block\Marketplace\Product;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @var string
     */
    protected $_mode = 'Edit';

    /**
     * @var string
     */
    protected $_template = 'Lofmp_FeaturedProducts::widget/form/container.phtml';

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Lofmp_FeaturedProducts';
        $this->_controller = 'marketplace_product';
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _preparelayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        return __('Edit Featured Product');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/index', ['_current' => true, 'back' => null]);
    }
}
