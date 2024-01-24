<?php

namespace CoreMarketplace\SellerIdentificationApproval\Block\Adminhtml\Seller\Edit\Tab;

use Lofmp\SellerIdentificationApproval\Block\Seller\Register;
use Lofmp\SellerIdentificationApproval\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Design\Theme\LabelFactory;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;
use Magento\Theme\Model\Layout\Source\Layout;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class IdentificationVerification extends \Lofmp\SellerIdentificationApproval\Block\Adminhtml\Seller\Edit\Tab\IdentificationVerification
{
    /**
     * @var LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var Layout
     */
    protected $_pageLayout;

    /**
     * @var BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Lof\MarketPlace\Model\MessageAdmin
     */
    protected $message;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $message_detail;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Layout $pageLayout
     * @param LabelFactory $labelFactory
     * @param BuilderInterface $pageLayoutBuilder
     * @param Data $helper
     * @param \Lof\MarketPlace\Model\MessageDetail $message_detail
     * @param \Lof\MarketPlace\Model\MessageAdmin $message
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Layout $pageLayout,
        LabelFactory $labelFactory,
        BuilderInterface $pageLayoutBuilder,
        Data $helper,
        \Lof\MarketPlace\Model\MessageDetail $message_detail,
        \Lof\MarketPlace\Model\MessageAdmin $message,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        $this->helper = $helper;
        $this->message = $message;
        $this->message_detail = $message_detail;
        parent::__construct(
            $context, 
            $registry, 
            $formFactory, 
            $pageLayout,
            $labelFactory,
            $pageLayoutBuilder,
            $helper,
            $data
        );
    }

    /**
     * Prepare form tab configuration
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    //phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Initialise form fields
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Identification Verification')]);

        // $fieldset->addField(
        //     'identification_request',
        //     'multiselect',
        //     [
        //         'label' => __('Identification Requested'),
        //         'title' => __('Identification Requested'),
        //         'name' => 'identification',
        //         'values' => $this->getOptions()
        //     ]
        // );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Verification Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses()
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Register::class)
                ->setTemplate('CoreMarketplace_SellerIdentificationApproval::seller/verification.phtml')
                ->setNameInLayout('additional_fields')
        );

        $this->getChildHtml('additional_fields');

        // $commentModel = $this->_coreRegistry->registry('lof_marketplace_message');

        // $fieldset = $form->addFieldset('comment_fieldset', ['legend' => __('Comments')]);

        // if ($commentModel->getId()) {
        //     $fieldset->addField('message_id', 'hidden', ['name' => 'message_id']);
        // }
        // // if (!$commentModel->getDescription()) {
        // //     $fieldset->addField(
        // //         'partner_id',
        // //         'select',
        // //         [
        // //             'name' => 'partner_id',
        // //             'label' => __('Seller'),
        // //             'title' => __('Seller'),
        // //             'required' => false,
        // //             'values' => [
        // //                 [
        // //                     'value'=> $model->getId()
        // //                 ]
        // //             ],
        // //             'disabled' => $isElementDisabled
        // //         ]
        // //     );
        // // }

        // if ($commentModel->getSubject()) {
        //     $fieldset->addField(
        //         'subject',
        //         'note',
        //         [
        //             'name' => ' subject',
        //             'label' => __('Subject'),
        //             'text' => $commentModel->getSubject()
        //         ]
        //     );
        // } else {
        //     $fieldset->addField(
        //         'subject',
        //         'text',
        //         ['name' => 'subject', 'label' => __('Subject'), 'title' => __('Subject'), 'required' => true]
        //     );
        // }
        // // if ($commentModel->getDescription()) {
        // //     $fieldset->addField(
        // //         'description',
        // //         'note',
        // //         [
        // //             'name' => ' description',
        // //             'label' => __('Description'),
        // //             'text' => $commentModel->getDescription()
        // //         ]
        // //     );
        // // } else {
        // //     $fieldset->addField(
        // //         'description',
        // //         'textarea',
        // //         [
        // //             'name' => ' description',
        // //             'label' => __('Description'),
        // //         ]
        // //     );
        // // }
        // $fieldset->addField(
        //     'message',
        //     'textarea',
        //     [
        //         'name' => 'message',
        //         'label' => __('Message'),
        //         'title' => __('Message'),
        //         'after_element_html' => $this->_getMessageContentAfterHtml($commentModel->getId(), $commentModel->getSellerId())
        //     ]
        // );

        // $form->setValues($commentModel->getData());

        $this->setForm($form);

        return $this;
    }

    /**
     * @param $message_id
     * @param $seller_id
     * @return string
     */
    protected function _getMessageContentAfterHtml($message_id, $seller_id)
    {
        if ($message_id) {
            $_messsage = $this->message->getCollection()
                ->addFieldToFilter('seller_id', $seller_id)
                ->addFieldToFilter('is_read', 0);
            foreach ($_messsage as $key => $_msg) {
                $_msg->setData('is_read', 1)->save();
            }
            $message = $this->message_detail->getCollection()
                ->addFieldToFilter('message_id', $message_id)
                ->addFieldToFilter('message_admin', 1);
            $data = '';
            foreach ($message as $key => $_message) {
                if ($_message->getData('seller_send')) {
                    $name = $_message->getData('sender_name');
                    $class = 'user';
                } else {
                    $name = $_message->getData('sender_name');
                    $class = '';
                }
                $data .= '<div class="lof-ticket-history">';
                $data .= '<div class="lof-message">';
                $data .= '<div class="lof-message-header">';
                $data .= '<strong>' . $name . '</strong>';
                $createdAt = $_message->getCreatedAt();
                $data .= '<span class="minor">'
                    . __('added %1 (%2)', $this->helper->nicetime($createdAt), $createdAt) . '</span>';
                $data .= '</div>';
                $data .= '<div class="lof-message-body ' . $class . '">';
                $data .= $_message->getContent();
                $data .= '</div>';
                $data .= '</div>';
                $data .= '</div>';
            }

            return $data;
        }

        return '';
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Identification Verification');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Identification Verification');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getOptions()
    {
        $values = [];

        $options = $this->helper->getIdentificationTypes();
        foreach ($options as $key => $option) {
            if (!$this->helper->isEnable($key)) {
                unset($options[$key]);
            }

            $values[] = [
                'label' => $option,
                'value' => $key
            ];
        }

        return $values;
    }
}
