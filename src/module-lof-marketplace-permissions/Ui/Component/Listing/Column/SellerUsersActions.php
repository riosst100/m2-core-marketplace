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

namespace Lof\MarketPermissions\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class SellerUsersActions.
 */
class SellerUsersActions extends Column
{
    /**
     * Url interface.
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Customer status Active.
     *
     * @var string
     */
    private $customerStatusActive = 'Active';

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * SellerUsersActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        \Lof\MarketPermissions\Api\AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->roleManagement = $roleManagement;
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
        if ($this->authorization->isAllowed('Lof_MarketPermissions::users_edit')
            && isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $getUrl = $this->urlBuilder->getUrl('permissions/customer/get');
                $provider = 'seller_users_listing.seller_users_listing_data_source';
                $item[$this->getData('name')]['edit'] = [
                    'href' => '#',
                    'label' => __('Edit'),
                    'hidden' => false,
                    'type' => 'edit-user',
                    'options' => [
                        'getUrl' => $getUrl,
                        'getUserUrl' => $getUrl . '?customer_id=' . $item['entity_id'],
                        'saveUrl' => $this->urlBuilder->getUrl('permissions/customer/manage'),
                        'id' => $item['entity_id'],
                        'gridProvider' => $provider,
                        'adminUserRoleId' => $this->roleManagement->getSellerAdminRoleId(),
                    ],
                ];
                $item[$this->getData('name')]['delete'] = [
                    'href' => '#',
                    'label' => __('Delete'),
                    'hidden' => false,
                    'id' => $item['entity_id'],
                    'type' => 'delete-user',
                    'options' => [
                        'setInactiveUrl' => $this->urlBuilder->getUrl('permissions/customer/delete'),
                        'deleteUrl' => $this->urlBuilder->getUrl('permissions/customer/permanentDelete'),
                        'id' => $item['entity_id'],
                        'gridProvider' => $provider,
                        'inactiveClass' => $this->getSetInactiveButtonClass($item),
                    ],
                ];
            }
        }

        return $dataSource;
    }

    /**
     * Get set inactive button class.
     *
     * @param array $userData
     * @return string
     */
    private function getSetInactiveButtonClass(array $userData)
    {
        return ($this->isShowSetInactiveButton($userData)) ? '' : '_hidden';
    }

    /**
     * Is show set inactive button.
     *
     * @param array $userData
     * @return bool
     */
    private function isShowSetInactiveButton(array $userData)
    {
        return (!empty($userData['status']) && $userData['status']->getText() == $this->customerStatusActive);
    }
}
