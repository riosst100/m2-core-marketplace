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

namespace Lof\MarketPermissions\Block\Marketplace\Role;

class Edit extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Acl\AclResource\ProviderInterface
     */
    private $resourceProvider;

    /**
     * @var \Lof\MarketPermissions\Model\Authorization\PermissionProvider
     */
    private $permissionProvider;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider
     * @param \Lof\MarketPermissions\Model\Authorization\PermissionProvider $permissionProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider,
        \Lof\MarketPermissions\Model\Authorization\PermissionProvider $permissionProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->roleRepository = $roleRepository;
        $this->roleFactory = $roleFactory;
        $this->jsonHelper = $jsonHelper;
        $this->resourceProvider = $resourceProvider;
        $this->permissionProvider = $permissionProvider;
    }

    /**
     * Get Role.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRole()
    {
        $id = $this->retrieveRoleId();
        if ($id) {
            $role = $this->roleRepository->get($id);
            if ($this->isDuplicate()) {
                $role->setId(null);
                $role->setRoleName($role->getRoleName() . ' - ' . __('Duplicated'));
            }
            return $role;
        }
        return $this->roleFactory->create();
    }

    /**
     * Get tree JS options.
     *
     * @return array
     */
    public function getTreeJsOptions()
    {
        $roleId = $this->retrieveRoleId();
        if ($roleId) {
            $permissions = $this->permissionProvider->retrieveRolePermissions($roleId);
        } else {
            $permissions = [];
        }
        $resources = $this->resourceProvider->getAclResources();
        return [
            'roleTree' => [
                'data' => $this->prepareTreeData($resources, $permissions)
            ]
        ];
    }

    /**
     * Prepare tree data.
     *
     * @param array $resources
     * @param array $permissions
     * @param int $level
     * @return array
     */
    private function prepareTreeData(array &$resources, array $permissions, $level = 0)
    {
        for ($counter = 0, $counterMax = count($resources); $counter < $counterMax; $counter++) {
            $resources[$counter]['text'] = $resources[$counter]['title'];
            unset($resources[$counter]['title'], $resources[$counter]['sort_order']);
            $resources[$counter]['state'] = [];
            if (!empty($resources[$counter]['children'])) {
                $this->prepareTreeData($resources[$counter]['children'], $permissions, $level + 1);
                $resources[$counter]['state']['opened'] = 'open';
            }
            if (isset($permissions[$resources[$counter]['id']])
                && $permissions[$resources[$counter]['id']] === 'allow') {
                $resources[$counter]['state']['selected'] = true;
            }
            if ($level === 0) {
                $resources[$counter]['li_attr'] = ['class' => 'root-collapsible'];
            }
        }
        return $resources;
    }

    /**
     * Retrieve role Id.
     *
     * @return string
     */
    private function retrieveRoleId()
    {
        return $this->getRequest()->getParam('id') ?: $this->getRequest()->getParam('duplicate_id');
    }

    /**
     * Check if duplicate.
     *
     * @return bool
     */
    private function isDuplicate()
    {
        return $this->getRequest()->getParam('id') ? false : true;
    }

    /**
     * Get json helper.
     *
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
