## Public API

1. Get RMA address

Endpoint: /V1/lofmp-returns/address
Method: GET
Params:

Response:
```
{
  "items": [
    {
      "name": "string",
      "sort_order": 0,
      "seller_id": 0,
      "address": "string",
      "is_active": true,
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```
2. Get RMA Condition

Endpoint: /V1/lofmp-returns/condition
Method: GET
Params:
Response:
```
{
  "items": [
    {
      "name": "string",
      "sort_order": 0,
      "is_active": true,
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```

3. Get RMA Resolution

Endpoint: /V1/lofmp-returns/resolution
Method: GET
Param:
Response:
```
{
  "items": [
    {
      "name": "string",
      "sort_order": 0,
      "is_active": true,
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```

4. Get RMA Reason

Endpoint: /V1/lofmp-returns/reason
Method: GET
Param:
Response:
```
{
  "items": [
    {
      "name": "string",
      "sort_order": 0,
      "is_active": true,
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```

## Customer API

1. Submit new RMA request

Endpoint: /V1/lofmp-returns/me/rma
Method: POST
Params:
```
{
  "rma": {
    "rma_id": int,
    "order_id": int,
    "seller_id": int,
    "return_address": "string",
    "items: [
        {
            "item_id" : int
            "order_item_id" : int
            "reason_id" : int,
            "resolution_id" : int,
            "condition_id": int,
            "qty_requested": int
        }
    ]
  }
}
```

2. Get my RMA request

Endpoint: /V1/lofmp-returns/me/item
Method: GET
Params: searchCriteria
  Example: searchCriteria[pageSize]=5&searchCriteria[currentPage]=1
Response:
```
{
  "items": [
    {
      "rma_id": 0,
      "order_item_id": 0,
      "product_id": 0,
      "order_id": 0,
      "reason_id": 0,
      "resolution_id": 0,
      "condition_id": 0,
      "qty_requested": 0,
      "qty_returned": 0,
      "created_at": "string",
      "updated_at": "string",
      "name": "string",
      "seller_commission": "string",
      "admin_commission": "string",
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```

3. Get items from RMA with rma_id

Endpoint: /V1/lofmp-returns/me/itembyrma/{rmaId}
Method: GET
Params: searchCriteria
  Example: searchCriteria[pageSize]=5&searchCriteria[currentPage]=1

Response:
```
{
  "items": [
    {
      "rma_id": 0,
      "order_item_id": 0,
      "product_id": 0,
      "order_id": 0,
      "reason_id": 0,
      "resolution_id": 0,
      "condition_id": 0,
      "qty_requested": 0,
      "qty_returned": 0,
      "created_at": "string",
      "updated_at": "string",
      "name": "string",
      "seller_commission": "string",
      "admin_commission": "string",
      "id": 0
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "string",
            "value": "string",
            "condition_type": "string"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "string",
        "direction": "string"
      }
    ],
    "page_size": 0,
    "current_page": 0
  },
  "total_count": 0
}
```
