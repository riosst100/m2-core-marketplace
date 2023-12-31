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
 * @package    Lof_MarketplaceGraphQl
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model\Resolver\DataProvider;

use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Product field data provider, used for GraphQL resolver processing.
 */
class CreateSeller
{

    /**
     * @var SellersRepositoryInterface
     */
    private $sellerRepository;

    /**
     * CreateSeller constructor.
     * @param SellersRepositoryInterface $sellersRepository
     */
    public function __construct(
        SellersRepositoryInterface $sellersRepository
    ) {
        $this->sellerRepository = $sellersRepository;
    }

    /**
     * @param $data
     * @param $customerId
     * @return mixed
     * @throws LocalizedException
     */
    public function createSeller($data, $customerId)
    {
        return $this->sellerRepository->saveSeller($data, $customerId);
    }

    /**
     * @param CustomerInterface $customer
     * @param $data
     * @param $password
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|array|mixed|string|null
     * @throws InputException
     * @throws LocalizedException
     * @throws InputMismatchException
     */
    public function registerSeller(CustomerInterface $customer, $data, $password)
    {
        return $this->sellerRepository->registerNewSeller($customer, $data, $password);
    }
}
