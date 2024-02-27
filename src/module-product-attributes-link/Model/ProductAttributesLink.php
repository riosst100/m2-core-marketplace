<?php

namespace CoreMarketplace\ProductAttributesLink\Model;

class ProductAttributesLink extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';
    
    /**
     * Entity Id
     */
    const ENTITY_ID = 'entity_id';
    
    /**
     * BlogManager Blog cache tag.
     */
    const CACHE_TAG = 'coremarketplace_product_attributes_link';
    
    /**
     * @var string
     */
    protected $_cacheTag = 'coremarketplace_product_attributes_link';

    /**
     * @var string
     */
    protected $_eventPrefix = 'coremarketplace_product_attributes_link';
    
    /**
     * Dependency Initilization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink::class);
    }
    
    /**
     * Load object data.
     *
     * @param int $id
     * @param string|null $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoute();
        }
        return parent::load($id, $field);
    }
    
    /**
     * No Route
     *
     * @return $this
     */
    public function noRoute()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get Identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set Id
     *
     * @param int $id
     * @return \CoreMarketplace\ProductAttributesLink\Model\ProductAttributesLink
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}