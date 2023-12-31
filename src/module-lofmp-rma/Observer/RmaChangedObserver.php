<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lofmp\Rma\Api\Repository\StatusRepositoryInterface;

class RmaChangedObserver implements ObserverInterface
{
    /**
     * @var \Lofmp\Rma\Helper\RuleHelper
     */
    protected $ruleHelper;

    /**
     * @var \Lofmp\Rma\Helper\Data
     */
    protected $rmaHelper;

    /**
     * @var StatusRepositoryInterface
     */
    protected $statusRepository;

    /**
     * @var \Lofmp\Rma\Api\Repository\MessageRepositoryInterface
     */
    protected $messageRepository;

    /**
     * @var \Lofmp\Rma\Helper\Mail
     */
    protected $rmaMail;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * RmaChangedObserver constructor.
     *
     * @param \Lofmp\Rma\Helper\RuleHelper $ruleHelper
     * @param \Lofmp\Rma\Helper\Data $rmaHelper
     * @param \Lofmp\Rma\Model\AttachmentFactory $AttachmentFactory
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository
     * @param StatusRepositoryInterface $statusRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Lofmp\Rma\Helper\Mail $rmaMail
     */
    public function __construct(
        \Lofmp\Rma\Helper\RuleHelper $ruleHelper,
        \Lofmp\Rma\Helper\Data $rmaHelper,
        \Lofmp\Rma\Model\AttachmentFactory $AttachmentFactory,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        StatusRepositoryInterface $statusRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Lofmp\Rma\Helper\Mail $rmaMail
    ) {
        $this->ruleHelper = $ruleHelper;
        $this->rmaHelper = $rmaHelper;
        $this->_request = $request;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attachmentFactory = $AttachmentFactory;
        $this->messageRepository = $messageRepository;
        $this->statusRepository = $statusRepository;
        $this->rmaMail = $rmaMail;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rma = $observer->getData('rma');
        $attachments = $this->rmaHelper->getAttachments('return_label', $rma->getId());
        $attachment = array_shift($attachments);
        if (!$attachment) {
            $attachment = $this->attachmentFactory->create();
        }
        $files = $this->_request->getFiles();
        $post = $this->_request->getParams();
        $filesArray = $files->toArray();
        if (count($filesArray) > 0 && (isset($filesArray['return_label']) && $filesArray['return_label']['name'] != '')) {
            if (isset($post['return_label']['delete']) && $post['return_label']['delete']) {
                $attachment->delete();
            }
            $content = @file_get_contents(addslashes($filesArray['return_label']['tmp_name']));
            $type = $filesArray['return_label']['type'];
            $size = $filesArray['return_label']['size'];
            $check = $this->rmaHelper->CheckFile($type, $size);
            if ($check) {
                $attachment
                    ->setItemType('return_label')
                    ->setItemId($rma->getId())
                    ->setName($filesArray['return_label']['name'])
                    ->setSize($size)
                    ->setBody($content)
                    ->setType($type)
                    ->save();
            }
        }
        $this->notifyRmaChange($rma, $observer->getData('user'));
    }

    /**
     * @param $rma
     * @param $user
     */
    public function notifyRmaChange($rma, $user)
    {
        if ($rma->getOrigData('status_id') == null || $rma->getStatusId() != $rma->getOrigData('status_id')) {
            $this->onRmaStatusChange($rma, $user);
        }
        if ($rma->getOrigData('rma_id')) {

            $this->ruleHelper->newEvent(
                'rma_updated',
                $rma
            );
        } else {
            $this->ruleHelper->newEvent(
                'rma_created',
                $rma
            );
        }
    }

    /**
     * @param $rma
     * @param $user
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function onRmaStatusChange($rma, $user)
    {
        $status = $this->statusRepository->getById($rma->getStatusId());
        $historyMessage = $status->getHistoryMessage();
        $customerMessage = $status->getCustomerMessage();
        $adminMessage = $status->getAdminMessage();
        $return_address_html = $rma->getReturnAddressHtml();
        if (!$return_address_html) {
            $rma->setReturnAddressHtml($rma->getReturnAddress());
        }
        if ($historyMessage[0]) {
            $text = $this->rmaMail->parseVariables($historyMessage[0], $rma);

            $params = [
                'isNotified' => $status->getCustomerMessage() != '',
                'isVisible' => 1
            ];

            $message = $this->messageRepository->create();
            $message->setRmaId($rma->getId())
                ->setText($text)
                ->setIsVisibleInFrontend(true)
                ->setIsCustomerNotified(true)
                ->setUserId($rma->getUserId());

            $this->messageRepository->save($message);
        }
        if ($customerMessage[0]) {
            $this->rmaMail->sendNotificationCustomer($rma, $customerMessage[0], true);
        }
        if ($adminMessage[0]) {
            $this->rmaMail->sendNotificationSeller($rma, $adminMessage[0], true);
        }
        if ($customerMessage || $historyMessage) {
            if ($rma->getUserId()) {
                $rma->setLastReplyName($this->rmaHelper->getUserName($rma->getUserId()))
                    ->save();
            }
        }
    }
}
