<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface BlockipSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Blockip list.
     * @return \Lof\SmtpEmail\Api\Data\BlockipInterface[]
     */
    public function getItems();

    /**
     * Set ip list.
     * @param \Lof\SmtpEmail\Api\Data\BlockipInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

