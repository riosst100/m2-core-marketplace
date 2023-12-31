<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SellerIdentificationApproval\Api\Data;

interface AttachmentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Attachment list.
     * @return \Lofmp\SellerIdentificationApproval\Api\Data\AttachmentInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Lofmp\SellerIdentificationApproval\Api\Data\AttachmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

