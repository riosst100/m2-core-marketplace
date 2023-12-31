<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Controller\Marketplace\Rule;

class SaveRule extends \Lofmp\CouponCode\Controller\Marketplace\Rule {

    public function execute() {
        $customerSession = $this->_getSession();
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId,'customer_id');
        $status = $seller?$seller->getStatus():0;
        
        if ($customerSession->isLoggedIn() && $status == 1) {
                $websiteId = $this->_getStoreManager()->getStore()->getWebsiteId();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $data = $this->getRequest()->getPostValue();
                if ($data) {
                    $model = $objectManager->get('Lofmp\CouponCode\Model\Rule');
                    $model = $this->_objectManager->create('Lofmp\CouponCode\Model\Rule');
                    $model_sale_rule = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

                    $id = $this->getRequest()->getParam('coupon_rule_id');

                    if ($id) {
                        $model->load($id);
                        $sale_rule_id = $model->getRuleId();
                        $model_sale_rule->load($sale_rule_id);
                        if (($id != $model->getId()) || ($seller->getId() != $model->getSellerId())) {
                            throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
                        }
                    }
                    $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                    $validateResult = $model_sale_rule->validateData(new \Magento\Framework\DataObject($data));
                    if ($validateResult !== true) {
                        foreach ($validateResult as $errorMessage) {
                            $this->messageManager->addError($errorMessage);
                        }
                        $session->setPageData($data);
                        
                        if($id){
                            $this->_redirect('lofmpcouponcode/*/edit', ['id' => $model->getId()]);
                        }else{
                            $this->_redirect ('lofmpcouponcode/rule/new');
                        }
                        return;
                    }
                    if (isset(
                        $data['simple_action']
                    ) && $data['simple_action'] == 'by_percent' && isset(
                        $data['discount_amount']
                    )
                    ) {
                        $data['discount_amount'] = min(100, $data['discount_amount']);
                    }

                    if (isset($data['rule']['conditions'])) {
                        $data['conditions'] = $data['rule']['conditions'];
                    } 
                    if (isset($data['rule']['actions'])) {
                        $data['actions'] = $data['rule']['actions'];
                    }
                    if(empty($data['coupons_generated'])){
                        $data['coupons_generated'] = 0;
                    }
                    $data['seller_id'] = $seller->getId();
                    $data['coupon_type'] = '2';
                    $data['use_auto_generation'] = '1';
                    $data['uses_per_customer'] = '0';
                    $data['website_ids'] = $model->getId()?$model->getWebsiteIds():[];
                    $data['website_ids'] = is_array($data['website_ids'])?$data['website_ids']:[(int)$data['website_ids']];
                    if (!in_array($websiteId, $data['website_ids'])) {
                        $data['website_ids'] = $websiteId;
                    }
                    $data['actions'] = isset($data['actions']) ? $data['actions']: $model_sale_rule->getActionsSerialized();
                    $data["actions"] = $this->helperData->generateActionCondition($data["actions"], $data['seller_id']);
                    try {
                        $model_sale_rule->loadPost($data);
                        $model->setData($data);
                        $model_sale_rule->save();
                        $model->setData('rule_id', $model_sale_rule->getId());
                        $model->setData('name', $model_sale_rule->getName());
                        $model->save();

                        $id = $model->getId();
                        $this->messageManager->addSuccess(__('You saved the Rule.'));
                        
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                    if ($this->getRequest()->getParam("back")) {
                        $this->_redirect ('lofmpcouponcode/rule/edit', ["coupon_rule_id" => $id]);
                    } else {
                        $this->_redirect ('lofmpcouponcode/rule/index');
                    }
            } elseif($customerSession->isLoggedIn() && $status == 0) {
                $this->_redirectUrl ( $this->getFrontendUrl('lofmpcouponcode/rule/index'));
            } else {
                $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
                $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
            }
        }
    }
}
