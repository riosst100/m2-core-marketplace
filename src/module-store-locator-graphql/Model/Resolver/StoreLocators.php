<?php
declare(strict_types=1);

namespace CoreMarketplace\StoreLocatorGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lofmp\StoreLocator\Model\StoreLocatorFactory;

class StoreLocators implements ResolverInterface
{
    /**
     * @var StoreLocatorFactory
     */
    private StoreLocatorFactory $storeLocatorFactory;

    /**
     * @param StoreLocatorFactory $storeLocatorFactory
     */
    public function __construct(
        StoreLocatorFactory $storeLocatorFactory
    ) {
        $this->storeLocatorFactory = $storeLocatorFactory;
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

        $seller = $value['model'];
        if ($seller) {
            $collection = $this->storeLocatorFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $seller->getId());
            if ($collection->getSize()) {
                return $collection->getData();
            }
        }

        return [];
    }
}
