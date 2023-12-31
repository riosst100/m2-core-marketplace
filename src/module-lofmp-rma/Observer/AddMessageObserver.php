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

use Lofmp\Rma\Model\Config;

use Magento\Framework\Event\ObserverInterface;

class AddMessageObserver implements ObserverInterface
{
    /**
     * @var \Lofmp\Rma\Api\Repository\AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @var \Lofmp\Rma\Helper\Mail
     */
    protected $rmaMail;

    /**
     * @var \Lofmp\Rma\Helper\Data
     */
    protected $rmaHelper;

    /**
     * @var \Lofmp\Rma\Helper\RuleHelper
     */
    protected $ruleHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * AddMessageObserver constructor.
     *
     * @param \Lofmp\Rma\Helper\Mail $rmaMail
     * @param \Lofmp\Rma\Helper\RuleHelper $ruleHelper
     * @param \Lofmp\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository
     * @param \Lofmp\Rma\Helper\Data $rmaHelper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Lofmp\Rma\Helper\Mail $rmaMail,
        \Lofmp\Rma\Helper\RuleHelper $ruleHelper,
        \Lofmp\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository,
        \Lofmp\Rma\Helper\Data $rmaHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->rmaMail = $rmaMail;
        $this->ruleHelper = $ruleHelper;
        $this->rmaHelper = $rmaHelper;
        $this->attachmentRepository = $attachmentRepository;
        $this->_request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rma = $observer->getData('rma');
        $user = $observer->getData('user');
        $message = $observer->getData('message');
        $params = $observer->getData('params');
        $files = $this->_request->getFiles();
        $filesArray = $files->toArray();
        if (count($filesArray) > 0 && (isset($filesArray['attachment']) && count($filesArray['attachment']) > 0)) {
            foreach ($filesArray['attachment'] as $index => $file) {
                if (!$file || $file['name'] == '' || (isset($filesArray['attachment'][$index]['is_saved']) && !empty($filesArray['attachment'][$index]['is_saved']))) {
                    continue;
                }

                $type = $file['type'];
                $size = $file['size'];
                $check = $this->rmaHelper->CheckFile($type, $size);
                if (!$check) {
                    continue;
                }
                $attachment = $this->attachmentRepository->create();
                $content = @file_get_contents(addslashes($file['tmp_name']));

                $attachment
                    ->setItemType('message')
                    ->setItemId($message->getId())
                    ->setName($file['name'])
                    ->setSize($size)
                    ->setBody($content)
                    ->setType($type)
                    ->save();

                $filesArray['attachment'][$index]['is_saved'] = 1;//need to check
            }
        }
        if ($user instanceof \Magento\User\Model\User) {
            if ($message->getIsCustomerNotified()) {
                $this->rmaMail->sendNotificationCustomer($rma, $message);
            }
            if ($rma->getUserId() != $user->getId() && !$message->getIsVisibleInFrontend()) {
                $this->rmaMail->sendNotificationSeller($rma, $message);
            }
            $this->ruleHelper->newEvent(
                'new_customer_reply',
                $rma
            );
        } else {
            if (isset($params['isNotifyAdmin']) && $params['isNotifyAdmin']) {
                $this->rmaMail->sendNotificationSeller($rma, $message);
            }
            if ($message->getIsCustomerNotified()) {
                $this->rmaMail->sendNotificationCustomer($rma, $message);
            }
            $this->ruleHelper->newEvent(
                'new_customer_reply',
                $rma
            );
        }
    }
}
