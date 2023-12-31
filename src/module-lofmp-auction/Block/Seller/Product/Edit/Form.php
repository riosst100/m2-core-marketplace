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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Block\Seller\Product\Edit;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ResourceModel\Product\Source\AllProductsForAuction;
use Lof\MarketPlace\Block\Seller\Widget\Form\Generic;
use Lofmp\Auction\Block\Seller\Product\Chooser;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\System\Store;

/**
 * Class Form
 * Lofmp\Auction\Block\Seller\Product\Edit
 */
class Form extends Generic
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var
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
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketHelper;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param AllProductsForAuction $allProducts
     * @param Data $helper
     * @param \Lof\MarketPlace\Helper\Data $marketHelper
     * @param TimezoneInterface $timezone
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        AllProductsForAuction $allProducts,
        Data $helper,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        TimezoneInterface $timezone,
        Session $customerSession,
        array $data = []
    ) {
        $this->marketHelper = $marketHelper;
        $this->helper = $helper;
        $this->_allProducts = $allProducts;
        $this->_systemStore = $systemStore;
        $this->timezone = $timezone;
        $this->customerSession = $customerSession;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofauction_product');
        $auctionConfig = $this->helper->getAuctionConfiguration();
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('Product Information'), 'class' => 'fieldset-wide fieldset-widget-options']
        );
        $form->addFieldset(
            'history_fieldset',
            ['legend' => __('Bids History'), 'class' => 'fieldset-wide fieldset-widget-options']
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
            $chooserField->setValue($model->getProductId());
            $helperBlock = $this->getLayout()->createBlock(
                Chooser::class,
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
                    'text' => "<a href=" . $product->getProductUrl() . " target='_blank'>"
                        . $product->getName() . "</a>"
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
                    'class' => 'lofau-status-box lofau-status-' . $className
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
                'note' => __('The price stipulated as the lowest acceptable by the seller for an item sold at auction.
                 If the closing price is lower than Reserve Price,
                 the winner must buy the product with the cost is Reserve Price.'),
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
        }
        if (!$model->getProductId()) {
            // add dependence javascript block
            $dependenceBlock = $this->getLayout()
                ->createBlock(Dependence::class);
            $this->setChild('form_after', $dependenceBlock);
            $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');
        }

        if ($this->customerSession->getFormData()) {
            $data = $this->customerSession->getFormData();
        } else {
            $data = $model->getData();
        }
        if ($data) {
            $data['stop_auction_time'] = $this->timezone->date(
                new \DateTime($data['stop_auction_time'])
            )->format('m/d/y H:i:s');
            $data['start_auction_time'] = $this->timezone->date(
                new \DateTime($data['start_auction_time'])
            )->format('m/d/y H:i:s');

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
}
