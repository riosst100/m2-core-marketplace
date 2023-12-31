<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Seller\Product;

use Magento\Framework\App\RequestInterface;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Head constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param RequestInterface $request
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_productFactory = $productFactory;
        $this->_assetRepo = $assetRepo;
    }

    /**
     * @param $type_id
     * @return mixed
     */
    public function getProductFromType($type_id)
    {
        return $this->_productFactory->load($type_id);
    }

    /**
     * @return \Magento\Cms\Model\Wysiwyg\Config
     */
    public function getWysiwygConfig()
    {
        return $this->_wysiwygConfig;
    }

    /**
     * @param $path
     * @return string
     */
    public function getAssetRepoUrl($path)
    {
        return $this->_assetRepo->getUrl($path);
    }

    /**
     * Get Editor Config JSON Data
     * @return string
     */
    public function getEditorConfig()
    {
        $config = $this->_wysiwygConfig->getConfig();
        $config = json_encode($config->getData());
        return $config;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->request->getParam('id');
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        $product = '';
        if ($this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
        }
        return $product;
    }

    /**
     * @return mixed
     */
    public function getTypeProduct()
    {
        return $this->request->getParam('type');
    }
}
