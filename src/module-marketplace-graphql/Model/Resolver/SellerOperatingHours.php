<?php
declare(strict_types=1);

namespace CoreMarketplace\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;

class SellerOperatingHours implements ResolverInterface
{
    /**
     * @var SellerCollection
     */
    private SellerCollection $sellerCollectionFactory;

    /**
     * @param SellerCollection $sellerCollectionFactory
     */
    public function __construct(
        SellerCollection $sellerCollectionFactory
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
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
            $operatingHours = $seller && $seller->getOperatingHours() ? json_decode($seller->getOperatingHours(),true) : null;
            if ($operatingHours) {
                foreach($operatingHours as $day => $operatingHour) {
                    $formattedOperatingHours[] = [
                        'day' => $this->getDayName($day),
                        'time' => isset($operatingHour['time']) && $operatingHour['time'] ? $operatingHour['time'] : []
                    ];
                }
            }
        }

        return $formattedOperatingHours;
    }

    public function getDayName($day) 
    {
        $label = '';

        if ($day == "mon") {
            $label = __('Monday');
        } elseif ($day == "tue") {
            $label = __('Tuesday');
        } elseif ($day == "wed") {
            $label = __('Wednesday');
        } elseif ($day == "thu") {
            $label = __('Thursday');
        } elseif ($day == "fri") {
            $label = __('Friday');
        } elseif ($day == "sat") {
            $label = __('Saturday');
        } elseif ($day == "sun") {
            $label = __('Sunday');
        }

        return $label;
    }
}
