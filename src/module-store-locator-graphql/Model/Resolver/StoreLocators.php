<?php
declare(strict_types=1);

namespace CoreMarketplace\StoreLocatorGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lofmp\StoreLocator\Model\StoreLocatorFactory;
use Lof\MarketPlace\Helper\Data;

class StoreLocators implements ResolverInterface
{
    /**
     * @var StoreLocatorFactory
     */
    private StoreLocatorFactory $storeLocatorFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param StoreLocatorFactory $storeLocatorFactory
     * @param Data $helper
     */
    public function __construct(
        StoreLocatorFactory $storeLocatorFactory,
        Data $helper
    ) {
        $this->storeLocatorFactory = $storeLocatorFactory;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $data = [];

        $seller = $value['model'];
        if ($seller) {
            $collection = $this->storeLocatorFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $seller->getId());
            if ($collection->getSize()) {
                foreach($collection as $storeLocator) {
                    $storeLocatorData = $storeLocator->getData();
                    $storeLocatorData['country'] = $this->helper->getCountryName($storeLocator->getCountry());
                    $data[] = $storeLocatorData;
                }
            }
        }

        return $data;
    }
}
