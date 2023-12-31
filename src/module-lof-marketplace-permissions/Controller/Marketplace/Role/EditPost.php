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

namespace Lof\MarketPermissions\Controller\Marketplace\Role;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class EditPost.
 */
class EditPost extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::roles_edit';

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @var \Lof\MarketPermissions\Model\PermissionManagementInterface
     */
    private $permissionManagement;

    /**
     * EditPost constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     * @param \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser,
        \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->roleRepository = $roleRepository;
        $this->roleFactory = $roleFactory;
        $this->sellerUser = $sellerUser;
        $this->permissionManagement = $permissionManagement;
    }

    /**
     * Roles and permissions edit post.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $role = $this->roleFactory->create();
        $id = $this->getRequest()->getParam('id');

        try {
            $sellerId = $this->sellerUser->getCurrentSellerId();
            if ($id) {
                $role = $this->roleRepository->get($id);
                if ($role->getSellerId() != $sellerId) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Bad Request'));
                }
            }
            $role->setRoleName($this->getRequest()->getParam('role_name'));
            $role->setSellerId($sellerId);
            $resources = explode(',', $this->getRequest()->getParam('role_permissions'));
            $role->setPermissions($this->permissionManagement->populatePermissions($resources));
            $this->roleRepository->save($role);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);

            if ($id) {
                $result = $this->_redirect('*/role/edit', ['id' => $id]);
            } else {
                $result = $this->_redirect('*/role/edit');
            }
            return $result;
        }
        return $this->_redirect('*/*/');
    }
}
