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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Block\Product;

use Magento\Customer\Model\Session;
use Lof\Quickrfq\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Link
 *
 * @package Lof\Quickrfq\Block\Product
 */
class Link extends AbstractView
{

    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * @var \Lof\Quickrfq\Helper\Data
     */
    protected $_helperConfig;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\Quickrfq\Helper\Data                        $helperConfig
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Customer\Model\Session                  $session
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Data $helperConfig,
        Registry $registry,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helperConfig, $data);
        $this->_helperConfig    = $helperConfig;
        $this->_customerSession = $session;
    }

    /**
     * @return mixed
     */
    public function isRequiredLoggedIn()
    {
        return $this->_helperConfig->getConfig('option/required_customer_login');
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        $url = $this->getUrl('*/*/*', [ '_current' => true, '_use_rewrite' => true ]);

        return $this->getUrl('customer/account/login', [ 'referer' => base64_encode($url) ]);
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        if (!$this->hasData('current_product')) {
            $this->setData('current_product', $this->_coreRegistry->registry('current_product'));
        }

        return $this->getData('current_product');
    }
}
