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
 * Class DeleteButton
 * @package Lof\MarketPlace\Block\Seller\Form\General
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var string
     */
    protected $_deleteUrl = "*/*/*/delete";

    /**
     * @var string
     */
    protected $_deleteLabel = "Delete Item";

    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __($this->_deleteLabel),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl($this->_deleteUrl, [$this->_modelId => $this->getId()]);
    }
}
