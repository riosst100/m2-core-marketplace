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

namespace Lof\MarketPermissions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AfterDeleteSellerObserver implements ObserverInterface
{

    /**
     * @var int
     */
    private $noSellerId = 0;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @var \Lof\MarketPermissions\Api\TeamRepositoryInterface
     */
    private $teamRepository;

    /**
     * @var \Lof\MarketPermissions\Model\StructureRepository
     */
    private $structureRepository;

    /**
     * AfterDeleteSellerObserver constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     * @param \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository
     * @param \Lof\MarketPermissions\Model\StructureRepository $structureRepository
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager,
        \Lof\MarketPermissions\Api\TeamRepositoryInterface $teamRepository,
        \Lof\MarketPermissions\Model\StructureRepository $structureRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->structureManager = $structureManager;
        $this->teamRepository = $teamRepository;
        $this->structureRepository = $structureRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute(Observer $observer)
    {
        $seller = $observer->getSeller();
        //$model = $observer->getEvent()->getDataObject();
        if ($seller instanceof \Lof\MarketPlace\Model\Seller) {
            $allowedIds = $this->structureManager->getAllowedIds($seller->getCustomerId());
            $teams = $this->structureManager->getUserChildTeams($seller->getCustomerId());

            $this->detachCustomersFromSeller($allowedIds['users']);
            $this->deleteTeams($teams);
        }
    }


    /**
     * Delete seller teams.
     *
     * @param array $teams
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteTeams(array $teams)
    {
        foreach ($teams as $teamStructure) {
            $this->teamRepository->deleteById($teamStructure->getEntityId());
            $this->structureRepository->delete($teamStructure);
        }
    }

    /**
     * Detach customers from the seller.
     *
     * @param array $users
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function detachCustomersFromSeller(array $users)
    {
        foreach ($users as $customerId) {
            $this->structureManager->removeCustomerNode($customerId);
            $this->detachCustomerFromSeller($customerId);
        }
    }

    /**
     * Detach the customer from the seller.
     *
     * @param $customerId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function detachCustomerFromSeller($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        /** @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterface $sellerAttributes */
        $sellerAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
        $sellerAttributes->setSellerId($this->noSellerId);
        $sellerAttributes->setStatus(\Lof\MarketPermissions\Api\Data\SellerCustomerInterface::STATUS_INACTIVE);
        $this->customerRepository->save($customer);
    }
}
