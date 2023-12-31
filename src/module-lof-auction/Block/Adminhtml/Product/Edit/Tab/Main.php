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

namespace Lof\Auction\Block\Adminhtml\Product\Edit\Tab;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ResourceModel\Product\Source\AllProductsForAuction;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\System\Store;

/**
 * Class Main
 * @package Lof\Auction\Block\Adminhtml\Product\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var ProductFactory
     */
    protected $_productloader;
    /**
     * @var AllProductsForAuction
     */
    protected $_allProducts;
    /**
     * @var array configuration of Auction
     */

    protected $helper;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param ProductFactory $_productloader
     * @param AllProductsForAuction $allProducts
     * @param Data $helper
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        ProductFactory $_productloader,
        AllProductsForAuction $allProducts,
        Data $helper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_allProducts = $allProducts;
        $this->_systemStore = $systemStore;
        $this->_productloader = $_productloader;
        $this->timezone = $timezone;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * @return Main
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofauction_product');
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $auctionConfig = $this->helper->getAuctionConfiguration();
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('Product Information'), 'class' => 'fieldset-wide fieldset-widget-options']
        );
        if ($model->getId()) {
            $fieldset->addField('auction_entity_id', 'hidden', ['name' => 'auction_entity_id']);
        }
        if (!$model->getProductId()) {
            $chooserField = $fieldset->addField(
                'options_fieldset_entity_id',
                'label',
                [
                    'name' => 'product_id',
                    'label' => __('Product'),
                    'required' => true,
                    'class' => 'widget-option',
                    'note' => __("Select a product"),
                ]
            );
            /*Add chooser helper for the field*/
            $helperData = [
                'data' => [
                    'button' => ['open' => __("Select Product...")]
                ]
            ];
            $helperBlock = $this->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser',
                '',
                $helperData
            );

            $helperBlock->setConfig($helperData)
                ->setFieldsetId($fieldset->getId())
                ->prepareElementHtml($chooserField)
                ->setValue($model->getId());
        } else {
            $product = $this->helper->getProductbyId($model->getProductId());
            $fieldset->addField(
                'product_id',
                'note',
                [
                    'name' => 'product_id',
                    'label' => __('Product'),
                    'title' => __('Product'),
                    'text' => "<a href=" . $product->getProductUrl() . " target='_blank'>".
                        $product->getName() .
                        "</a>"
                ]
            );
            $fieldset->addField(
                'product_price',
                'note',
                [
                    'name' => 'product_price',
                    'label' => __('Product Price'),
                    'title' => __('Product Price'),
                    'text' => $product->getFormatedPrice()
                ]
            );
        }
        if ($model->getAuctionStatus() != '') {
            $statusNumber = $model->getAuctionStatus();
            $className = $this->helper->getAuctionClass($statusNumber);
            $fieldset->addField(
                'auction_status',
                'text',
                [
                    'name' => 'auction_status',
                    'label' => __('Status'),
                    'title' => __('Status'),
                    'readonly' => true,
                    'class' => 'lofau-status-box lofau-status-'.$className
                ]
            );
        }
        $fieldset->addField(
            'starting_price',
            'text',
            [
                'name' => 'starting_price',
                'label' => __('Starting Price'),
                'id' => 'starting_price',
                'title' => __('Starting Price'),
                'type' => 'price',
                'class' => 'required-entry validate-zero-or-greater',
                'note' => __('The Starting Price of the auction'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'max_amount',
            'text',
            [
                'name' => 'max_amount',
                'label' => __('Max Price'),
                'id' => 'max_price',
                'title' => __('Max Price'),
                'type' => 'price',
                'class' => 'validate-zero-or-greater',
                'note' => __('The Stopping Price of the auction'),
            ]
        );
        $fieldset->addField(
            'reserve_price',
            'text',
            [
                'name' => 'reserve_price',
                'label' => __('Reserve Price'),
                'id' => 'reserve_price',
                'title' => __('Reserve Price'),
                'values' => [],
                'class' => ' validate-zero-or-greater',
                'note' => __('The price stipulated as the lowest acceptable by the seller for an item sold at auction. If the closing price is lower than Reserve Price, the winner must buy the product with the cost is Reserve Price.'),
            ]
        );

        $fieldset->addField(
            'start_auction_time',
            'date',
            [
                'name' => 'start_auction_time',
                'label' => __('Start Time'),
                'id' => 'start_auction_time',
                'title' => __('Start Time'),
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss',
                'class' => 'required-entry admin__control-text',
                'style' => 'width:210px',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'stop_auction_time',
            'date',
            [
                'name' => 'stop_auction_time',
                'label' => __('Stop Time'),
                'id' => 'stop_auction_time',
                'title' => __('Stop Time'),
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss',
                'class' => 'required-entry admin__control-text',
                'style' => 'width:210px',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'days',
            'text',
            [
                'name' => 'days',
                'label' => __('Number of Days Till Winner Can Buy'),
                'id' => 'days',
                'title' => __('Number of Days Till Winner Can Buy'),
                'class' => 'required-entry integer validate-zero-or-greater',
                'note' => __('Number of days winner can purchase the product with winning price.'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'min_qty',
            'text',
            [
                'name' => 'min_qty',
                'label' => __('Minimum Quantity'),
                'id' => 'min_qty',
                'title' => __('Minimum Quantity'),
                'class' => 'required-entry validate-zero-or-greater',
                'note' => __('Minimum qty of product that winner have to buy.'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'max_qty',
            'text',
            [
                'name' => 'max_qty',
                'label' => __('Maximum Quantity'),
                'id' => 'max_qty',
                'title' => __('Maximum Quantity'),
                'class' => 'required-entry validate-zero-or-greater',
                'note' => __('Maximum qty of product that winner is allowed to buy.'),
                'required' => true,
            ]
        );

        if ($auctionConfig['increment_auc_enable']) {
            $fieldset->addField(
                'increment_opt',
                'select',
                [
                    'name' => 'increment_opt',
                    'label' => __('Enable Increment Price'),
                    'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
                    'id' => 'increment_opt',
                    'title' => __('Enable Increment Price'),
                    'class' => 'required-entry',
                    'note' => __('Enable to use the price Increment in system settings'),
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            'auto_auction_opt',
            'select',
            [
                'name' => 'auto_auction_opt',
                'label' => __('Enable Auto Bid'),
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
                'id' => 'auto_auction_opt',
                'title' => __('Enable Auto Bid'),
                'class' => 'required-entry',
                'note' => __('Enable to turn on automatic auction.'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'buy_it_now',
            'select',
            [
                'name' => 'buy_it_now',
                'label' => __('Buy It Now'),
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
                'id' => 'buy_it_now',
                'title' => __('Buy It Now'),
                'class' => 'required-entry',
                'note' => __('Allows customers to purchase products without auction.'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'featured_auction',
            'select',
            [
                'name' => 'featured_auction',
                'label' => __('Is Featured'),
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
                'id' => 'buy_it_now',
                'title' => __('Is Featured'),
                'class' => 'required-entry',
                'note' => __('Enable to set this auction to featured.'),
                'required' => true,
            ]
        );
        if ($auctionConfig['restriction_enable']) {
            $fieldset->addField(
                'continue_time',
                'text',
                [
                    'name' => 'continue_time',
                    'label' => __('Continues Time (minutes)'),
                    'id' => 'continue_time',
                    'title' => __('Continues Time (minutes)'),
                    'class' => 'validate-zero-or-greater',
                    'note' => __('If 2 customer bids are less than this time period, it will be considered continuous. Empty to use module settings.'),
                    'required' => false,
                ]
            );
            $fieldset->addField(
                'limit_bids',
                'text',
                [
                    'name' => 'limit_bids',
                    'label' => __('Numbers of bids'),
                    'id' => 'limit_bids',
                    'title' => __('Numbers of bids'),
                    'class' => 'validate-zero-or-greater',
                    'note' => __('Set Numbers of bids that customers can create in a short time. Empty to use module settings.'),
                    'required' => false,
                ]
            );
            $fieldset->addField(
                'max_bids',
                'text',
                [
                    'name' => 'max_bids',
                    'label' => __('Max bids Can Place'),
                    'id' => 'max_bids',
                    'title' => __('Max bids Can Place'),
                    'class' => 'validate-zero-or-greater',
                    'note' => __('Max Bids which users can place for this auction.'),
                    'required' => false,
                ]
            );
        }
        if (!$model->getProductId()) {
            // add dependence javascript block
            $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
            $this->setChild('form_after', $dependenceBlock);
            $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');
        }

        $data = $model->getData();
        if ($data) {
            $data['stop_auction_time'] = $this->timezone->date(new \DateTime($data['stop_auction_time']))->format('m/d/y H:i:s');
            $data['start_auction_time'] = $this->timezone->date(new \DateTime($data['start_auction_time']))->format('m/d/y H:i:s');

            if (isset($data['entity_id']) && $data['entity_id']) {
                $data['auction_entity_id'] = $data['entity_id'];
            }
            if (isset($statusNumber)) {
                $data['auction_status'] = $this->helper->getAuctionStatus($statusNumber);
            }
            $form->setValues($data);
        }
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

    /**
     * convertToTz convert Datetime from one zone to another
     * @param string $dateTime which we want to convert
     * @param string $toTz timezone in which we want to convert
     * @param string $fromTz timezone from which we want to convert
     * @return string
     * @throws Exception
     */
    protected function convertToTz($dateTime = "", $toTz = '', $fromTz = '')
    {
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }
}
