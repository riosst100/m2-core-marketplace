<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\DeliverySlot\Ui\Component\Listing\Column;

use Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SlotGroup.
 */
class SlotGroup extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var DeliverySlotGroupFactory
     */
    protected $slotGroup;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param DeliverySlotGroupFactory $slotGroups
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        DeliverySlotGroupFactory $slotGroup,
        array $components = [],
        array $data = []
    ) {
        $this->slotGroup = $slotGroup;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['parent_id'])) {
                    $slotGroup = $this->slotGroup->create()->load($item['parent_id']);
                    if ($slotGroup->getData()) {
                        $item[$fieldName] = "<a href='".$this->urlBuilder->getUrl(
                            'deliveryslot/deliveryslotgroup/edit',
                            ['group_id' => $item['parent_id']]
                        )."' target='blank' title='".__('View Group')."'>".$slotGroup->getSlotGroupName().'</a>';
                    } else {
                        $item[$fieldName] = 'NaN';
                    }
                }
            }
        }

        return $dataSource;
    }
}
