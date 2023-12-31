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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Ui\Component\Listing\Role\Column;

use Lof\MarketPermissions\Api\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductActions
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!$this->authorization->isAllowed('Lof_MarketPermissions::roles_edit')
            || !isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        $count = count($dataSource['data']['items']);
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    'permissions/role/edit',
                    ['id' => $item['role_id']]
                ),
                'label' => __('Edit'),
                'hidden' => false,
            ];
            if ($count > 1) {
                $item[$this->getData('name')]['delete'] = [
                    'href' => '#',
                    'label' => __('Delete'),
                    'hidden' => false,
                    'type' => 'delete-role',
                    'options' => [
                        'deleteUrl' => $this->urlBuilder->getUrl(
                            'permissions/role/delete',
                            ['id' => $item['role_id']]
                        ),
                        'deletable' => !(int)$item['users_count'],
                    ]
                ];
            }
            $item[$this->getData('name')]['duplicate'] = [
                'href' => $this->urlBuilder->getUrl(
                    'permissions/role/edit',
                    ['duplicate_id' => $item['role_id']]
                ),
                'label' => __('Duplicate'),
                'hidden' => false,
            ];
        }
        return $dataSource;
    }
}
