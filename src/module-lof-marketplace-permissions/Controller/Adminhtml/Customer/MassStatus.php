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

declare(strict_types=1);

namespace Lof\MarketPermissions\Controller\Adminhtml\Customer;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory;
use Magento\Framework\App\ObjectManager;

/**
 * Class MassStatus
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassStatus extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SellerCustomerInterfaceFactory
     */
    private $sellerCustomerFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param SellerCustomerInterfaceFactory|null $sellerCustomerFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        SellerCustomerInterfaceFactory $sellerCustomerFactory = null
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
        $this->sellerCustomerFactory = $sellerCustomerFactory ?: ObjectManager::getInstance()
            ->get(SellerCustomerInterfaceFactory::class);
    }

    /**
     * @inheritDoc
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = (int)$this->getRequest()->getParam('status');
        $customersUpdated = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $customerExtensionAttributes = $customer->getExtensionAttributes();
            /** @var SellerCustomerInterface $sellerCustomerAttributes */
            $sellerCustomerAttributes = $customerExtensionAttributes->getSellerAttributes();
            if (!$sellerCustomerAttributes) {
                $sellerCustomerAttributes = $this->sellerCustomerFactory->create();
            }
            $sellerCustomerAttributes->setStatus($status);
            $customerExtensionAttributes->setSellerAttributes($sellerCustomerAttributes);

            try {
                $this->customerRepository->save($customer);
                $customersUpdated++;
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        if ($customersUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $customersUpdated));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/index/index');

        return $resultRedirect;
    }
}
