<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api\Data;

interface ChatMessageSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ChatMessage list.
     * @return \Lofmp\ChatSystem\Api\Data\ChatMessageInterface[]
     */
    public function getItems();

    /**
     * Set message_id list.
     * @param \Lofmp\ChatSystem\Api\Data\ChatMessageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

