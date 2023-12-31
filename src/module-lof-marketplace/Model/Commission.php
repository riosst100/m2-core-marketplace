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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Commission extends \Magento\Rule\Model\AbstractModel
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const COMMISSION_BY_FIXED_AMOUNT = 'by_fixed';
    const COMMISSION_BY_PERCENT_PRODUCT_PRICE = 'by_percent';
    const COMMISSION_BASED_PRICE_INCL_TAX = 'by_price_incl_tax';
    const COMMISSION_BASED_PRICE_EXCL_TAX = 'by_price_excl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX = 'by_price_after_discount_incl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX = 'by_price_after_discount_excl_tax';

    /**
     * @var Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var Lof\MarketPlace\Model\Commission\Rule\Action\CollectionFactory
     */
    protected $_condProdCombineF;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_commission';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'commission';

    /**
     * Commission constructor.
     *
     * @param Rule\Condition\CombineFactory $condCombineFactory
     * @param Rule\CombineFactory $condProdCombineF
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Lof\MarketPlace\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Lof\MarketPlace\Model\Rule\CombineFactory $condProdCombineF,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        //$this->_resource = $resource;
        $this->_formFactory = $formFactory;
        $this->_localeDate = $localeDate;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->_combineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Commission::class);
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * @return Rule\Condition\Combine|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        $combine = $this->_combineFactory->create();
        return $combine;
    }

    /**
     * @return Rule\Combine|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }
}
