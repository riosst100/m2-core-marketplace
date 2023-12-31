<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

abstract class Actions extends Column
{
    /** Url path */
    protected $urlPathEnable;
    protected $urlPathDisable;
    protected $urlPathDelete;
    protected $urlPathEdit;

    protected $idFieldName;

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

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
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
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
                if (isset($item[$this->idFieldName])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                $this->urlPathEdit,
                                [
                                    $this->idFieldName => $item[$this->idFieldName]
                                ]
                            ),
                            'label' => __('Edit')
                        ]
                    ];
                }
                if (isset($item["$this->idFieldName"])) {
                    //Them link den trang edit
                    if($item['status'] == 0) {
                        $item[$name]['enable'] = [
                            'href' => $this->urlBuilder->getUrl($this->urlPathEnable, ['id' => $item["$this->idFieldName"]]),
                            'label' => __('Enable')
                        ];
                    }
                    else {
                        $item[$name]['disable'] = [
                            'href' => $this->urlBuilder->getUrl($this->urlPathDisable, ['id' => $item["$this->idFieldName"]]),
                            'label' => __('Disable')
                        ];
                    }

                    //Them link den trang delete
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl($this->urlPathDelete, ['id' => $item["$this->idFieldName"]]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete "${ $.$data.title }" record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
