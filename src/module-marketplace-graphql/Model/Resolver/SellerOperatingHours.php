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
            foreach($this->getDayNames() as $index => $day) {
                $code = $day['code'];
                $formattedOperatingHours[] = [
                    'day' => $day['label'],
                    'status' => isset($operatingHours[$code]['status']) && $operatingHours[$code]['status'] ? $operatingHours[$code]['status'] : 'closed',
                    'time' => isset($operatingHours[$code]['time']) && $operatingHours[$code]['time'] ? $operatingHours[$code]['time'] : []
                ];
            }
        }

        return $formattedOperatingHours;
    }

    public function getDayNames() 
    {
        $dayNames = [
            [
                'code' => 'mon',
                'label' => 'Monday'
            ],
            [
                'code' => 'tue',
                'label' => 'Tuesday'
            ],
            [
                'code' => 'wed',
                'label' => 'Wednesday'
            ],
            [
                'code' => 'thu',
                'label' => 'Thursday'
            ],
            [
                'code' => 'fri',
                'label' => 'Friday'
            ],
            [
                'code' => 'sat',
                'label' => 'Saturday'
            ],
            [
                'code' => 'sun',
                'label' => 'Sunday'
            ]
        ];

        return $dayNames;
    }
}
