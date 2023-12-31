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

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Validate.
 */
class Validate extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::roles_edit';

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->sellerUser = $sellerUser;
        $this->roleRepository = $roleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $resultJson->setData([
            'seller_role_name' => $this->isSellerRoleNameValid($this->getRequest()->getParam('seller_role_name')),
        ]);

        return $resultJson;
    }

    /**
     * Is seller role name valid.
     *
     * @param string $roleName
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isSellerRoleNameValid($roleName)
    {
        $sellerId = $this->sellerUser->getCurrentSellerId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Lof\MarketPermissions\Api\Data\RoleInterface::ROLE_NAME, $roleName)
            ->addFilter(\Lof\MarketPermissions\Api\Data\RoleInterface::SELLER_ID, $sellerId)
            ->create();
        return !$this->roleRepository->getList($searchCriteria)->getTotalCount();
    }
}
