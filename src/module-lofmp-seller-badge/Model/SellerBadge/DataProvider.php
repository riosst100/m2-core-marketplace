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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model\SellerBadge;

use Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var
     */
    protected $collection;

    /**
     * @var FileInfo
     */
    private $_fileInfo;

    /**
     * @var Image
     */
    protected $badgeImage;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param \Lofmp\SellerBadge\Model\SellerBadge\FileInfo|null $fileInfo
     * @param \Lofmp\SellerBadge\Model\SellerBadge\Image|null $badgeImage
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        FileInfo $fileInfo = null,
        ?Image $badgeImage = null
    ) {
        $this->_fileInfo = $fileInfo ?: \Magento\Framework\App\ObjectManager::getInstance()->get(FileInfo::class);
        $this->badgeImage = $badgeImage ?? \Magento\Framework\App\ObjectManager::getInstance()->get(Image::class);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
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
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadDataAfterSave($model);
        }
        $data = $this->dataPersistor->get('lofmp_sellerbadge_badge');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadDataAfterSave($model);
            $this->dataPersistor->clear('lofmp_sellerbadge_badge');
        }

        return $this->loadedData;
    }

    /**
     * @param $model
     */
    private function loadDataAfterSave($model)
    {
        $data = $model->getData();
        $id = $model->getId();
        $this->loadedData[$id]['general'] = $model->getData();
        $this->loadedData[$id]['general']['image'] = $this->convertValues(
            $model,
            $data,
            'image'
        );
        $this->loadedData[$id]['rule']['conditions'] = $model->getConditons();
    }

    /**
     * @param $model
     * @param $data
     * @param $field
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function convertValues($model, $data, $field)
    {
        if ($fileName = $model->getData($field)) {
            if ($this->_fileInfo->isExist($fileName)) {
                $stat = $this->_fileInfo->getStat($fileName);
                $mime = $this->_fileInfo->getMimeType($fileName);
                $data['general'][$field][0]['name'] = basename($fileName);
                $data['general'][$field][0]['url'] = $this->badgeImage->getUrl($model, $field);
                $data['general'][$field][0]['size'] = isset($stat) ? $stat['size'] : 0;
                $data['general'][$field][0]['type'] = $mime;
                return $data['general'][$field];
            }

        }
        return $data['general'][$field] = [];
    }
}
