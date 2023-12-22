
<?php
declare(strict_types=1);

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class NewAction extends \CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Create new link page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('CoreMarketplace_ProductAttributesLink::product_attributes_link');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Attributes Link'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Link'));

        return $resultPage;
    }
}
