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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lof\Auction\Controller\Adminhtml\MixAmount;

use Lof\Auction\Controller\Adminhtml\Mix;
use Lof\Auction\Helper\ProcessWinner;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\MixAmountFactory;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Setwinning
 * @package Lof\Auction\Controller\Adminhtml\MixAmount
 */
class Setwinning extends Mix
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ProcessWinner
     */
    protected $_processWinnerHelper;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var AmountFactory
     */
    private $amount;
    /**
     * @var AutoAuctionFactory
     */
    private $autoAmount;
    /**
     * @var MixAmountFactory
     */
    private $mixAmount;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ProcessWinner $processWinner
     * @param CustomerRepositoryInterface $customerRepository
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param MixAmountFactory $mixAmountFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ProcessWinner $processWinner,
        CustomerRepositoryInterface $customerRepository,
        AmountFactory $amountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        MixAmountFactory $mixAmountFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_processWinnerHelper = $processWinner;
        $this->customerRepository = $customerRepository;
        $this->amount = $amountFactory;
        $this->autoAmount = $autoAuctionFactory;
        $this->mixAmount = $mixAmountFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $mix = $this->mixAmount->create()->load($id);
        $bidId = $mix->getBidId();
        if ($mix->getData('is_auto')) {
            $model = $this->autoAmount->create()->load($bidId);
            $auctionType = "auto";
        } else {
            $model = $this->amount->create()->load($bidId);
            $auctionType = "manual";
        }
        if ($model->getEntityId()) {
            //find Winner when auction end time
            $status = $this->_processWinnerHelper->setWinnerManual($model, $auctionType);
            if ($status) {
                $mix->setWinningStatus(1)->save();
                $customer = $this->customerRepository->getById($model->getCustomerId());
                $this->messageManager->addSuccess(
                    __(
                        'You have set %1 to winner this auction id "%2".',
                        $customer->getFirstname().' '.$customer->getLastname(),
                        $model->getAuctionId()
                    )
                );
            } else {
                $this->messageManager->addWarning(__('We can not set auction winning this bid %1 at momment.', $model->getId()));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
