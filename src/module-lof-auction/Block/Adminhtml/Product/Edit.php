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

namespace Lof\Auction\Block\Adminhtml\Product;

use Lof\Auction\Model\ProductFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var ProductFactory
     */
    private $_auction;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $auction
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $auction,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_auction = $auction;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Lof_Auction';
        $this->_controller = 'adminhtml_product';

        $id = $this->getRequest()->getParam('id');
        $auction = $this->_auction->create()->load($id);
        $auctionStatus = $auction->getAuctionStatus();
        if ($this->_isAllowedAction('Lof_Auction::product_save')) {
            $this->buttonList->update('save', 'label', __('Save Product'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }
        if ($this->_isAllowedAction('Lof_Auction::product_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Product'));
        } else {
            $this->buttonList->remove('delete');
        }
        if ($auctionStatus != '' && ( $auctionStatus == AuctionStatus::STATUS_COMPLETE || $auctionStatus == AuctionStatus::STATUS_TIME_END)) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('saveandcontinue');
        } elseif ($auction->getId()) {
            $this->buttonList->add(
                'resetBid',
                [
                    'label' => __('Reset Bid'),
                    'class' => 'delete',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'resetBid', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        }
        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeader()
    {
        if ($this->_coreRegistry->registry('lofauction_product')->getId()) {
            return __("Edit Product");
        } else {
            return __('New Product');
        }
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getResetUrl()
    {
        return $this->getUrl('lofauction/*/reset', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('lofauction/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };

            function eventListenerReset () {
                deleteConfirm('Are you sure you want to do this?', '".$this->_getResetUrl()."', {data: {}});
            }
            var listenedElementyReset = document.querySelector(\"*[data-ui-id='lofauction-product-edit-resetbid-button']\");
            if (listenedElementyReset) {
                listenedElementyReset.onclick = function (event) {
                    var targetElement = listenedElementyReset;
                    if (event && event.target) {
                        targetElement = event.target;
                    }
                    eventListenerReset.apply(targetElement);
                }
            }
        ";
        return parent::_prepareLayout();
    }
}
