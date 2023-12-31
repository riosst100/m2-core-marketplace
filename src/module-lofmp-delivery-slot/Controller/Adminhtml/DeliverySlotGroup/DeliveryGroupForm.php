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
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlotGroup;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

class DeliveryGroupForm extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';
    
    /**
     * DeliveryGroupForm constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ForwardFactory $forwardFactory
    ) {
    
        parent::__construct($context);
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}
