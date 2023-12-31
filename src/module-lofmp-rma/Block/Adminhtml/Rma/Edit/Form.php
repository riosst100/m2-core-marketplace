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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;

class Form extends WidgetForm
{
    /**
     * Form constructor.
     *
     * @param FormFactory $formFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        FormFactory $formFactory,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;

        parent::__construct($context, $data);
    }

    /**
     * Old exchange amount.
     *
     * @var int
     */
    protected $oldAmount;

    /**
     * New exchange amount.
     *
     * @var int
     */
    protected $newAmount;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $form = $this->formFactory->create()->setData([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);
        /*   $amounts = $this->calculateHelper->calculateExchangeAmounts($this->getRma());

           $this->oldAmount = $amounts['oldAmount'];
           $this->newAmount = $amounts['newAmount'];*/

        return parent::_prepareForm();
    }
}
