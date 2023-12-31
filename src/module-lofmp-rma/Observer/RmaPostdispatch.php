<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;

class RmaPostdispatch implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * RmaPostdispatch constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getRequest();
        /** @var \Magento\Backend\Model\Session\Quote $session */
        $session = $this->objectManager->get('Magento\Backend\Model\Session\Quote');
        if ($request->getFullActionName() == 'sales_order_create_start' && (int)$request->getParam('rma_id')) {
            $session->setRmaId($request->getParam('rma_id'));
        } else {
            $session->unsetRmaId($request->getParam('rma_id'));
        }
    }
}
