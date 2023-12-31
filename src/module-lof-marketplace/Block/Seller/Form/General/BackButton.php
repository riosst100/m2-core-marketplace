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
namespace Lof\MarketPlace\Block\Seller\Form\General;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lof\MarketPlace\Block\Seller\Form\General\GenericButton;

/**
 * Class BackButton
 * @package Lof\MarketPlace\Block\Seller\Form\General
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var string
     */
    protected $_backUrl = "catalog/dashboard/";

    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
    public function getBackUrl()
    {
        return $this->getUrl($this->_backUrl);
    }
}
