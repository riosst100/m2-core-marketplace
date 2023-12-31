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

namespace Lofmp\DeliverySlot\Model\Config;

use Magento\Framework\Registry;
use Lof\MarketPlace\Model\ResourceModel\Config\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->registry = $registry;
        $this->_request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->registry->registry('config');
        $seller_id = $this->registry->registry('seller_id');
        $this->loadedData = [];
        $configData = [];
        if($items){
            foreach ($items as $item) {
                $key = $item["path"];
                $key = str_replace("/","__",$key);
                $configData[$key] = $item["value"];
            }
        }
        $this->loadedData[$seller_id] = $configData;
        return $this->loadedData;
    }
}
