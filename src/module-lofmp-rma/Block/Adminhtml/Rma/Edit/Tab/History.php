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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab;

class History extends \Magento\Backend\Block\Template
{
    /**
     * History constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return \Lofmp\Rma\Model\Rma
     */
    public function getCurrentRma()
    {
        if ($this->registry->registry('current_rma') && $this->registry->registry('current_rma')->getId()) {
            return $this->registry->registry('current_rma');
        }
    }

    /**
     * @param $Uid
     */
    public function getAttachmentUrl($Uid)
    {
        $this->context->getUrlBuilder()->getUrl('rma/attachment/download', ['uid' => $Uid]);
    }

    /**
     * @param bool $isRead
     * @return string
     */
    public function getMarkUrl($isRead)
    {
        return $this->getUrl('*/*/markRead', ['rma_id' => $this->getRma()->getId(), 'is_read' => (int)$isRead]);
    }
}
