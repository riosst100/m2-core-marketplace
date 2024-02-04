<?php

namespace CoreMarketplace\SellerIdentificationApproval\Controller\Marketplace\Index;

use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\WebsiteStore;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Sender;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Lof\MarketPlace\Model\ResourceModel\Group\Collection;
use Exception;
use Lofmp\SellerIdentificationApproval\Model\Attachment\UploaderFactory;
use Lofmp\SellerIdentificationApproval\Model\AttachmentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\File\ReadFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    const BASE64_ENCODED_DATA = 'base64_encoded_data';

    const ATTACHMENTS_FOLDER = 'lof_seller';

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var Collection
     */
    private $groupCollection;

    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var Session
     */
    private $sesson;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var SellerHelper
     */
    protected $helperSellerData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $region;

    /**
     * @var Mapper
     */
    protected $customerMapper;

    /**
     * @var \Magento\Customer\Model\CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;
    /**
     * @var ReadFactory
     */
    private $readFactory;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var File
     */
    private $file;
    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @param Context $context
     * @param Session $session
     * @param Seller $seller
     * @param Sender $sender
     * @param Collection $groupCollection
     * @param SellerHelper $sellerHelper
     * @param WebsiteStore $websiteStoreHelper
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param RequestInterface $request
     * @param Mapper $customerMapper
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     * @param \Magento\Directory\Model\Region $region
     * @param \Magento\Framework\Url $frontendUrl
     */
    public function __construct(
        Context $context,
        Session $session,
        Seller $seller,
        Sender $sender,
        Collection $groupCollection,
        SellerHelper $sellerHelper,
        WebsiteStore $websiteStoreHelper,
        DataPersistorInterface $dataPersistor,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor,
        \Magento\Directory\Model\Region $region,
        AttachmentFactory $attachmentFactory,
        ReadFactory $readFactory,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        File $file,
        \Magento\Framework\Url $frontendUrl
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->seller = $seller;
        $this->customerMapper = $customerMapper;
        $this->customerExtractor = $customerExtractor;
        $this->_request = $request;
        $this->groupCollection = $groupCollection;
        $this->helperSellerData = $sellerHelper;
        $this->customerRepository = $customerRepository;
        $this->_sellerHelper = $sellerHelper->getHelperData();
        $this->sender = $sender;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->dataPersistor = $dataPersistor;
        $this->region = $region;
        $this->attachmentFactory = $attachmentFactory;
        $this->readFactory       = $readFactory;
        $this->filesystem        = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->file = $file;
        $this->_frontendUrl = $frontendUrl;
    }

    /**
     * @param $file
     * @param $sellerId
     * @throws FileSystemException
     * @throws Exception
     */
    public function saveSellerAttachment($file, $sellerId)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $fileContent = base64_decode($file->getData(self::BASE64_ENCODED_DATA), true);
        $tmpDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $tmpFileName  = substr(hash('sha256', (rand())), 0, 7) . '.' . $file->getName();
        $tmpDirectory->writeFile($tmpFileName, $fileContent);

        $fileAttributes = [
            'tmp_name' => $tmpDirectory->getAbsolutePath() . $tmpFileName,
            'name'     => $file->getName(),
            'file_type'     => $file->getFileType(),
        ];
        $uploader = $this->uploaderFactory->create();
        $uploader->processFileAttributes($fileAttributes);
        $uploader->addValidateCallback('nameLength', $uploader, 'validateNameLength');
        $uploader->addValidateCallback('size', $uploader, 'validateSize');
        $uploader->setAllowRenameFiles(true)
            ->setFilesDispersion(true)
            ->setAllowCreateFolders(true);
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::ATTACHMENTS_FOLDER);
        $data = $uploader->save($path);

        if (isset($data['name']) && isset($data['file'])) {
            $attachment = $this->attachmentFactory->create();
            $attachment->setSellerId($sellerId)
                ->setFileName($data['name'])
                ->setFilePath($data['file'])
                ->setFileType($file->getType())
                ->setIdentifyType($file->getFileType())
                ->save();
        }
    }

    /**
     * @param $request
     * @return bool
     */
    public function deleteFiles($request)
    {
        $deleteIds = $request->getParam('delete_ids');
        if ($deleteIds) {
            $deleteIds = explode(',', $deleteIds);
            $collection = $this->attachmentFactory->create()
                ->getCollection()
                ->addFieldToFilter(
                    'entity_id',
                    ['in' => $deleteIds]
                );
            if ($collection->getSize()) {
                foreach ($collection as $item) {
                    $item->delete();
                }
            }
        }

        return true;
    }

    /**
     * @param $filesArray
     * @return array
     */
    public function getFiles($filesArray)
    {
        $files = [];
        foreach ($filesArray as $key => $fileTypes) {
            foreach ($fileTypes as $file) {
                if (empty($file['tmp_name'])) {
                    continue;
                }
                $fileContent = $this->readFactory
                    ->create($file['tmp_name'], \Magento\Framework\Filesystem\DriverPool::FILE)
                    ->read($file['size']);
                $fileContent = base64_encode($fileContent);
                $files[] = $this->attachmentFactory->create(
                    [
                        'data' => [
                            'base64_encoded_data' => $fileContent,
                            'type'                => $file['type'],
                            'name'                => $file['name'],
                            'file_type'           => str_replace("-files", "", $key)
                        ],
                    ]
                );
            }
        }
        return $files;
    }
    
    public function execute()
    {
        $sellerId = $this->getRequest()->getParam('seller_id');

        $filesArray = (array)$this->getRequest()->getFiles();

        $files = $this->getFiles($filesArray);
        if ($files) {
            foreach ($files as $file) {
                $this->saveSellerAttachment($file, $sellerId);
            }
        }

        $this->deleteFiles($this->getRequest());
        
        $seller = $this->seller->load($sellerId);
        if ($seller) {
            $seller->setDocumentsVerifyStatus(2);
            $seller->save();
        }

        $this->_redirectUrl($this->getFrontendUrl('marketplace/sellerverification/index'));

        return null;
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
}
