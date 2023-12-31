<?php
namespace Lofmp\Quickrfq\Block\Adminhtml\Rfq;

use Lof\Quickrfq\Model\Attachment;
use Lof\Quickrfq\Model\Quickrfq;
use Lof\Quickrfq\Model\ResourceModel\Message\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Layout
 *
 * @package Lofmp\Quickrfq\Block\Adminhtml\Rfq
 */
class Layout extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Collection
     */
    private $messageCollection;
    /**
     * @var UrlInterface
     */
    private $_urlInterface;
    /**
     * @var Attachment
     */
    private $attachment;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Message constructor.
     * @param Context $context
     * @param UrlInterface $urlInterface
     * @param Collection $messageCollection
     * @param Quickrfq $quickrfq
     * @param Attachment $attachment
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlInterface,
        Collection $messageCollection,
        Quickrfq $quickrfq,
        Attachment $attachment,
        ProductRepository $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quickrfq = $quickrfq;
        $this->attachment = $attachment;
        $this->_urlInterface = $urlInterface;
        $this->productRepository = $productRepository;
        $this->messageCollection = $messageCollection;
    }

    /**
     * @return mixed
     */
    public function getQuoteId()
    {
        return $this->getRequest()->getParam('quickrfq_id');
    }

    /**
     * @return Quickrfq
     */
    public function getQuote()
    {
        return $this->quickrfq->load($this->getQuoteId());
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    public function getProduct()
    {
        $productId = $this->getQuote()->getProductId();
        try {
            $product = $this->productRepository->getById($productId);
            return ! empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

}
