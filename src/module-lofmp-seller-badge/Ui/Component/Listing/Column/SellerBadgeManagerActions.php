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

namespace Lofmp\SellerBadge\Ui\Component\Listing\Column;

use Lof\MarketPlace\Model\Seller;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;

class SellerBadgeManagerActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * actions list
     */
    const URL_PATH_DELETE = 'lofmp_sellerbadge/sellerbadgemanager/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Seller
     */
    protected $seller;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        Seller $seller,
        array $components = [],
        array $data = []
    ) {
        $this->seller = $seller;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['manager_id'])) {
                    $name = $this->seller->load($item['seller_id'])->getName();
                    $item[$this->getData('name')] = [
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'manager_id' => $item['manager_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete %1', $name),
                                'message' => __('Are you sure you want to delete a %1 record?', $name),
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get instance of escaper
     *
     * @return Escaper
     */
    private function getEscaper()
    {
        if (!$this->escaper) {
            $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
        }
        return $this->escaper;
    }
}
