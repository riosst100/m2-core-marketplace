<?php

namespace Lofmp\Rma\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Class FacebookSupport
 * @package Lofmp\FaceSupportLive\Block\Chatbox
 */
class ReportProduct extends Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * FacebookSupport constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $my_template = "report/rma/product/grid/chart.phtml";
        if ($this->hasData("producttemplate") && $this->getData("producttemplate")) {
            $my_producttemplate = $this->getData("producttemplate");
        } elseif (isset($data['producttemplate']) && $data['producttemplate']) {
            $my_producttemplate = $data['template'];
        }
        if ($my_template) {
            $this->setTemplate($my_template);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('RMA Report by Product'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('RMA Report by Product'));
        }
    }
}
