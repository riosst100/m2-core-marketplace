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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Model\Attachment;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Math\Random;
use Lof\Quickrfq\Helper\Data as HelperConfig;

/**
 * Class Uploader
 * @package Lof\Quickrfq\Model\Attachment
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
    private $defaultNameLength = 20;


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
            $configData = $this->helperConfig->getAllowedExtensions();
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
        return isset($this->_file['size'])
            && $this->_file['size'] < $this->helperConfig->getMaxFileSize() * $this->defaultSizeMultiplier;
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
    public static function getNewFileName($destinationFile)
    {
        /** @var Random $random */
        $random = ObjectManager::getInstance()->get(Random::class);

        return $random->getRandomString(32);
    }
}
