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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

class Sellerlist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var Seller
     */
    protected $_seller;

    /**
     * Sellerlist constructor.
     * @param Seller $seller
     */
    public function __construct(
        \Lof\MarketPlace\Model\Seller $seller
    ) {
        $this->_seller = $seller;
    }

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $sellers = $this->_seller->getCollection()
            ->addFieldToFilter('status', '1');
        $listSeller = [];
        foreach ($sellers as $seller) {
            $listSeller[] = [
                'label' => $seller->getName(),
                'value' => $seller->getId()
            ];
        }
        return $listSeller;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, [
                'value' => '',
                'label' => '-- Please Select --',
            ]);
        }

        return $options;
    }
}
