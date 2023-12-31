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

class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        // @phpstan-ignore-next-line
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);
        $active = $this->helper->getStoreConfig('lofmp_multishipping/general/activation') ? 1 : 0;
        if ($websitecode = $this->getRequest()->getParam('website')) {
            $website = $this->_objectManager->get(\Magento\Store\Model\Website::class)->load($websitecode);
            if ($website && $website->getWebsiteId()) {
                $active = $website->getConfig('lofmp_multishipping/general/activation') ? 1 : 0;
            }
        }

        $validation = $active ? 0 : 1;
        foreach ($element->getElements() as $field) {
            if ($field instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
                $html .= '<tr id="row_' . $field->getHtmlId() . '"><td colspan="4">' . $field->toHtml() . '</td></tr>';
            } else {
                $html .= $field->toHtml();
            }
        }

        $html .= $this->_getFooterHtml($element);
        $html .= '<script>
        		var enable=0;

				if(' . $validation . '){
					document.getElementById("' . $element->getHtmlId() . '").style.display="none";
					document.getElementById("' . $element->getHtmlId() . '-state").previousElementSibling.style.display="none";
					document.getElementById("' . $element->getHtmlId() . '-state").style.display="none";
				}
				</script>';
        return $html;
    }
}
