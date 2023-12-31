<?php

namespace Lofmp\StoreLocator\Block\StoreLocator\Tag;

class TagBuild extends \Magento\Framework\View\Element\Template
{
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Tag
     */
     protected $_tagFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\StoreLocator\Model\Tag $tagFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    )
    {
        $this->_helper        = $helper;
        $this->_coreRegistry  = $registry;
        $this->_tagFactory = $tagFactory;
        $this->_resource      = $resource;
        parent::__construct($context);
       }

    /**
     * Retrieve form action
     *
     * @return string
     */
     public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Tag Build'));
        return parent::_prepareLayout ();
    }
    public function getFormAction()
    {
            // compagnymodule is given in routes.xml
            // controller_name is folder name inside controller folder
            // action is php file name inside above controller_name folder

        return '/compagnymodule/controller_name/action';
        // here controller_name is manage, action is contact
    }
}