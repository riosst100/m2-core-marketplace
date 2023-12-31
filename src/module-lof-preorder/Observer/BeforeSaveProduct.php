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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class BeforeSaveProduct implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @param RequestInterface $request
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     */
    public function __construct(
        RequestInterface $request,
        \Lof\PreOrder\Helper\Data $preorderHelper
    ) {
        $this->_request = $request;
        $this->_preorderHelper = $preorderHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_preorderHelper;
        $productId = 0;
        $preorderProductId = $helper->getPreorderCompleteProductId();
        $data = $this->_request->getParams();
        if (array_key_exists('id', $data)) {
            $productId = $data['id'];
        }
        if (!array_key_exists('is_admin', $data)) { // in case update product
            if ($productId == $preorderProductId) {
                $error = "You can not update 'Complete PreOrder' Product";
                throw new \Magento\Framework\Validator\Exception(__($error));
            }
        }
    }
}
