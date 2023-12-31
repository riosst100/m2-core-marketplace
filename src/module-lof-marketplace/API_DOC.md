## REST API doc for marketplace
List API docs of marketplace extension

### Register New Seller Account

Endpoint: ``/V1/seller/register``
Method: POST
Body Params:
```
{
  "customer": {
    "email": "string",
    "firstname": "string",
    "lastname": "string"
  },
  "data": {
    "shop_url": "string",
    "contact_number": "string",
    "company": "string",
    "address": "string",
    "region": "string",
    "region_id": int,
    "group_id": int,
    "city": "string",
    "postcode": "string",
    "country_id": "string",
    "telephone": "string"
  },
  "password": "string"
}
```

Example:

```
{
  "customer": {
    "email": "sellertest123@gmail.com",
    "firstname": "test seller",
    "lastname": "test"
  },
  "data": {
    "shop_url": "my-shop1234",
    "contact_number": "+12334-233-2223",
    "company": "landofcoder",
    "address": "68 nguyen co thach",
    "region": "HN",
    "region_id": 0,
    "group_id": 1,
    "city": "Ha Noi",
    "postcode": "100000",
    "country_id": "VN",
    "telephone": "02334-233-2223"
  },
  "password": "Landofcoder1"
}
```


### Submit seller rating for logged in customer

Endpoint: ``/V1/seller/:sellerUrl/ratings``
Method: POST

Body Params:
```
{
  "rating": {
    "rate1": Int,
    "rate2": Int,
    "rate3": Int,
    "title": String,
    "detail": String,
    "nickname": String,
    "email": String,
    "like_about": String, 
    "not_like_about": String,
    "is_recommended": Boolean
  }
}
```

Response: RatingInterface

Example:

```
{
  "rating": {
    "rate1": 5,
    "rate2": 4,
    "rate3": 4,
    "title": "test seller rating api 1",
    "detail": "detail seller rating api 2",
    "nickname": "tester",
    "email": "tester@gmail.com",
    "like_about": "it is very cool", 
    "not_like_about": "No, Im happy",
    "is_recommended": true
  }
}
```

### Get Seller List Ratings

Endpoint: ``/V1/seller/:sellerUrl/ratings``
Method: GET

Response: 

```
{
    "items: [
        RatingInterface
    ]
    "search_criteria": {
        "filter_groups": [],
        "sort_orders": [
        {
            "field": String,
            "direction": String
        }
        ],
        "page_size": Int,
        "current_page": Int
    },
    "total_count": Int
}
```


Example:

``[Your_Domain_Url]/rest/default/V1/seller/seller2/ratings?searchCriteria%5BsortOrders%5D%5B0%5D%5Bfield%5D=created_at&searchCriteria%5BsortOrders%5D%5B0%5D%5Bdirection%5D=DESC&searchCriteria%5BpageSize%5D=10&searchCriteria%5BcurrentPage%5D=1
``
