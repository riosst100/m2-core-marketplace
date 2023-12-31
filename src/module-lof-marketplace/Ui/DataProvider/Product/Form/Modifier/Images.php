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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

use Lof\MarketPlace\Model\Locator\LocatorInterface;

class Images extends AbstractModifier
{
    /**#@+
     * Attribute names
     */
    const CODE_IMAGE_MANAGEMENT_GROUP = 'image-management';
    const CODE_MEDIA_GALLERY = 'media_gallery';
    const CODE_IMAGE = 'image';
    const CODE_SMALL_IMAGE = 'small_image';
    const CODE_THUMBNAIL = 'thumbnail';
    const CODE_SWATCH_IMAGE = 'swatch_image';
    /**#@-*/

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator, \Magento\Framework\App\Request\Http $request)
    {
        $this->locator = $locator;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        unset($meta[self::CODE_IMAGE_MANAGEMENT_GROUP]);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $productId = $this->request->getPost('id');
        $modelId = $productId;
        if (isset($data[$modelId][self::DATA_SOURCE_DEFAULT]['media_gallery']['images'])
            && is_array($data[$modelId][self::DATA_SOURCE_DEFAULT]['media_gallery']['images'])
        ) {
            foreach ($data[$modelId][self::DATA_SOURCE_DEFAULT]['media_gallery']['images'] as $index => $image) {
                if (!isset($image['label'])) {
                    $data[$modelId][self::DATA_SOURCE_DEFAULT]['media_gallery']['images'][$index]['label'] = '';
                }
            }
        }

        return $data;
    }
}
