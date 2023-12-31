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
namespace Lofmp\Rma\Helper;

class RuleHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Lofmp\Rma\Api\Repository\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Model\CustomerFactory                   $customerFactory,
        \Magento\User\Model\UserFactory                           $userFactory,
        \Lofmp\Rma\Helper\Mail $rmaMail,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->productFactory         = $productFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->objectManager          = $objectManager;
        $this->sortOrderBuilder       = $sortOrderBuilder;
        $this->ruleRepository         = $ruleRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->rmaMail                = $rmaMail;
        $this->customerFactory        = $customerFactory;
        $this->userFactory            = $userFactory;
        $this->context                = $context;

        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $sentEmails = [];

    /**
     * @var array
     */
    protected $processedEvents = [];

    /**
     * @param string                  $eventType
     * @param \Lofmp\Rma\Model\Rma $rma
     * @return void
     */
    public function newEvent($eventType, $rma)
    {
        $key = $eventType.$rma->getId();
        if (isset($this->processedEvents[$key])) {
            return;
        } else {
            $this->processedEvents[$key] = true;
        }

        $this->sentEmails = [];
        $rules = $this->getEventRules($eventType);
        /** @var \Lofmp\Rma\Model\Rule $rule */
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if (!$rule->validate($rma)) {
                continue;
            }
            $this->processRule($rule, $rma);
            if ($rule->getIsStopProcessing()) {
                break;
            }
        }
    }

    /**
     * @return \Magento\Framework\Api\AbstractSimpleObject
     */
    protected function getSortOrder()
    {
        return $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventRules($eventType)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addFilter('event', $eventType)
            ->addSortOrder($this->getSortOrder())
        ;

        return $this->ruleRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param \Lofmp\Rma\Model\Rule $rule
     * @param \Lofmp\Rma\Model\Rma  $rma
     * @return void
     */
    protected function processRule($rule, $rma)
    {
        /* set attributes **/
        if ($rule->getStatusId()) {
            $rma->setStatusId($rule->getStatusId());
        }
        if ($rule->getUserId()) {
            $rma->setUserId($rule->getUserId());
        }

        $rma->save();

        /* send notifications **/
        if ($rule->getIsSendOwner()) {
            /** @var \Magento\User\Model\User $user */
            if ($user = $this->userFactory->create()->load($rma->getUserId())) {
                $this->_sendEventNotification($user->getEmail(), $user->getName(), $rule, $rma);
            }
        }
        if ($rule->getIsSendUser()) {
            /** @var \Magento\Customer\Model\Customer $customer */
            if ($customer = $this->customerFactory->create()->load($rma->getCustomerId())) {
                $this->_sendEventNotification($customer->getEmail(), $customer->getName(), $rule, $rma);
            }
        }
        if ($otherEmail = $rule->getOtherEmail()) {
            $this->_sendEventNotification($otherEmail, '', $rule, $rma);
        }
    }

    /**
     * @param string                   $email
     * @param string                   $name
     * @param \Lofmp\Rma\Model\Rule $rule
     * @param \Lofmp\Rma\Model\Rma  $rma
     * @return void
     */
    protected function _sendEventNotification($email, $name, $rule, $rma)
    {
        if (!is_array($this->sentEmails) || !in_array($email, $this->sentEmails)) {
            $this->rmaMail->sendNotificationRule($email, $name, $rule, $rma);
            $this->sentEmails[] = $email;
        }
    }

    /**
     * @var array
     */
    protected $operatorInputByType = [
        'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}'],
        'numeric'     => ['==', '!=', '>=', '>', '<=', '<'],
        'date'        => ['==', '>=', '<='],
        'select'      => ['==', '!='],
        'boolean'     => ['==', '!='],
        'multiselect' => ['{}', '!{}', '()', '!()'],
        'grid'        => ['()', '!()'],
    ];

    /**
     * @var array
     */
    protected $operatorOptions = [
        '=='  => 'is',
        '!='  => 'is not',
        '>='  => 'equals or greater than',
        '<='  => 'equals or less than',
        '>'   => 'greater than',
        '<'   => 'less than',
        '{}'  => 'contains',
        '!{}' => 'does not contain',
        '()'  => 'is one of',
        '!()' => 'is not one of',
    ];

    /**
     * @param string $name
     * @param string $current
     * @param string $style
     * @param null   $tags
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAttributeSelectHtml($name, $current, $style, $tags = null)
    {
        $options = [];
        $options['-'][] = '<option value="">'.__('not set').'</option>';

        $id = preg_replace('/[^a-zA-z_]/', '_', $name);

        $html = '<select name="'.$name.'" id="'.$id.'" style="'.$style.'" '.$tags.'>';
        foreach ($options as $group => $items) {
            if ($group == '-') {
                $html .= implode('', $items);
            } else {
                $html .= '<optgroup label="'.$group.'">';
                $html .= implode('', $items);
                $html .= '</optgroup>';
            }
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * @param string $name
     * @param null   $current
     * @param null   $attributeCode
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getConditionSelectHtml($name, $current = null, $attributeCode = null)
    {
        $conditions = [];

        if ($attributeCode != null) {
            $entityTypeId = $this->productFactory->create()->getResource()->getTypeId();
            $attribute = $this->entityAttributeFactory->create()->loadByCode($entityTypeId, $attributeCode);
            $type = 'string';
            if ($attributeCode === 'attribute_set_id') {
                $type = 'select';
            } elseif ($attributeCode === 'tracker') {
                $type = 'numeric';
            } else {
                switch ($attribute->getFrontendInput()) {
                    case 'select':
                        $type = 'select';
                        break;

                    case 'multiselect':
                        $type = 'multiselect';
                        break;

                    case 'date':
                        $type = 'date';
                        break;

                    case 'boolean':
                        $type = 'boolean';
                        break;

                    default:
                        $type = 'string';
                }
            }

            foreach ($this->operatorInputByType[$type] as $operator) {
                $operatorTitle = __($this->operatorOptions[$operator]);
                $selected = $current == $operator ? 'selected="selected"' : '';
                $conditions[] = '<option '.$selected.' value="'.$operator.'">'.$operatorTitle.'</option>';
            }
        }

        return '<select style="width:100px" name="'.$name.'">'.implode('', $conditions).'</select>';
    }

    /**
     * @param string $name
     * @param string $current
     * @param null   $tags
     *
     * @return string
     */
    public function getOutputTypeHtml($name, $current, $tags = null)
    {
        $element = $this->objectManager->create('Magento\Framework\Data\Form\Element\Select');
        $element
            ->setForm(new \Magento\Framework\DataObject())
            ->setValue($current)
            ->setName($name)
            ->addData($tags)
            ->setValues([
                'pattern' => __('Pattern'),
                'attribute' => __('Attribute Value'),
            ]);

        return $element->getElementHtml();
    }

    /**
     * @param string $name
     * @param null   $current
     * @param null   $attribute
     * @param null   $tags
     *
     * @return string
     */
    public function getAttributeValueHtml($name, $current = null, $attribute = null, $tags = null)
    {
        $html = '';

        $attribute = $this->productFactory->create()->getResource()->getAttribute($attribute);
        if ($attribute) {
            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                $options = [];

                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $selected = '';
                    if ($option['value'] == $current) {
                        $selected = 'selected="selected"';
                    }
                    $options[] = '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
                }

                $html = '<select style="width:250px" name="'.$name.'" '.$tags.'>';
                $html .= implode('', $options);
                $html .= '</select>';
            }
        }

        if (!$html) {
            $html = '<input style="width:244px" class="input-text" type="text" name="'.$name.'" value="'.$current.'">';
        }

        return $html;
    }

    /**
     * @param string $name
     * @param null   $value
     *
     * @return string
     */
    public function getFormattersHtml($name, $value = null)
    {
        $element = $this->objectManager->create('Magento\Framework\Data\Form\Element\Select');
        $element
            ->setForm(new \Magento\Framework\DataObject())
            ->setValue($value)
            ->setName($name)
            ->setValues([
                '' => __('Default'),
                'intval' => __('Integer'),
                'price' => __('Price'),
                'strip_tags' => __('Strip Tags'),
            ]);

        return $element->getElementHtml();
    }

    /**
     * @param string $attributeCode
     *
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeGroup($attributeCode)
    {
        $group = '';

        $primary = [
            'attribute_set',
            'attribute_set_id',
            'entity_id',
            'full_description',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'name',
            'short_description',
            'description',
            'sku',
            'status',
            'url',
            'url_key',
            'visibility',
        ];

        $stock = [
            'is_in_stock',
            'qty',
        ];

        $price = [
            'tax_class_id',
            'special_from_date',
            'special_to_date',
            'cost',
            'msrp',
        ];

        if (in_array($attributeCode, $primary)) {
            $group = __('Primary Attributes');
        } elseif (in_array($attributeCode, $stock)) {
            $group = __('Stock Attributes');
        } elseif (in_array($attributeCode, $price) || strpos($attributeCode, 'price') !== false) {
            $group = __('Prices & Taxes');
        } elseif (strpos($attributeCode, 'image') !== false || strpos($attributeCode, 'thumbnail') !== false) {
            $group = __('Images');
        } elseif (substr($attributeCode, 0, strlen('custom:')) == 'custom:') {
            $group = __('Custom Attributes');
        } elseif (substr($attributeCode, 0, strlen('mapping:')) == 'mapping:') {
            $group = __('Mapping');
        } elseif (strpos($attributeCode, 'category') !== false) {
            $group = __('Category');
        } elseif (strpos($attributeCode, 'lofmeta') !== false) {
            $group = __('Landofcoder Meta Tags');
        } else {
            $group = __('Others Attributes');
        }

        return $group;
    }
}
