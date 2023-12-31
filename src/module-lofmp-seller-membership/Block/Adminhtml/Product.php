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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Container
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Model\Product\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;

        parent::__construct($context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $typeId = 'seller_membership';
        $addButtonProps = [
            'id' => 'add_new_product',
            'label' => __('Add Membership Product'),
            'class' => 'add primary',
            'button_class' => '',
            'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl(
            'catalog/*/new',
            ['set' => $this->_productFactory->create()->getDefaultAttributeSetId(), 'type' => $type]
        );
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
