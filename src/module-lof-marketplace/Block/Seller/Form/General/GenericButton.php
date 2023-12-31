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
namespace Lof\MarketPlace\Block\Seller\Form\General;


use Magento\Backend\Block\Widget\Context;;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var string
     */
    protected $_modelId = "entity_id";

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * get id
     * 
     * @return string|int|mixed|null
     */
    public function getId()
    {
        $modelId = $this->registry->registry($this->_modelId);
        return $modelId ? $modelId : null;
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
