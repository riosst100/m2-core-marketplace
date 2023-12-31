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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Adminhtml\Widget;

class BuildWidget extends \Magento\Widget\Controller\Adminhtml\Widget\BuildWidget
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $_serializer;

    /**
     * BuildWidget constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Widget\Model\Widget $widget,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->_serializer = $serializer;
        parent::__construct($context, $widget);
    }

    /**
     * Format widget pseudo-code for inserting into wysiwyg editor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $type = $this->getRequest()->getPost('widget_type');
        $params = $this->getRequest()->getPost('parameters', []);

        $field_pattern = [
            "pretext",
            "pretext_html",
            "shortcode",
            "html",
            "raw_html",
            "content",
            "tabs",
            "latestmod_desc",
            "custom_css",
            "block_params"
        ];
        // phpcs:disable Magento2.PHP.LiteralNamespaces.LiteralClassUsage
        $widget_types = ["Lof\BaseWidget\Block\Widget\Accordionbg"];

        foreach ($params as $k => $v) {
            // phpcs:disable Magento2.PHP.ReturnValueCheck.ImproperValueTesting
            if (strpos($k, 'class') > 0 || strpos($k, 'Class') > 0 || $k == "contentclass") {
                continue;
            }

            if (is_array($params[$k]) || !$this->isBase64Encoded($params[$k])) {
                if (in_array($k, $field_pattern)
                    || preg_match("/^tabs(.*)/", $k)
                    || preg_match("/^content_(.*)/", $k)
                    || (preg_match("/^header_(.*)/", $k) && in_array($type, $widget_types))
                ) {
                    if (is_array($params[$k])) {
                        $params[$k] = base64_encode($this->_serializer->serialize($params[$k]));
                    } elseif (!$this->isBase64Encoded($params[$k])) {
                        $params[$k] = base64_encode($params[$k]);
                    }
                }
            }
        }

        $asIs = $this->getRequest()->getPost('as_is');
        $html = $this->_widget->getWidgetDeclaration($type, $params, $asIs);
        $this->getResponse()->setBody($html);
    }

    /**
     * @param $data
     * @return bool
     */
    public function isBase64Encoded($data)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (base64_encode(base64_decode($data)) === $data) {
            return true;
        }

        return false;
    }
}
