<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2014 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SmtpEmail\Block\Adminhtml;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Lof_SmtpEmail::menu.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                 'emaillog' => [
                    'title' => __('Emails log'),
                    'url' => $this->getUrl('*/emaillog/index'),
                    'resource' => 'Lof_SmtpEmail::emaillog'
                ],
                'emaildebug' => [
                    'title' => __('Emails debug'),
                    'url' => $this->getUrl('*/emaildebug/index'),
                    'resource' => 'Lof_SmtpEmail::emaildebug'
                ],
                'blacklist' => [
                    'title' => __('Blacklist'),
                    'url' => $this->getUrl('*/blacklist/index'),
                    'resource' => 'Lof_SmtpEmail::blacklist'
                ],
                'create_blacklist' => [
                    'title' => __('New Blacklist'),
                    'url' => $this->getUrl('*/blacklist/new'),
                    'resource' => 'Lof_SmtpEmail::blacklist_new'
                ],
                'blockip' => [
                    'title' => __('Blockip'),
                    'url' => $this->getUrl('*/blockip/index'),
                    'resource' => 'Lof_SmtpEmail::blockip'
                ],
                'create_blockip' => [
                    'title' => __('New Blockip'),
                    'url' => $this->getUrl('*/blockip/new'),
                    'resource' => 'Lof_SmtpEmail::blockip_new'
                ],
                'spam' => [
                    'title' => __('Spam'),
                    'url' => $this->getUrl('*/spam/index'),
                    'resource' => 'Lof_SmtpEmail::spam'
                ],
                'create_spam' => [
                    'title' => __('New Blockip'),
                    'url' => $this->getUrl('*/spam/new'),
                    'resource' => 'Lof_SmtpEmail::spam_new'
                ],
                'settings' => [
                    'title' => __('Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit/section/lofsmtpemail'),
                    'resource' => 'Lof_SmtpEmail::settings'
                ],
                'readme' => [
                    'title' => __('Guide'),
                    'url' => 'http://guide.landofcoder.com/smtp-email/',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'https://landofcoder.ticksy.com',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]
            ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }

        return $this->items;
    }

    /**
     * @return array
     */
    public function getCurrentItem()
    {

        $items = $this->getMenuItems();

        $controllerName = $this->getRequest()->getControllerName();

        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }

        return $items['page'];
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }
        return $result;
    }

    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }
}
