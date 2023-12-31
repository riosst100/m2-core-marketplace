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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    /**
     * @var \Lofmp\SellerRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Lofmp\SellerRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Lofmp\SellerRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Lofmp\SellerRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Lofmp\SellerRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Lofmp\SellerRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * @return \Lofmp\SellerRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }
}
