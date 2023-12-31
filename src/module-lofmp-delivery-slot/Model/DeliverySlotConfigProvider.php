<?php
/**
 * @package Lofmp_DeliverySlot
 * @author  SaiRam sairam@egrovesystems.com
 */

namespace Lofmp\DeliverySlot\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Lofmp\DeliverySlot\Helper\Data;

/**
 * Class DeliverySlotConfigProvider
 *
 * @package Lofmp\DeliverySlot\Model
 */
class DeliverySlotConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * DeliverySlotConfigProvider constructor.
     *
     * @param Data $data
     */
    public function __construct(
        Data $data
    ) {
        $this->data = $data;
    }

    /**
     * Get Delivery Slot Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $deliverySlotConfig = [];
        $deliverySlotConfig['delivery_slot'] = $this->getDeliverySlotConfig();
        return $deliverySlotConfig;
    }

    /**
     * Return Delivery Slot Configuration
     *
     * @return array
     */
    protected function getDeliverySlotConfig()
    {
        return [
            'enable' => (int)$this->data->getDeliverySlotConfig('enabled')
        ];
    }
}
