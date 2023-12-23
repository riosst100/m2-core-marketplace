<?php

/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Paymentfee
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Paymentfee\Block\Sales;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template;
use Lof\Paymentfee\Helper\Data;

class Totals extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Totals constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }


    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $this->getSource();
        $storeId = $source->getStoreId();

        if ($source->getPaymentFee() == 0) {
            return $this;
        }

        $paymentFeeTitle = $this->helper->getTitle($storeId);

        $paymentFeeExclTax = $source->getPaymentFee();
        $basePaymentFeeExclTax = $source->getBasePaymentFee();
        $paymentFeeExclTaxTotal = [
            'code' => 'payment_fee',
            'strong' => false,
            'value' => $paymentFeeExclTax,
            'base_value' => $basePaymentFeeExclTax,
            'label' => $paymentFeeTitle,
        ];

        $paymentFeeInclTax = $paymentFeeExclTax + $source->getPaymentFeeTax();
        $basePaymentFeeInclTax = $basePaymentFeeExclTax + $source->getBasePaymentFeeTax();
        $paymentFeeInclTaxTotal = [
            'code' => 'payment_fee_incl_tax',
            'strong' => false,
            'value' => $paymentFeeInclTax,
            'base_value' => $basePaymentFeeInclTax,
            'label' => $paymentFeeTitle,
        ];

        if ($this->helper->displayExclTax($storeId) && $this->helper->displayInclTax($storeId)) {
            $inclTxt = __('Incl. Tax');
            $exclTxt = __('Excl. Tax');
            $paymentFeeInclTaxTotal['label'] .= ' ' . $inclTxt;
            $paymentFeeExclTaxTotal['label'] .= ' ' . $exclTxt;
        }

        if ($this->helper->displayExclTax($storeId)) {
            $parent->addTotal(
                $this->dataObjectFactory->create()->setData($paymentFeeExclTaxTotal),
                'shipping'
            );
        }

        if ($this->helper->displayInclTax($storeId)) {
            $parent->addTotal(
                $this->dataObjectFactory->create()->setData($paymentFeeInclTaxTotal),
                'shipping'
            );
        }

        return $this;
    }
}
