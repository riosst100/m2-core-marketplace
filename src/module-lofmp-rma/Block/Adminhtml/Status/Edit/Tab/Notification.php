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



namespace Lofmp\Rma\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Lofmp\Rma\Api\Repository\StatusRepositoryInterface;

class Notification extends Form
{
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->statusRepository = $statusRepository;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lofmp\Rma\Model\Rule $rule */
        $status = $this->registry->registry('current_status');
         $storeId = (int)$this->getRequest()->getParam('store');
         $fieldset = $form->addFieldset('notification_fieldset', ['legend' => __('Notifications')]);
          $customerMessages = $status->getCustomerMessage();

        if (isset($customerMessages[$storeId])) {
            $customerMessage = $customerMessages[$storeId];
        } else {
            $customerMessage = $customerMessages[0];
        }
          $adminMessages = $status->getAdminMessage();

        if (isset($adminMessages[$storeId])) {
            $adminMessage = $adminMessages[$storeId];
        } else {
            $adminMessage = $adminMessages[0];
        }
           $historyMessages = $status->getHistoryMessage();
        if (isset($customerMessages[$storeId])) {
            $historyMessage = $historyMessages[$storeId];
        } else {
            $historyMessage = $historyMessages[0];
        }
        $fieldset->addField('customer_message', 'textarea', [
            'label'       => __('Customer message '),
            'name'        => 'customer_message',
            'value'       =>  $customerMessage,
            'note'        => __('Notifications email for customer ,leave blank to not send'),
            'scope_label' => __('[STORE VIEW]'),
        ]);

        $fieldset->addField('history_message', 'textarea', [
            'label'       => __('History message'),
            'name'        => 'history_message',
            'value'       => $historyMessage,
            'scope_label' => __('[STORE VIEW]'),
        ]);

        $fieldset->addField('admin_message', 'textarea', [
            'label'       => __('Admin message  '),
            'name'        => 'admin_message',
            'value'       => $adminMessage,
            'note'        => __('Notifications email for admin,leave blank to not send'),
            'scope_label' => __('[STORE VIEW]'),
        ]);

        return parent::_prepareForm();
    }
}
