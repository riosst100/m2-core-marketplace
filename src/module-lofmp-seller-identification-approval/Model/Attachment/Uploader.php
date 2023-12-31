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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Model\Attachment;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Math\Random;
use Lofmp\SellerIdentificationApproval\Helper\Data as HelperConfig;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Uploader extends \Magento\Framework\Api\Uploader
{

    /**
     * @var HelperConfig
     */
    private $helperConfig;

    /**
     * This number is used to convert Mbs in bytes.
     *
     *
     * @var int
     */
    private $defaultSizeMultiplier = 1048576;

    /**
     * Default file name length.
     *
     * @var int
     */
    private $defaultNameLength = 90;

    /**
     * Uploader constructor.
     * @param HelperConfig $helperConfig
     */
    public function __construct(
        HelperConfig $helperConfig
    ) {
        parent::__construct();
        $this->helperConfig = $helperConfig;
    }

    /**
     * Check is file has allowed extension.
     *
     * @inheritdoc
     */
    public function checkAllowedExtension($extension)
    {
        if (empty($this->_allowedExtensions)) {
            $type = $this->_file['file_type'];
            $configData = $this->helperConfig->getAllowedExtensions($type);
            $allowedExtensions = $configData ? explode(',', $configData) : [];
            $this->_allowedExtensions = $allowedExtensions;
        }
        return parent::checkAllowedExtension($extension);
    }

    /**
     * Validate size of file.
     *
     * @return bool
     */
    public function validateSize()
    {
        $type = $this->_file['file_type'];

        return isset($this->_file['size'])
            && $this->_file['size'] < $this->helperConfig->getMaxFileSize($type) * $this->defaultSizeMultiplier;
    }

    /**
     * Validate name length of file.
     *
     * @return bool
     */
    public function validateNameLength()
    {
        return mb_strlen($this->_file['name']) <= $this->defaultNameLength;
    }

    /**
     * @inheritDoc
     */
    //phpcs:ignore Magento2.Functions.StaticFunction.StaticFunction
    public static function getNewFileName($destinationFile)
    {
        /** @var Random $random */
        $random = ObjectManager::getInstance()->get(Random::class);

        return $random->getRandomString(32);
    }
}
