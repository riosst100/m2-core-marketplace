<?php

namespace Lofmp\Rma\Cron;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\Api\SortOrderBuilder;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Lofmp\Rma\Api\Repository\RmaRepositoryInterface;

class SendAdminNotify
{

    protected $logger;

    public function __construct(
        \Lofmp\Rma\Helper\Mail $rmaMail,
        \Lofmp\Rma\Helper\Help $Helper,
        RmaRepositoryInterface $rmaRepository,
        SortOrderBuilder       $sortOrderBuilder,
        SearchCriteriaBuilder  $searchCriteriaBuilder
    ) {
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->rmaRepository         = $rmaRepository;
        $this->rmaMail               = $rmaMail;
        $this->helper                   = $Helper;
    }

/**
 * Write to system.log
 *
 * @return void
 */

    public function execute()
    {
        $date = $this->helper->getConfig($store = null, 'rma/policy/outdate');
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();
         $to = date("Y-m-d h:i:s"); // current date
         $from = strtotime('-'.$date.' day', strtotime($to));
         $from = date('Y-m-d', $from);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addSortOrder($sortOrderSort)
        ;
        $rmasList =  $this->rmaFactory->create()->getCollection();
        $rmasList->addFieldToFilter('main_table.created_at', ['lteq' =>$from])->addFieldToFilter('status.name', ['neq' =>'Closed']);
        if (count($rmasList)) {
            $this->rmaMail->sendAdminNotifyEmail($rmasList);
        }
    }
}
