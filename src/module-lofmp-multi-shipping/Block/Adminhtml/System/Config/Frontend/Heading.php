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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Block\Adminhtml\System\Config\Frontend;

use Magento\Backend\Block\Context;

class Heading extends \Magento\Config\Block\System\Config\Form\Field\Heading
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Heading constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_request = $request;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $active = 1;
        if ($websitecode = $this->_request->getParam('website')) {
            $website = $this->_objectManager->get(\Magento\Store\Model\Website::class)->load($websitecode);
            if ($website && $website->getWebsiteId()) {
                $active = $website->getConfig('lofmp_multishipping/general/activation') ? 1 : 0;
            }
        } else {
            // @phpstan-ignore-next-line
            $active = $this->_objectManager->get(\Lof\MarketPlace\Helper\Data::class)
                ->getStoreConfig('lofmp_multishipping/general/activation') ? 1 : 0;
        }

        $methods = $this->_objectManager->get(\Lofmp\MultiShipping\Model\Source\Shipping\Methods::class)->getMethods();
        $count = 0;
        if (count($methods) > 0) {
            $count = 1;
        }
        $validation = $active && $count ? 0 : 1;
        $html = '';
        $html .= sprintf(
            '<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
            $element->getHtmlId(),
            $element->getHtmlId(),
            $element->getLabel()
        );
        $html .= '<script>
				if(' . $validation . '){
					document.getElementById("row_' . $element->getHtmlId() . '").style.display="none";
				}
				</script>';
        return $html;
    }
}
