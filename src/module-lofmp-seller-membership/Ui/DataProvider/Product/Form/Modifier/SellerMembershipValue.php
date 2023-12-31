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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\SellerMembership\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

//use Lofmp\SellerMembership\Model\Config\Source\Type as CreditType;

/**
 * Data provider for categories field of product page
 */
class SellerMembershipValue extends AbstractModifier
{

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];
        /**
         * @var \Magento\Catalog\Model\Config\Source\Product\Options\Price
         */
    protected $productOptionsPrice;

    /**
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ProductOptionsPrice $productOptionsPrice,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->dbHelper = $dbHelper;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->productOptionsPrice = $productOptionsPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->customizeCreditDropdownValueField();
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        return $data;
    }

    /**
     * Customize credit dropdown value field
     *
     * @return $this
     */
    protected function customizeCreditDropdownValueField()
    {
        $fieldCode = 'seller_duration';
        $durationPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );

        if ($durationPath) {
            $this->meta = $this->arrayManager->merge(
                $durationPath,
                $this->meta,
                $this->getDurationStructure($durationPath)
            );

            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($durationPath, 0, -3)
                . '/' . $fieldCode,
                $this->meta,
                $this->arrayManager->get($durationPath, $this->meta)
            );

            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($durationPath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }
    /**
     * Get credit dropdown struct
     *
     * @param string $fieldPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getDurationStructure($fieldPath)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'container',
                        'component' => 'Lofmp_SellerMembership/js/components/dynamic-rows',
                        'template' => 'ui/dynamic-rows/templates/default',
                        'label' => __('Duration'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'sortOrder' =>
                        $this->arrayManager->get($fieldPath . '/arguments/data/config/sortOrder', $this->meta),

                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'duration' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Duration'),
                                        'dataScope' => 'membership_duration',
                                        'validation' => [
                                            'validate-zero-or-greater' => true,
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'unit' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Duration Unit'),
                                        'componentType' => Field::NAME,
                                        'formElement' => Select::NAME,
                                        'dataScope' => 'membership_unit',
                                        'dataType' => Text::NAME,
                                        'options' => [
                                            [
                                                'value' => 'day',
                                                'label' => 'Day'
                                            ],
                                            [
                                                'value' => 'week',
                                                'label' => 'Week'
                                            ],
                                            [
                                                'value' => 'month',
                                                'label' => 'Month'
                                            ],
                                            [
                                                'value' => 'year',
                                                'label' => 'Year'
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'price' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => Price::NAME,
                                        'label' => __('Price'),
                                        'enableLabel' => true,
                                        'dataScope' => 'membership_price',
                                        'addbefore' => $this->locator->getStore()
                                            ->getBaseCurrency()
                                            ->getCurrencySymbol(),
                                        'validation' => [
                                            'validate-zero-or-greater' => true,
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'order' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Sort Order'),
                                        'dataScope' => 'membership_order',
                                        'validation' => [
                                            'validate-zero-or-greater' => true,
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
