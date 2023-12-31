<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Landofcoder
 * @package     Lof_Quickrfq
 * @copyright   Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */
namespace Lofmp\Quickrfq\Block\Marketplace\Quickrfq;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class QuickrfqButton
 */
class QuickrfqButton extends \Magento\Backend\Block\Widget\Container
{
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
                'onclick' => "window.location.href = '" . $this->getDeleteUrl() . "'",
                'class' => 'delete'
            ]
        );
        $this->buttonList->add(
            'done',
            [
                'label' => __('Done'),
                'onclick' => "window.location.href = '" . $this->getDoneUrl() . "'",
                'class' => 'done'
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
    public function getDoneUrl()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->getUrl('*/*/done', ['quickrfq_id' => $quoteId]);
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
