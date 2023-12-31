<?php
namespace Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlotGroup;

use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup\CollectionFactory;
use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    public function __construct(
        Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lofmp\DeliverySlot\Model\DeliverySlotGroup\SaveslotGroup $savealotgroup,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup\CollectionFactory $collection
    ) {
    
        $this->messageManager = $messageManager;
        $this->collection = $collection;
        $this->scopeConfig = $scopeConfig;
        $this->savealotgroup = $savealotgroup;
        parent::__construct($context);
    }

    public function execute()
    {
        $items['group_information'] = $this->getRequest()->getParams();
        $presentzip = explode(',', $this->getRequest()->getParam('zip_code'));
        $groupId = $this->getRequest()->getParam('group_id');
        $seller_id = isset($items['group_information']['seller_id']) && $items['group_information']['seller_id']?(int)$items['group_information']:null;
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($groupId) {
            $collectData = $this->collection->create()->addFieldToSelect('zip_code')->addFieldToFilter('group_id', $groupId);
            $oldzipcode = call_user_func_array('array_merge', $collectData->getData());
            $exitszips = explode(',', $oldzipcode['zip_code']);
            $old = array_intersect($exitszips, $presentzip);
            $newzips = array_diff($presentzip, $old);
            $newzip = $this->checkzipcodeExist($newzips, $seller_id);
            $newzipcode = array_merge($old, $newzip);
            $updatedzip = implode(',', $newzipcode);
            $result = $this->savealotgroup->update($items['group_information'], $updatedzip, $groupId, 0, true);
            $deliveryId = (bool)$result;
            if ($deliveryId) {
                $this->messageManager->addSuccessMessage('SuccessFully Updated  Group Slots');
            } else {
                $this->messageManager->addErrorMessage('Unable Update Group Slots');
            }
        } else {
            $collectionzip = $this->checkzipcodeExist($presentzip, $seller_id);
            $oldzipcode = implode(',', $collectionzip);
            $result = $this->savealotgroup->save($items['group_information'], $oldzipcode);
            $deliveryId = (bool)$result;
            if ($deliveryId) {
                $this->messageManager->addSuccessMessage('SuccessFully Added New Group Slots');
            } else {
                $this->messageManager->addErrorMessage('Unable Added New Group Slots');
            }
        }
        $resultpage = $resultRedirect->setPath('*/*/index');
        $returnToEdit = (bool)$this->getRequest()->getParam('back', false);

        if ($returnToEdit) {
            $resultpage = $this->_redirect('deliveryslot/deliveryslotgroup/edit', ['group_id' => $result['group_id']]);
        } else {
            $resultpage = $resultRedirect->setPath('*/*/index');
        }
        return $resultpage;
    }


    public function checkzipcodeExist(array $zipcode, $seller_id = null)
    {
        $collectionzip = [];
        foreach ($zipcode as $key => $presentzips) {
            $collectData = $this->collection->create()->addFieldToFilter('zip_code', ['like' => '%' . $presentzips . '%']);
            if($seller_id){
                $collectData->addFieldToFilter('seller_id', (int)$seller_id);
            }
            if (empty($collectData->getData())) {
                $collectionzip[] = $presentzips;
            } else {
                $this->messageManager->addSuccessMessage(__('Already this %1  Zipcodes Exists.', $presentzips));
            }
        }
        return $collectionzip;
    }
}
