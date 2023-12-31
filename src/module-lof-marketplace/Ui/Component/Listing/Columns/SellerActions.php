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

namespace Lof\MarketPlace\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Lof\MarketPlace\Block\Adminhtml\Seller\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class SellerActions extends Column
{
    /** Url Path */
    const SELLER_URL_PATH_EDIT = 'lofmarketplace/seller/edit';
    const SELLER_URL_PATH_DELETE = 'lofmarketplace/seller/delete';
    const SELLER_URL_PATH_ENABLE = 'lofmarketplace/seller/enable';
    const SELLER_URL_PATH_DISABLE = 'lofmarketplace/seller/disable';
    const SELLER_URL_PATH_VERIFY = 'lofmarketplace/seller/verify';
    const SELLER_URL_PATH_UNVERIFY = 'lofmarketplace/seller/unverify';
    const SELLER_URL_PATH_REQUEST_WITHDRAWL = 'lofmarketplace/withdrawal/new';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * SellerActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::SELLER_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['seller_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['seller_id' => $item['seller_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_DELETE,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ' . $item['name']),
                            'message' => __('Are you sure you want to delete a "' . $item['name'] . '" record?')
                        ]
                    ];
                    $item[$name]['enable'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_ENABLE,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Approved'),
                        'confirm' => [
                            'title' => __('Approved "' . $item['name'] . '"'),
                            'message' => __('Are you sure you want to enable a "' . $item['name'] . '" record?')
                        ]
                    ];
                    $item[$name]['disable'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_DISABLE,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Disapproved'),
                        'confirm' => [
                            'title' => __('Disapproved "' . $item['name'] . '"'),
                            'message' => __('Are you sure you want to disable a "' . $item['name'] . '" record?')
                        ]
                    ];
                    $item[$name]['verify'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_VERIFY,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Verify'),
                        'confirm' => [
                            'title' => __('Verify "' . $item['name'] . '"'),
                            'message' => __('Are you sure you want to verify "' . $item['name'] . '"?')
                        ]
                    ];
                    $item[$name]['unverify'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_UNVERIFY,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Cancel verification'),
                        'confirm' => [
                            'title' => __('Cancel verification "' . $item['name'] . '"'),
                            'message' => __('Are you sure you want to cancel verification of "' . $item['name'] . '"?')
                        ]
                    ];
                    $item[$name]['withdrawl'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::SELLER_URL_PATH_REQUEST_WITHDRAWL,
                            ['seller_id' => $item['seller_id']]
                        ),
                        'label' => __('Request Withdrawl'),
                        'confirm' => [
                            'title' => __('Request Withdrawl "' . $item['name'] . '"'),
                            'message' => __('Are you sure you want to create withdrawl request of "' . $item['name'] . '"?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
