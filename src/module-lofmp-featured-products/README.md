# MAGENTO 2 Seller Featured Products

Allow seller add his featured products adn display Featured products slider block on seller detail page on frontend.

## REST API Docs

1. Get list featured products by seller url

Endpoint: `/V1/sellerFeaturedProduct/:sellerUrl/search`
Params:
- sellerUrl : String
- searchCriteria: search criteria

Response:

```
{
    items: {
        ProductData
    },
    search_criteria: {
        SearchCriteria
    },
    total_count: Int
}
```

Example:

``https://demo.landofcoder.com/index.php/rest/all/V1/sellerFeaturedProduct/seller2/search?searchCriteria%5BpageSize%5D=5&searchCriteria%5BcurrentPage%5D=1``
