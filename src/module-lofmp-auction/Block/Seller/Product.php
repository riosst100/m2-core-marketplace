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

namespace Lofmp\Auction\Block\Seller;

use Lof\Auction\Helper\Data;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Product
 * Lofmp\Auction\Block\Seller
 */
class Product extends \Magento\Backend\Block\Widget\Container
{

    /**
     * @var TypeFactory
     */
    protected $_typeFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Data
     */
    protected $_productHelper;
    /**
     * @var string
     */
    private $_blockGroup;

    /**
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Lofmp_Auction';
        $this->_controller = 'seller_product';
        $this->_headerText = __('Manage Action Products');
        $this->_addButtonLabel = __('Add Action Product');
        parent::_construct();
    }

    /**
     * @param Context $context
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param Data $productHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        TypeFactory $typeFactory,
        ProductFactory $productFactory,
        Data $productHelper,
        array $data = []
    ) {
        $this->_productFactory  = $productFactory;
        $this->_typeFactory     = $typeFactory;
        $this->_productHelper   = $productHelper;

        parent::__construct($context, $data);
        $this->removeButton('add');
    }
}
