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


namespace Lof\SellerCommunity\Block\Adminhtml\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\AuthorizationInterface;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var string
     */
    protected $fieldId = 'id';

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param AuthorizationInterface|null $authorization
     */
    public function __construct(
        Context $context,
        $authorization = null
    ) {
        $this->context = $context;
        $this->authorization = $authorization
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\AuthorizationInterface::class
            );
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
