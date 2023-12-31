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

namespace Lof\Auction\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

/**
 * Class Tabs
 * @package Lof\Auction\Block\Adminhtml\Product\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var Registry
     */
    private $_coreRegistry;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder ,
     * @param Session $authSession
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @throws LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Auction Information'));

        $this->addTab(
            'auction_product',
            [
                'label' => __('Auction Product'),
                'content' => $this->getLayout()->createBlock('Lof\Auction\Block\Adminhtml\Product\Edit\Tab\Main')->toHtml()
            ]
        );
        $this->addTab(
            'bids',
            [
                'label' => __('Manual Bids'),
                'url' => $this->getUrl('lofauction/product/bids', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        $this->addTab(
            'auto_bids',
            [
                'label' => __('Auto Bids'),
                'url' => $this->getUrl('lofauction/product/autobids', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        $this->addTab(
            'history_bids',
            [
                'label' => __('History Bids'),
                'url' => $this->getUrl('lofauction/product/history', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        $this->addTab(
            'maxabsentee',
            [
                'label' => __('Max Absentee Bids'),
                'url' => $this->getUrl('lofauction/product/maxabsentee', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
    }
}
