<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api\Data;

interface ChatSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Chat list.
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface[]
     */
    public function getItems();

    /**
     * Set chat_id list.
     * @param \Lofmp\ChatSystem\Api\Data\ChatInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

