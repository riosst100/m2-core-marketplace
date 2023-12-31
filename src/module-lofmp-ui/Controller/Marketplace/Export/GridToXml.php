<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lofmp\Ui\Controller\Marketplace\Export;

use Lofmp\Ui\Controller\Marketplace\AbstractUiAction;
use Magento\Framework\App\Action\Context;
use Magento\Ui\Model\Export\ConvertToXml;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
/**
 * Class Render
 */
class GridToXml extends AbstractUiAction
{
    /**
     * @var ConvertToXml
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param ConvertToXml $converter
     * @param FileFactory $fileFactory
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param SellerFactory $sellerFactory
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Url $url
     * @param Filter|null $filter
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ConvertToXml $converter,
        FileFactory $fileFactory,
        Session $customerSession,
        CustomerUrl $customerUrl,
        SellerFactory $sellerFactory,
        PageFactory $resultPageFactory,
        Registry $registry,
        Url $url,
        Filter $filter = null,
        LoggerInterface $logger = null
    ) {
        $this->filter = $filter ?: ObjectManager::getInstance()->get(Filter::class);

        parent::__construct($context, $customerSession, $customerUrl, $this->filter, $url, $sellerFactory);

        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
    }

    /**
     * Export data provider to XML
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        return $this->fileFactory->create('export.xml', $this->converter->getXmlFile(), 'var');
    }

    /**
     * Checking if the user has access to requested component.
     *
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        if ($this->_request->getParam('namespace')) {
            try {
                $component = $this->filter->getComponent();
                $dataProviderConfig = $component->getContext()
                    ->getDataProvider()
                    ->getConfigData();
                if (isset($dataProviderConfig['aclResource'])) {
                    return $this->_authorization->isAllowed(
                        $dataProviderConfig['aclResource']
                    );
                }
            } catch (\Throwable $exception) {
                $this->logger->critical($exception);

                return false;
            }
        }

        return true;
    }
}
