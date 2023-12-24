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
 * @package    Lof_SmtpEmail
 *
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Block\Adminhtml\Report;

class Report extends \Magento\Framework\View\Element\Template
{


    /**
     * @var \Lof\SmtpEmail\Model\Emaillog
     */
    protected $_emaillog;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Lof\SmtpEmail\Model\Emaillog           $emaillogCollection
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\SmtpEmail\Model\Emaillog $emaillogCollection
    ) {
        $this->_emaillog = $emaillogCollection;
        parent::__construct($context);
    }

    /**
     * @return Lof\SmtpEmail\Model\ResourceModel\Emaillog\Collecion
     */
    public function getSumEmaillog()
    {
        $emaillog = $this->_emaillog->getCollection();
        return $emaillog;
    }

    public function getStatus() {
        return $this->_emaillog->getStatusEmail();
    }

}
