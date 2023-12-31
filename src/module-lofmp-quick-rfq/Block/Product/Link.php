<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lofmp\Quickrfq\Block\Product;

use Magento\Customer\Model\Session;
use Lof\Quickrfq\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Link
 *
 * @package Lofmp\Quickrfq\Block\Product
 */
class Link extends \Lof\Quickrfq\Block\Product\Link
{


    /**
     * @var
     */
    protected $_helperConfig;
    /**
     * @var \Lofmp\Quickrfq\Helper\Data
     */
    private $_helperSeller;

    /**
     * Link constructor.
     *
     * @param Context $context
     * @param Data $helperConfig
     * @param \Lofmp\Quickrfq\Helper\Data                      $helperSeller
     * @param Registry $registry
     * @param Session $session
     * @param array                                            $data
     */
    public function __construct(
        \Lofmp\Quickrfq\Helper\Data $helperSeller,
        Context $context,
        Data $helperConfig,
        Registry $registry,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $helperConfig, $registry, $data, $session);
        $this->_helperSeller    = $helperSeller;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $isEnable = $this->_helperSeller->getConfig('general/enabled');
        if ($this->getCurrentProduct()->getSellerId() && !$isEnable) {
            return '';
        } else {
            return parent::_toHtml();
        }
    }

    /**
     * @return array|mixed|null
     */
    public function getCurrentProduct()
    {
        if (! $this->hasData('current_product')) {
            $this->setData('current_product', $this->_coreRegistry->registry('current_product'));
        }

        return $this->getData('current_product');
    }
}
