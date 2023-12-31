<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Model;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreFactory;

class QuickResponse extends AbstractModel
{
    public function __construct(
        StoreFactory $storeFactory,
        \Lofmp\Rma\Helper\Message $messageHelper,
        ScopeConfigInterface $scopeConfig,
        Auth $auth,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeFactory         = $storeFactory;
        $this->context              = $context;
        $this->messageHelper        = $messageHelper;
        $this->scopeConfig          = $scopeConfig;
        $this->auth                 = $auth;
        $this->registry             = $registry;
        $this->resource             = $resource;
        $this->resourceCollection   = $resourceCollection;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\QuickResponse');
    }

    /**
     * To option array
     *
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * Parse template
     *
     * @param Rma $rma
     * @return string
     */
    public function getParsedTemplate($rma)
    {
        $storeId = $rma->getStoreId();
        $storeOb = $this->storeFactory->create()->load($storeId);
        if (!$name = $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        )
        ) {
            $name = $storeOb->getName();
        }
        $store = new DataObject([
            'name'    => $name,
            'phone'   => $this->scopeConfig->getValue(
                'general/store_information/phone',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $storeId
            ),
            'address' => $this->scopeConfig->getValue(
                'general/store_information/address',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $storeId
            ),
        ]);
        $user = $this->auth->getUser();

        $result = $this->messageHelper->parse(
            $this->getTemplate(),
            [
                'rma'   => $rma,
                'store' => $store,
                'user'  => $user,
            ],
            [],
            $store->getId()
        );

        return $result;
    }
}
