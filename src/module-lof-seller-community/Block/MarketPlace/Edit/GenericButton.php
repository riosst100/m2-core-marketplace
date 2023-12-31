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
 * @package    Lof_SellerCommunity
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\SellerCommunity\Block\MarketPlace\Edit;

use Magento\Backend\Block\Widget\Context;;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var string
     */
    protected $fieldId = 'id';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * construct
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {

        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getObjectId()
    {
        return $this->context->getRequest()->getParam($this->fieldId);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
