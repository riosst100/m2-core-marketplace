<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Slider\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Slider Model
 */
class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Lofmp\Slider\Model\ResourceModel\Slider
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\Model\Context                          $context
     * @param \Magento\Framework\Registry                               $registry
     * @param \Lofmp\Slider\Model\ResourceModel\Slider|null               $resource
     * @param \Lofmp\Slider\Model\ResourceModel\Slider\Collection|null    $resourceCollection
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\Slider\Model\ResourceModel\Slider $resource,
        \Lofmp\Slider\Model\ResourceModel\Slider\Collection $resourceCollection,
        array $data = []
    ) {
        $this->_resource = $resource;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

	/**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Lofmp\Slider\Model\ResourceModel\Slider');
    }

    /**
     * update status by id
     *
     * @param int $id
     * @return void
     */
    public function updateStatus($id)
    {
        $connection = $this->_resource->getConnection();
        $update = 'UPDATE ' . $this->_resource->getTable('lofmp_marketplace_slider') . ' SET is_active = 0 WHERE slider_id <> ' .$id;
        $connection->query($update);
    }
}
