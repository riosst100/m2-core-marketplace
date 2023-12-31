<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Model\Quickrfq;

use Lof\Quickrfq\Model\ResourceModel\Quickrfq\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $collection;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $quoteCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $quoteCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $quoteCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $quote) {
            $this->loadedData[$quote->getId()] = $quote->getData();
            $temp = $quote->getData();
        }

        $data = $this->dataPersistor->get('lof_quickrfq');

        if (!empty($data)) {
            $quote = $this->collection->getNewEmptyItem();
            $quote->setData($data);
            $this->loadedData[$quote->getId()] = $quote->getData();

            $this->dataPersistor->clear('lof_quickrfq');
        }

        if (empty($this->loadedData)) {
            return $this->loadedData;
        } else {
            if ($quote->getData('product_name') != null) {
                $t2[$quote->getId()] = $temp;
                return $t2;
            } else {
                return $this->loadedData;
            }
        }
    }
}
