<?php

namespace CoreMarketplace\SellerMembership\Ui\DataProvider\Product\Form\Modifier;

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
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CurrencySymbolProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Helper\Data;

//use Lofmp\SellerMembership\Model\Config\Source\Type as CreditType;

/**
 * Data provider for categories field of product page
 */
class SellerMembershipValue extends \Lofmp\SellerMembership\Ui\DataProvider\Product\Form\Modifier\SellerMembershipValue
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
     * @var CurrencySymbolProvider
     */
    private $currencySymbolProvider;

    /**
     * @var StoreManagerInterface
     * @since 101.0.0
     */
    protected $storeManager;

    /**
     * @var Data
     * @since 101.0.0
     */
    protected $directoryHelper;

    /**
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param StoreManagerInterface $storeManager
     * @param Data $directoryHelper
     * @param CurrencySymbolProvider|null $currencySymbolProvider
     */
    public function __construct(
        LocatorInterface $locator,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ProductOptionsPrice $productOptionsPrice,
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        Data $directoryHelper,
        ?CurrencySymbolProvider $currencySymbolProvider = null
    ) {
        $this->locator = $locator;
        $this->dbHelper = $dbHelper;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->productOptionsPrice = $productOptionsPrice;
        $this->storeManager = $storeManager;
        $this->directoryHelper = $directoryHelper;
        $this->currencySymbolProvider = $currencySymbolProvider
        ?: ObjectManager::getInstance()->get(CurrencySymbolProvider::class);

        parent::__construct(
            $locator,
            $dbHelper,
            $urlBuilder,
            $productOptionsPrice,
            $arrayManager
        );
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
     * Get websites list
     *
     * @return array
     */
    private function getWebsites()
    {
        $websites = [
            [
                'label' => __('All Websites') . ' [' . $this->directoryHelper->getBaseCurrencyCode() . ']',
                'value' => 0,
            ]
        ];
        $product = $this->locator->getProduct();

        if (!$this->isScopeGlobal() && $product->getStoreId()) {
            /** @var \Magento\Store\Model\Website $website */
            $website = $this->getStore()->getWebsite();

            $websites[] = [
                'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                'value' => $website->getId(),
            ];
        } elseif (!$this->isScopeGlobal()) {
            $websitesList = $this->storeManager->getWebsites();
            $productWebsiteIds = $product->getWebsiteIds();
            foreach ($websitesList as $website) {
                /** @var \Magento\Store\Model\Website $website */
                if (!in_array($website->getId(), $productWebsiteIds)) {
                    continue;
                }
                $websites[] = [
                    'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                    'value' => $website->getId(),
                ];
            }
        }

        return $websites;
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     * @since 101.0.0
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return $this->storeManager->getStore($this->locator->getProduct()->getStoreId())->getWebsiteId();
        }

        return 0;
    }

    /**
     * Show website column and switcher for group price table
     *
     * @return bool
     */
    private function isMultiWebsites()
    {
        return !$this->storeManager->isSingleStoreMode();
    }

    /**
     * Show group prices grid website column
     *
     * @return bool
     */
    private function isShowWebsiteColumn()
    {
        if ($this->isScopeGlobal() || $this->storeManager->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * Check tier_price attribute scope is global
     *
     * @return bool
     */
    private function isScopeGlobal()
    {
        return $this->locator->getProduct()
            ->getResource()
            ->getAttribute('seller_duration')
            ->isScopeGlobal();
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    private function isAllowChangeWebsite()
    {
        if (!$this->isShowWebsiteColumn() || $this->locator->getProduct()->getStoreId()) {
            return false;
        }
        return true;
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
                        'website_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'component' => 'Magento_Catalog/js/components/website-currency-symbol',
                                        'dataType' => Text::NAME,
                                        'formElement' => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'website_id',
                                        'label' => __('Website'),
                                        'options' => $this->getWebsites(),
                                        'value' => $this->getDefaultWebsite(),
                                        'visible' => $this->isMultiWebsites(),
                                        'disabled' => ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()),
                                        'sortOrder' => 10,
                                        'currenciesForWebsites' => $this->currencySymbolProvider
                                            ->getCurrenciesPerWebsite(),
                                        'currency' => $this->currencySymbolProvider
                                            ->getDefaultCurrency(),
                                    ],
                                ],
                            ],
                        ],
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
