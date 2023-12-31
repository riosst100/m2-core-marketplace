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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

abstract class AbstractTab extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $registryKey = '';

    /**
     * AbstractTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getLabel();
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
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->registryKey;
    }

    /**
     * @param string $registryKey
     * @return $this
     */
    public function setRegistryKey($registryKey)
    {
        $this->registryKey = $registryKey;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->_coreRegistry->registry($this->getRegistryKey());
    }

    /**
     * @return \Magento\Framework\Phrase|string Tab label and title
     */
    abstract protected function getLabel();

    /**
     * Doing for possibility extend and additional new fields in tab form
     *
     * @param \Magento\Rule\Model\AbstractModel $model
     * @return \Magento\Framework\Data\Form $form
     */
    abstract protected function formInit($model);
}
