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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\StoreCredit\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

/**
 * Class PageActions
 */
class TransactionActions extends Column
{
    /** Url path */
    const CMS_URL_PATH_DELETE   = 'lofstorecredit/customer/delete';
    const CMS_URL_PATH_EDIT   = 'lofstorecredit/customer/edit';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;
    /**
     * @var string
     */
    private $deleteUrl;
    /**
     * @var string
     */
    private $editUrl;
    /**
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
        $deleteUrl = self::CMS_URL_PATH_DELETE,
        $editUrl = self::CMS_URL_PATH_EDIT
    ) {
        $this->urlBuilder       = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->deleteUrl        = $deleteUrl;
        $this->editUrl          = $editUrl;
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
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {

                    $item[$name]['delete'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $this->deleteUrl,
                                [
                                    'entity_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.email }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.email }" record?')
                            ]
                    ];
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $this->editUrl,
                            [
                                'entity_id' => $item['entity_id']
                            ]
                        ),
                        'label' => __('Edit')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
