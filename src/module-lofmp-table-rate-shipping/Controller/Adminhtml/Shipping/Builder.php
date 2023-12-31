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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Controller\Adminhtml\Shipping;

use Lofmp\TableRateShipping\Model\ShippingFactory;
use Magento\Framework\App\RequestInterface;

class Builder
{
    /**
     * @var \Lofmp\TableRateShipping\Model\ShippingFactory
     */
    protected $_shippingFactory;

    /**
     * @param ShippingFactory $shippingFactory
     */
    public function __construct(
        ShippingFactory $shippingFactory
    ) {
        $this->_shippingFactory = $shippingFactory;
    }

    /**
     * @param RequestInterface $request
     * @return \Lofmp\TableRateShipping\Model\Shipping
     */
    public function build(RequestInterface $request)
    {
        $rowId = (int)$request->getParam('id');
        $shipping = $this->_shippingFactory->create();
        if ($rowId) {
            try {
                $shipping->load($rowId);
            } catch (\Exception $e) {
                return $shipping;
            }
        }
        return $shipping;
    }
}
