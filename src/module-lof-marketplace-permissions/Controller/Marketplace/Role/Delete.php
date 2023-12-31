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

/**
 * Controller for role deleting.
 */
class Delete extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction
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
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * Delete constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->roleRepository = $roleRepository;
        $this->sellerUser = $sellerUser;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $roleId = $request->getParam('id');
        try {
            $role = $this->roleRepository->get($roleId);
            $sellerId = $this->sellerUser->getCurrentSellerId();

            if ($role->getSellerId() != $sellerId) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Bad Request'));
            }

            $this->roleRepository->delete($role->getId());
            $this->messageManager->addSuccessMessage(
                __(
                    'You have deleted role %sellerRoleName.',
                    ['sellerRoleName' => $role ? $role->getRoleName() : '']
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again later.'));
            $this->logger->critical($e);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('permissions/role/');

        return $resultRedirect;
    }
}
