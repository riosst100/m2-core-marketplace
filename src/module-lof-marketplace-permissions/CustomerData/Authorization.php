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

namespace Lof\MarketPermissions\CustomerData;

use Lof\MarketPermissions\Api\AuthorizationInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Seller authorization section.
 */
class Authorization implements SectionSourceInterface
{
    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * @var array
     */
    private $authorizationResources;

    /**
     * @param AuthorizationInterface $authorization
     * @param array $authorizationResources
     */
    public function __construct(
        AuthorizationInterface $authorization,
        $authorizationResources = []
    ) {
        $this->authorization = $authorization;
        $this->authorizationResources = $authorizationResources;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'resources' => $this->getAuthorizationResourcesStates()
        ];
    }

    /**
     * Get authorization resources states.
     *
     * @return array
     */
    private function getAuthorizationResourcesStates()
    {
        $authorizationResourcesStatus = [];
        foreach ($this->authorizationResources as $resource) {
            $authorizationResourcesStatus[$resource] = $this->authorization->isAllowed($resource);
        }

        return $authorizationResourcesStatus;
    }
}
