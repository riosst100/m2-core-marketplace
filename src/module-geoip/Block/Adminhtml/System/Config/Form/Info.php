<?php

namespace CoreMarketplace\GeoIp\Block\Adminhtml\System\Config\Form;

/**
 * Admin geoip configurations information block
 */
class Info extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * Info constructor.
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList = $moduleList;
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $useUrl = \Magefan\Community\Model\UrlChecker::showUrl($this->getUrl());
        $version = '1.0.0';
        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            ' . $this->escapeHtml($this->getModuleTitle()) . ' v' . $this->escapeHtml($version) . ' was developed by ';
        if ($useUrl) {
            $html .= '<a href="' . $this->escapeHtml($this->getModuleUrl()) . '" target="_blank">Magefan</a>';
        } else {
            $html .= '<strong>Magefan</strong>';
        }
        $html .= '.</div>';

        return $html;
    }

    /**
     * Return extension url
     * @return string
     */
    protected function getModuleUrl()
    {
        return 'https://mage' . 'fan.com?utm_source=m2admin_geo_ip_config&utm_medium=link&utm_campaign=regular';
    }

    /**
     * Return extension title
     * @return string
     */
    protected function getModuleTitle()
    {
        return 'GeoIp Database Extension';
    }
}
