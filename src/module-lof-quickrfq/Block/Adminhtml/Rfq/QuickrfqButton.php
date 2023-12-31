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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Quickrfq\Block\Adminhtml\Rfq;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
/**
 * Class BackButton
 */
class QuickrfqButton extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    private $_mode;
    /**
     * @var string
     */
    private $_objectId;
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * QuickrfqButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
    }

    /**
     *
     */
    protected function _construct()
    {

        parent::_construct();
        
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->buttonList->add(
            'back',
            [
                'label' => __('Back'),
                'onclick' => "window.location.href = '" . $this->getBackUrl() . "'",
                'class' => 'back'
            ]
        );

        $this->buttonList->add(
            'approval',
            [
                'label' => __('Approve Quote'),
                'onclick' => "window.location.href = '" . $this->getApproveUrl() . "'",
                'class' => 'save primary'
            ]
        );

        //show re quote button
        $this->buttonList->add(
            'requote',
            [
                'label' => __('Re-Open Quote'),
                'onclick' => "window.location.href = '" . $this->getRenewUrl() . "'",
                'class' => 're-quote re-new'
            ]
        );
        
        $this->buttonList->add(
            'close',
            [
                'label' => __('Close Quote'),
                'onclick' => "window.location.href = '" . $this->getCloseUrl() . "'",
                'class' => 'close'
            ]
        );
        $this->buttonList->add(
            'delete',
            [
                'label' => __('Delete'),
                'onclick' => "
                    if( confirm('".__("Do you really want to delete this RFQ?")."') ){
                        window.location.href = '" . $this->getDeleteUrl() . "'
                    }
                ",
                'class' => 'delete'
            ]
        );
    }

    /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $quote = $this->_coreRegistry->registry('quickrfq');
        if ($quote && $quote->getStatus() !== \Lof\Quickrfq\Model\Quickrfq::STATUS_DONE) {
            $this->buttonList->remove('requote');
        }
        if ($quote && ($quote->getStatus() == \Lof\Quickrfq\Model\Quickrfq::STATUS_DONE || $quote->getStatus() == \Lof\Quickrfq\Model\Quickrfq::STATUS_APPROVE)) {
            $this->buttonList->remove('approval');
        }
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->getUrl('*/*/delete', ['quickrfq_id' => $quoteId]);
    }

    /**
     * @return string
     */
    public function getApproveUrl()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->getUrl('*/*/approve', ['quickrfq_id' => $quoteId]);
    }

    /**
     * @return string
     */
    public function getRenewUrl()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->getUrl('*/*/renew', ['quickrfq_id' => $quoteId]);
    }

    /**
     * @return string
     */
    public function getCloseUrl()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->getUrl('*/*/close', ['quickrfq_id' => $quoteId]);
    }
}
