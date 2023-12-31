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

namespace Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class NewSlot
 * @package Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot
 */
class NewSlot extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    /**
     * NewSlot constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $forwardFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
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
