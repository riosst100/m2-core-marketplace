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

namespace Lof\Quickrfq\Controller;

class FileProcessor
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    private $attachmentFactory;

    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    private $readFactory;

    /**
     * @param \Magento\Framework\App\RequestInterface        $request
     * @param \Magento\Framework\Filesystem\File\ReadFactory $readFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Lof\Quickrfq\Model\AttachmentFactory $attachmentFactory,
        \Magento\Framework\Filesystem\File\ReadFactory $readFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->request           = $request;
        $this->readFactory       = $readFactory;
    }

    public function getFiles()
    {
        $filesArray = (array) $this->request->getFiles('files');
        $files      = [];
        foreach ($filesArray as $file) {
            if (empty($file['tmp_name'])) {
                continue;
            }
            $fileContent = $this->readFactory
                ->create($file['tmp_name'], \Magento\Framework\Filesystem\DriverPool::FILE)
                ->read($file['size']);
            $fileContent = base64_encode($fileContent);
            $files[]     = $this->attachmentFactory->create(
                [
                    'data' => [
                        'base64_encoded_data' => $fileContent,
                        'type'                => $file['type'],
                        'name'                => $file['name'],
                    ],
                ]
            );
        }

        return $files;
    }
}
