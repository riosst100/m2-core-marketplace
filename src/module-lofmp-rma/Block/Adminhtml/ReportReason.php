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
namespace Lofmp\Rma\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Class FacebookSupport
 * @package Lofmp\FaceSupportLive\Block\Chatbox
 */
class ReportReason extends Template implements \Magento\Widget\Block\BlockInterface
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
        $my_template = "report/rma/reason/grid/chart.phtml";
        if ($this->hasData("reasontemplate") && $this->getData("reasontemplate")) {
            $my_template = $this->getData("reasontemplate");
        } elseif (isset($data['reasontemplate']) && $data['reasontemplate']) {
            $my_template = $data['reasontemplate'];
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
        $this->pageConfig->getTitle()->set(__('RMA Report by Reason'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('RMA Report by Reason'));
        }
    }
}
