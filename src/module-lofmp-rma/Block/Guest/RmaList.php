<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Block\Guest;

class RmaList extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $sessionObj,
        \Magento\Customer\Model\Session $customerSession,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface     $rmaRepository,
        \Lofmp\Rma\Model\Status $statusFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder       $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder            $sortOrderBuilder,
        \Magento\Framework\View\Element\Template\Context $context,
        \Lofmp\Rma\Helper\Help                                $Helper,
        array $data = []
    ) {
        $this->rmaRepository = $rmaRepository;
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->status         = $statusFactory;
        $this->helper                = $Helper;
        $this->sessionObj = $sessionObj;
        parent::__construct($context);
    }
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Returns'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('My Returns'));
        }
    }

    public function getSessionOrder()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestOrderId();
    }

    public function getSessionEmail()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestEmail();
    }

    public function getRmaList()
    {
        if ($this->getIsShowBundle()) {
            $rma = $this->getRmaAvailableList(0);
        } else {
            $rma = $this->getRmaAvailableList();
        }
        return $rma;
    }

    public function getRmaAvailableList($parent_rma_id = null)
    {
        $customer_email = $this->getSessionEmail();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('main_table.customer_email', $customer_email)
            ->addSortOrder($this->sortOrderBuilder
                            ->setField('rma_id')
                            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_DESC)
                            ->create());

        if ($parent_rma_id !== null) {
            $searchCriteria = $searchCriteria
                ->addFilter('main_table.parent_rma_id', (int)$parent_rma_id);
        }
        
        $rma = $this->rmaRepository->getList($searchCriteria->create())->getItems();
        return $rma;
    }

    public function getChildrenRmaList($parent_rma_id = 0)
    {
        if ($parent_rma_id) {
            return $this->getRmaAvailableList($parent_rma_id);
        }
        return ;
    }

    /**
     * @return string
     */
    public function getNewRmaUrl()
    {
        return $this->_urlBuilder->getUrl('rma/guest/sellect');
    }
    /**
     * @return string
     */
    public function getStatusname($id)
    {
         $status =  $this->status->load($id);
         return $status->getName();
    }
    public function getIsShowBundle()
    {
        return $this->helper->isShowBundleRmaFrontend();
    }
}
