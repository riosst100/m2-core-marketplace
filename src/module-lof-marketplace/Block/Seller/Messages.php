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

namespace Lof\MarketPlace\Block\Seller;

use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Message;

class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * @var
     */
    protected $interpretationStrategy;

    /**
     * Messages constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Message\InterpretationStrategyInterface $interpretationStrategy
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Message\InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->interpretationStrategy = $interpretationStrategy;
        $this->addMessages($this->messageManager->getMessages(true));
    }

    /**
     * Render messages in HTML format grouped by type
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _renderMessagesByType()
    {
        $html = '';
        foreach ($this->getMessageTypes() as $type) {
            if ($messages = $this->getMessagesByType($type)) {
                if (!$html) {
                    $html .= '<' . $this->firstLevelTagName . ' class="messages">';
                }
                $messateType = $type;
                switch ($type) {
                    case MessageInterface::TYPE_ERROR:
                        $title = __("Error!");
                        $class = 'fa-ban';
                        break;
                    case MessageInterface::TYPE_NOTICE:
                        $title = __("Info");
                        $class = 'fa-info';
                        $messateType = 'info';
                        break;
                    case MessageInterface::TYPE_SUCCESS:
                        $title = __("Success");
                        $class = 'fa-check';
                        break;
                    case MessageInterface::TYPE_WARNING:
                        $title = __("Alert");
                        $class = 'fa-warning';
                        break;
                    default:
                        $title = __("Info");
                        $class = 'fa-info';
                        break;
                }
                foreach ($messages as $message) {
                    $html .= '<' . $this->secondLevelTagName
                        . ' class="alert ' . 'alert-' . $messateType . ' ' . $type . ' alert-dismissable">';
                    $html .= '<' . $this->contentWrapTagName . $this->getUiId('message', $type) . '>';
                    $html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">';
                    $html .= '&times;</button>';
                    $html .= '<h4><i class="icon fa ' . $class . '"></i> ' . $title . '</h4>';
                    $html .= $this->interpretationStrategy->interpret($message);
                    $html .= '</' . $this->contentWrapTagName . '>';
                    $html .= '</' . $this->secondLevelTagName . '>';
                }
            }
        }
        if ($html) {
            $html .= '</' . $this->firstLevelTagName . '>';
        }

        return $html;
    }
}
