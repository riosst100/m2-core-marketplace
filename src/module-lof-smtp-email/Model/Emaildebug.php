<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Emaildebug extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Lof\SmtpEmail\Logger\Logger
     */
	protected $_logger;

    /**
     * @var \Lof\SmtpEmail\Helper\Data
     */
    protected $_helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DateTime
     */
    protected $coreDate;

    /**
     * Initialize resource model
     * @param \Magento\Framework\Model\Context $context
     * @param \Lof\SmtpEmail\Logger\Logger $logger
     * @param \Lof\SmtpEmail\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param ObjectManagerInterface $objectManager
     * @param DateTime $coreDate
     * @return void
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context,
    	\Lof\SmtpEmail\Logger\Logger $logger,
        \Lof\SmtpEmail\Helper\Data $helper,
    	\Magento\Framework\Registry $registry,
    	ObjectManagerInterface $objectManager,
    	DateTime $coreDate
    ) {
    	$this->_logger = $logger;
    	$this->objectManager = $objectManager;
    	$this->coreDate = $coreDate;
        $this->_helper = $helper;
    	parent::__construct($context,$registry);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Lof\SmtpEmail\Model\ResourceModel\Emaildebug');
    }

    /**
     * message debug
     *
     * @param mixed|string $message
     * @return void
     */
    public function messageDebug($message)
    {
        if($this->_helper->getConfig('general_settings/enable_email_debug') == 1) {
            $this->setData([
                'created_at'        => date('Y-m-d H:i:s'),
                'message'           => $message,
            ]);
            $this->save();
        }
    }

    /**
     * Clear debug
     *
     * @return void
     */
    public function clearDebug()
    {
        $keep_email = $this->_helper->getConfig('clear/debug');
        if($keep_email > 0) {
            $time = time() - $keep_email*24*60*60;
            $time = date('Y-m-d H:i:s',$time);
             $collection=$this->getCollection()->addFieldToFilter('created_at',['lt' => $time]);
            foreach ($collection as $key => $_collection) {
                $_collection->delete();
            }
        }
    }
}
