<?php

namespace CoreMarketplace\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CurrencySymbolProvider;

class TermAndConditions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var ModuleManager
     * @since 101.0.0
     */
    protected $moduleManager;

    /**
     * @var GroupManagementInterface
     * @since 101.0.0
     */
    protected $groupManagement;

    /**
     * @var SearchCriteriaBuilder
     * @since 101.0.0
     */
    protected $searchCriteriaBuilder;

    /**
     * @var GroupRepositoryInterface
     * @since 101.0.0
     */
    protected $groupRepository;

    /**
     * @var Data
     * @since 101.0.0
     */
    protected $directoryHelper;

    /**
     * @var StoreManagerInterface
     * @since 101.0.0
     */
    protected $storeManager;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    /**
     * @var string
     * @since 101.0.0
     */
    protected $scopeName;

    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * @var GroupSourceInterface
     */
    private $customerGroupSource;

    /**
     * @var CurrencySymbolProvider
     */
    private $currencySymbolProvider;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupManagementInterface $groupManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ModuleManager $moduleManager
     * @param Data $directoryHelper
     * @param ArrayManager $arrayManager
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param string $scopeName
     * @param GroupSourceInterface|null $customerGroupSource
     * @param CurrencySymbolProvider|null $currencySymbolProvider
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        GroupRepositoryInterface $groupRepository,
        GroupManagementInterface $groupManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ModuleManager $moduleManager,
        Data $directoryHelper,
        ArrayManager $arrayManager,
        \Lof\MarketPlace\Helper\Data $helper,
        $scopeName = '',
        GroupSourceInterface $customerGroupSource = null,
        ?CurrencySymbolProvider $currencySymbolProvider = null
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->groupManagement = $groupManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
        $this->helper = $helper;
        $this->customerGroupSource = $customerGroupSource
            ?: ObjectManager::getInstance()->get(GroupSourceInterface::class);
        $this->currencySymbolProvider = $currencySymbolProvider
            ?: ObjectManager::getInstance()->get(CurrencySymbolProvider::class);
    }

    /**
     * @inheritdoc
     *
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->addDefaultValue();

        return $this->meta;
    }

    /**
     * @inheritdoc
     *
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Customize field.
     *
     * @return $this
     */
    private function addDefaultValue()
    {
        $product = $this->locator->getProduct();
        if (!$product->getTermAndConditions()) 
        {
            $seller = $this->helper->getCurrentSeller();
            if ($seller && $seller->getTermAndConditions()) {
                $pathTnc = $this->arrayManager->findPath('term_and_conditions', $this->meta, null, 'children');
                if ($pathTnc) {
                    $this->meta = $this->arrayManager->merge(
                        $pathTnc . '/arguments/data/config',
                        $this->meta,
                        [
                            'value' => $seller->getTermAndConditions()
                        ]
                    );
                }
            }
        }

        return $this;
    }
}
