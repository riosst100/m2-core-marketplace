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
        $operatingHours = isset($value['operating_hours']) && $value['operating_hours'] ? json_decode($value['operating_hours'], true) : null;

        if (isset($value['model'])) {
            $model = $value['model'];
            $operatingHours = $model && $model->getOperatingHours() ? json_decode($model->getOperatingHours(),true) : null;
        }

        foreach($this->getDayNames() as $index => $day) {
            $code = $day['code']; 
            $formattedOperatingHours[] = [
                'day' => $day['label'],
                'status' => isset($operatingHours[$code]['status']) && $operatingHours[$code]['status'] ? $operatingHours[$code]['status'] : 'closed',
                'time' => isset($operatingHours[$code]['time']) && $operatingHours[$code]['time'] ? $operatingHours[$code]['time'] : []
            ];
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
