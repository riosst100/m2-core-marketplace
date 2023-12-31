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

namespace Lof\MarketPermissions\Block\Marketplace;

use Lof\MarketPermissions\Api\Data\StructureInterface;
use Lof\MarketPermissions\Api\Data\TeamInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Seller management block.
 */
class Management extends \Magento\Framework\View\Element\Template
{
    /**
     * Until what level the tree is open.
     *
     * @var string
     */
    private $level = 2;

    /**
     * Customer icon.
     *
     * @var string
     */
    private $iconCustomer = 'icon-customer';

    /**
     * Team icon.
     *
     * @var string
     */
    private $iconTeam = 'icon-seller';

    /**
     * Open state.
     *
     * @var bool
     */
    private $stateOpen = true;

    /**
     * Closed bool.
     *
     * @var string
     */
    private $stateClosed = false;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $customerContext;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var bool|null
     */
    private $isSuperUser = null;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $treeManagement;

    /**
     * @var \Lof\MarketPermissions\Api\SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Authorization\Model\UserContextInterface $customerContext
     * @param \Lof\MarketPermissions\Model\Seller\Structure $treeManagement
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement
     * @param \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
     * @param array $data [optional]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Authorization\Model\UserContextInterface $customerContext,
        \Lof\MarketPermissions\Model\Seller\Structure $treeManagement,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement,
        \Lof\MarketPermissions\Api\AuthorizationInterface $authorization,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerContext = $customerContext;
        $this->jsonHelper = $jsonHelper;
        $this->treeManagement = $treeManagement;
        $this->sellerManagement = $sellerManagement;
        $this->authorization = $authorization;
    }

    /**
     * Gets prepare tree array.
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @param int $level [optional]
     * @return array
     */
    private function getTreeAsArray(\Magento\Framework\Data\Tree\Node $tree, $level = 0)
    {
        $data = $this->getTreeItemAsArray($tree);
        if ($tree->hasChildren()) {
            $data['state']['opened'] = ($level < $this->level) ? $this->stateOpen : $this->stateClosed;
            foreach ($tree->getChildren() as $child) {
                $data['children'][] = $this->getTreeAsArray($child, ($level + 1));
            }
            $this->sortTreeArray($data['children']);
        }
        return $data;
    }

    /**
     * Sorts tree array.
     *
     * @param array $treeArray
     * @return void
     */
    private function sortTreeArray(array &$treeArray)
    {
        usort(
            $treeArray,
            function ($elementA, $elementB) {
                return strcmp($elementA['text'], $elementB['text']);
            }
        );
    }

    /**
     * Gets tree item as array.
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @return array
     */
    private function getTreeItemAsArray(\Magento\Framework\Data\Tree\Node $tree)
    {
        $data = [];
        if ($tree->getData(StructureInterface::ENTITY_TYPE) == StructureInterface::TYPE_TEAM) {
            $data['type'] = $this->iconTeam;
            $data['text'] = $this->escapeHtml($tree->getData(TeamInterface::NAME));
            $data['description'] = $this->escapeHtml($tree->getData(TeamInterface::DESCRIPTION));
        } else {
            $data['type'] = $this->iconCustomer;
            $data['text'] = $this->escapeHtml(
                $tree->getData(CustomerInterface::FIRSTNAME) .
                ' ' .
                $tree->getData(CustomerInterface::LASTNAME)
            );
            if ($this->customerContext->getUserId() == $tree->getData(StructureInterface::ENTITY_ID)) {
                $data['text'] .= ' ' . __('(me)');
            }
        }
        $data['attr']['data-tree-id'] = $tree->getData(StructureInterface::STRUCTURE_ID);
        $data['attr']['data-entity-id'] = $tree->getData(StructureInterface::ENTITY_ID);
        $data['attr']['data-entity-type'] = $tree->getData(StructureInterface::ENTITY_TYPE);
        return $data;
    }

    /**
     * Gets if current user is an SU.
     *
     * @return bool
     */
    public function isSuperUser()
    {
        if ($this->isSuperUser === null) {
            $this->isSuperUser = $this->authorization->isAllowed('Lof_MarketPermissions::users_edit');
        }
        return $this->isSuperUser;
    }

    /**
     * Gets tree array.
     *
     * @return array
     */
    public function getTree()
    {
        $result = [];
        $customerId = $this->customerContext->getUserId();
        if ($customerId) {
            $tree = $this->treeManagement->getTreeByCustomerId($customerId);
            $this->treeManagement->addDataToTree($tree);
            $this->treeManagement->filterTree($tree, 'is_active', true);
            if ($tree->getData(StructureInterface::ENTITY_ID) == $customerId) {
                $this->isSuperUser = true;
            }
            $result = $this->getTreeAsArray($tree);
        }
        return $result;
    }

    /**
     * Get tree js options.
     *
     * @return array
     */
    public function getTreeJsOptions()
    {
        return [
            'hierarchyTree' => [
                'moveUrl'   => $this->getUrl('*/structure/manage'),
                'selectionLimit' => 1,
                'draggable' => $this->isSuperUser(),
                'initData'  => $this->getUrl('*/structure/get'),
                'adminUserRoleId' => 0
            ]
        ];
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
     * Has current customer seller.
     *
     * @return bool
     */
    public function hasCustomerSeller()
    {
        $hasSeller = false;
        $customerId = $this->customerContext->getUserId();
        if ($customerId) {
            $seller = $this->sellerManagement->getByCustomerId($customerId);
            if ($seller) {
                $hasSeller = true;
            }
        }

        return $hasSeller;
    }
}
