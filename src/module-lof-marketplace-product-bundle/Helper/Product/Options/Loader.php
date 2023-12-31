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
 * @package    Lof_MarketProductBundle
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\MarketProductBundle\Helper\Product\Options;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\Data\OptionInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class Loader
 */
class Loader extends \Magento\ConfigurableProduct\Helper\Product\Options\Loader
{
    /**
     * @var OptionValueInterfaceFactory
     */
    private $optionValueFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * ReadHandler constructor
     *
     * @param OptionValueInterfaceFactory $optionValueFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        OptionValueInterfaceFactory $optionValueFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->optionValueFactory = $optionValueFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * @param ProductInterface $product
     * @return OptionInterface[]
     */
    public function load(ProductInterface $product)
    {
        $options = [];
        /** @var Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();

        $array = get_class_methods($typeInstance);


        if(array_search("getConfigurableAttributeCollection",$array)){


            $attributeCollection = $typeInstance->getConfigurableAttributeCollection($product);
            $this->extensionAttributesJoinProcessor->process($attributeCollection);
            foreach ($attributeCollection as $attribute) {
                $values = [];
                $attributeOptions = $attribute->getOptions();
                if (is_array($attributeOptions)) {
                    foreach ($attributeOptions as $option) {
                        /** @var \Magento\ConfigurableProduct\Api\Data\OptionValueInterface $value */
                        $value = $this->optionValueFactory->create();
                        $value->setValueIndex($option['value_index']);
                        $values[] = $value;
                    }
                }
                $attribute->setValues($values);
                $options[] = $attribute;
            }
        }
         return $options;
    }
}
