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

namespace Lof\MarketPlace\Model;

class Widget extends \Magento\Widget\Model\Widget
{
    /**
     * @param array $filters
     * @return array
     */
    public function getWidgets($filters = [])
    {
        $core_widgets = parent::getWidgets($filters);
        $result = $core_widgets;
        $widgets = [];

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0 && $widgets) {
            foreach ($widgets as $code => $widget) {
                try {
                    foreach ($filters as $field => $value) {
                        if (!isset($widget[$field]) || (string)$widget[$field] != $value) {
                            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
                            throw new \Exception();
                        }
                    }
                    // phpcs:disable Magento2.Exceptions.ThrowCatch.ThrowCatch
                } catch (\Exception $e) {
                    unset($result[$code]);
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     * @api
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getWidgetDeclaration($type, $params = [], $asIs = true)
    {
        $fieldPattern = [
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
        $widgetTypes = ["Lof\BaseWidget\Block\Widget\Accordionbg"];

        /**
         * @SuppressWarnings('unused')
         */
        foreach ($params as $k => $value) {
            // phpcs:disable Magento2.PHP.ReturnValueCheck.ImproperValueTesting
            if (strpos($k, 'class') > 0 || strpos($k, 'Class') > 0) {
                continue;
            }
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            if (is_array($params[$k]) || !base64_decode($params[$k], true)) {
                if (in_array($k, $fieldPattern)
                    || preg_match("/^tabs(.*)/", $k)
                    || preg_match("/^content_(.*)/", $k)
                    || (preg_match("/^header_(.*)/", $k) && in_array($type, $widgetTypes))
                    || (preg_match("/^html_(.*)/", $k) && in_array($type, $widgetTypes))
                ) {
                    if (is_array($params[$k])) {
                        // phpcs:disable Magento2.Security.InsecureFunction.FoundWithAlternative
                        $params[$k] = base64_encode(serialize($params[$k]));
                        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                    } elseif (!base64_decode($params[$k], true)) {
                        $params[$k] = base64_encode($params[$k]);
                    }
                }
            }
        }

        return parent::getWidgetDeclaration($type, $params, $asIs);
    }
}
