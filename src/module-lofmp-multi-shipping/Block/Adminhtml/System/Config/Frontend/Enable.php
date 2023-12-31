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

class Enable extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Enable constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // @phpstan-ignore-next-line
        $active = $this->_objectManager->get(\Lof\MarketPlace\Helper\Data::class)
                ->getStoreConfig('lofmp_multishipping/general/activation') ? 0 : 1;
        if ($websitecode = $this->getRequest()->getParam('website')) {
            $website = $this->_objectManager->get(\Magento\Store\Model\Website::class)->load($websitecode);
            if ($website && $website->getWebsiteId()) {
                $active = $website->getConfig('lofmp_multishipping/general/activation') ? 0 : 1;
            }
        }
        $html = '';
        $html .= $element->getElementHtml();
        $html .= '<script>
				if(' . $active . '){
					document.getElementById("row_' . $element->getHtmlId() . '").style.display="none";
				}
				</script>';
        return $html;
    }
}
