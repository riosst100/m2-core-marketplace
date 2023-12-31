<?php
namespace Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlotGroup;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;

use Magento\Framework\View\Result\PageFactory;

class Save extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $resultPageFactory;
    protected $collection;
    protected $scopeConfig;
    protected $savealotgroup;
    protected $messageManager;
    protected $helperData;
    
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        SellerFactory $sellerFactory,
        Url $url,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lofmp\DeliverySlot\Helper\Data $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lofmp\DeliverySlot\Model\DeliverySlotGroup\SaveslotGroup $savealotgroup,
        \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup\CollectionFactory $collection
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->collection = $collection;
        $this->scopeConfig = $scopeConfig;
        $this->savealotgroup = $savealotgroup;
        $this->messageManager = $messageManager;
        $this->helperData = $helperData;
    }

    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $seller = $this->helperData->getSeller();
            $items['group_information'] = $this->getRequest()->getParams();
            $presentzip = explode(',', $this->getRequest()->getParam('zip_code'));
            $groupId = $this->getRequest()->getParam('group_id');
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($groupId) {
                $collectData = $this->collection->create()->addFieldToSelect('zip_code')->addFieldToFilter('group_id', $groupId);
                $oldzipcode = call_user_func_array('array_merge', $collectData->getData());
                $exitszips = explode(',', $oldzipcode['zip_code']);
                $old = array_intersect($exitszips, $presentzip);
                $newzips = array_diff($presentzip, $old);
                $newzip = $this->checkzipcodeExist($newzips, $seller->getId());
                $newzipcode = array_merge($old, $newzip);
                $updatedzip = implode(',', $newzipcode);
                $result = $this->savealotgroup->update($items['group_information'], $updatedzip, $groupId, $seller->getId());
                $deliveryId = (bool)$result;
                if ($deliveryId) {
                    $this->messageManager->addSuccessMessage('SuccessFully Updated  Group Slots');
                } else {
                    $this->messageManager->addErrorMessage('Unable Update  Group Slots');
                }
            } else {
                $collectionzip = $this->checkzipcodeExist($presentzip, $seller->getId());
                $oldzipcode = implode(',', $collectionzip);
                $result = $this->savealotgroup->save($items['group_information'], $oldzipcode, $seller->getId());
                $deliveryId = (bool)$result;
                if ($deliveryId) {
                    $this->messageManager->addSuccessMessage('SuccessFully Added New Group Slots');
                } else {
                    $this->messageManager->addErrorMessage('Unable Added New Group Slots');
                }
            }
            $resultpage = $resultRedirect->setPath('deliveryslot/deliveryslotgroup/index');
            $returnToEdit = (bool)$this->getRequest()->getParam('back', false);

            if ($returnToEdit) {
                $resultpage = $this->_redirect('deliveryslot/deliveryslotgroup/edit', ['group_id' => $result['group_id']]);
            } else {
                $resultpage = $resultRedirect->setPath('deliveryslot/deliveryslotgroup/index');
            }
            return $resultpage;
        }
        
    }


    public function checkzipcodeExist(array $zipcode, $seller_id = 0)
    {
        $collectionzip = [];
        foreach ($zipcode as $key => $presentzips) {
            $collectData = $this->collection
                ->create()
                ->addFieldToFilter('zip_code', ['like' => '%' . $presentzips . '%'])
                ->addFieldToFilter('seller_id', (int)$seller_id);

            if (empty($collectData->getData())) {
                $collectionzip[] = $presentzips;
            } else {
                $this->messageManager->addSuccessMessage(__('Already this %1  Zipcodes Exists.', $presentzips));
            }
        }
        return $collectionzip;
    }
}
