# Mage2 Module Lof MarketplaceGraphQl

    ``landofcoder/module-marketplace-graphql
``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
magento 2 marketplace graphql extension

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_MarketplaceGraphQl`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-marketplace-graphql`
 - enable the module by running `php bin/magento module:enable Lof_MarketplaceGraphQl`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### TODO
- Refactor Graphql queries
- Refactor Resolvers
- Add documendation for Graphql queries

## Queries

1. Get Seller Profile Info By Url

```
{
    sellerByUrl(
        seller_url: String! @doc(description: "Seller Url Key")
        get_other_info: Boolean = false @doc(description: "Get other info: reviews, ratings, total sales, vacation")
        get_products: Boolean = false @doc(description: "Get Latest Products")
    ) {
        address
        banner_pic
        city
        company_description
        company_locality
        contact_number
        country
        customer_id
        email
        gplus_active
        gplus_id
        group
        image
        name
        page_layout
        region
        return_policy
        sale
        seller_id
        shipping_policy
        shop_title
        status
        store_id
        thumbnail
        seller_rates {
            items {
                created_at
                customer_id
                detail
                email
                nickname
                rate1
                rate2
                rate3
                rating_id
                seller_id
                status
                title
            }
            total_count
        }
  }
}
```

Example:

```
{
  sellerByUrl(seller_url: "seller1") {
    shop_title
    thumbnail
    logo_pic
    banner_pic
    url_key
    telephone
    product_count
    total_sold
    offers
    benefits
    product_shipping_info
    prepare_time
    response_ratio
    response_time
    creation_time
  }
}

```

2. Get Seller Profile By ID

```
{
    sellerById(
        seller_id: Int! @doc(description: "Seller id")
        get_other_info: Boolean = false @doc(description: "Get other info: reviews, ratings, total sales, vacation")
        get_products: Boolean = false @doc(description: "Get Latest Products")
    ) {
        address
        banner_pic
        city
        company_description
        company_locality
        contact_number
        country
        customer_id
        email
        gplus_active
        gplus_id
        group
        image
        name
        page_layout
        region
        return_policy
        sale
        seller_id
        shipping_policy
        shop_title
        status
        store_id
        thumbnail
        seller_rates {
            items {
                created_at
                customer_id
                detail
                email
                nickname
                rate1
                rate2
                rate3
                rating_id
                seller_id
                status
                title
            }
            total_count
        }
  }
}
```

Example:

```
{
  sellerById(seller_id: 1) {
    shop_title
    thumbnail
    logo_pic
    banner_pic
    url_key
    telephone
    product_count
    total_sold
    offers
    benefits
    product_shipping_info
    prepare_time
    response_ratio
    response_time
    creation_time
  }
}

```


3. Get List Sellers with Filter options

```
{
    sellers (
        filter: SellerFilterInput,
        pageSize: Int = 20,
        currentPage: Int = 1,
        sort: SellerSortInput
    ) {
        total_count
        items {
            seller_rates {
                items {
                    created_at
                    customer_id
                    detail
                    email
                    nickname
                    rate1
                    rate2
                    rate3
                    rating_id
                    seller_id
                    status
                    title
                }
                total_count
            }
            sale
            seller_id
            name
            thumbnail
            country
            address
            group
            products {
                items {
                    sale
                    id
                    name
                    url_key
                    rating_summary
                    sku
                    image {
                        url
                        label
                    }
                    description {
                        html
                    }
                    short_description {
                        html
                    }
                    product_brand
                    price_range {
                        maximum_price {
                            discount {
                                amount_off
                                percent_off
                            }
                            final_price {
                                currency
                                value
                            }
                            regular_price {
                                currency
                                value
                            }
                        }
                        minimum_price {
                            discount {
                                amount_off
                                percent_off
                            }
                            final_price {
                                currency
                                value
                            }
                            regular_price {
                                currency
                                value
                            }
                        }
                    }
                    price {
                        regularPrice {
                            amount {
                                currency
                            }
                        }
                    }
                }
                total_count
            }
        }
    }
}
```

4. Get Seller Collection (Groups)

```
{
    sellerCollection(
        filter: SellerGroupFilterInput,
        pageSize: Int = 5,
        currentPage: Int = 1,
        sort: SellerGroupSortInput
    ) {
        total_count
        items {
            group_id
            name
            url_key
            position
        }
    }
}
```

5. Filter products by seller ID

```
fragment ShopProduct on ProductInterface {
  id
  rating_summary
  description {
    html
  }
  name
  image {
    url
  }
  url_key
  price_range {
    minimum_price {
      regular_price {
        value
        currency
      }
    }
    maximum_price {
      discount {
        percent_off
      }
      final_price {
        value
        currency
      }
      regular_price {
        value
      }
    }
  }
}

fragment PageInfo on SearchResultPageInfo {
  current_page
  page_size
  total_pages
}

productsBySellerId(
    seller_id: Int!
    search: String = ""
    filter: ProductAttributeFilterInput
    pageSize: Int = 20
    currentPage: Int = 1
    sort: ProductAttributeSortInput
  ) {
    items {
      ...ShopProduct
    }
    page_info {
      ...PageInfo
    }
    total_count
}
```

6. Get Seller Products by Seller Url

Example: get products of seller "seller1"

```
{
  productsBySellerUrl(
    seller_url: "seller1"
    search: ""
    filter: {}
    pageSize: 1
    currentPage: 1
  ) {
    items {
      id
      name
      url_key
      rating_summary
      sku
      stock_status
      image {
        url
        label
      }
      description {
        html
      }
      short_description {
        html
      }
      price {
        regularPrice {
          amount {
            currency
          }
        }
      }
    }
    page_info {
      page_size
      current_page
      total_pages
    }
    total_count
  }
}
```

7. Get Seller Information on products query

Example:

```
{
  products(filter: { sku: { eq: "AAAU_B2C-W713327" } }) {
    items {
      name
      sku
      url_key
      stock_status
      price_range {
        minimum_price {
          regular_price {
            value
            currency
          }
        }
      }
      seller {
        shop_title
        thumbnail
        url_key
        telephone
        product_count
        total_sold
        offers
        benefits
        product_shipping_info
        prepare_time
        response_ratio
        response_time
        creation_time
      }
    }
    total_count
    page_info {
      page_size
    }
  }
}

```

8. Mutation Registrer Seller

```
mutation {
  registerSeller(
    input: {
        seller: {
            group_id: String
            url_key: String!
        },
        customer: {
            firstname: String!
            lastname: String!
            email: String!
            address: {
                region_id: String
                country_id: String!
                city: String!
                street: String!
                company: String
                telephone: String!
                postcode: String!
            }
        },
        password: String!
    }
  ) {
    code
    message
  }
}
```

9. Mutation Become Seller - required customer logged in

```
mutation {
  becomeSeller(
    input: {
        group_id: String
        url_key: String!
    }
  ) {
    code
    message
  }
}
```

10. Mutation Review Seller - required customer logged in

```
mutation {
  reviewSeller(
    input: {
        seller_url  : String!
        rate1  : Int!
        rate2  : Int!
        rate3  : Int!
        nickname  : String!
        email  : String!
        title  : String!
        detail  : String!
    }
  ) {
    code
    message
  }
}
```

11. Mutation Customer send contact to seller - required customer logged in

```
mutation {
  customerSendMessage(
    input: {
        seller_url  : String!
        subject  : String
        content  : String!
    }
  ) {
    code
    message
  }
}
```

12. Mutation Customer send reply to a message - required customer logged in

```
mutation {
  customerReplyMessage(
    input: {
        message_id  : Int!
        content  : String!
    }
  ) {
    code
    message
  }
}
```

13. Query get messages of logged in customer - required customer logged in

```
{
    sellerMessages(
        filter: SellerMessageFilterInput
        pageSize: Int = 20
        currentPage: Int = 1
        sort: SellerMessageSortInput
    ) {
        total_count
        items {
            message_id
            description
            subject
            sender_email
            sender_name
            created_at
            status
            is_read
            sender_id
            owner_id
            receiver_id
            seller_send
            details (
                pageSize: Int = 20
                currentPage: Int = 1
            ) {
                items {
                    content
                    sender_name
                    sender_email
                    receiver_name
                    is_read
                    created_at
                }
                total_count
                page_info {
                    ...PageInfo
                }
            }
        }
        page_info {
            ...PageInfo
        }
    }
}
```

14. Query Get Seller Info of Logged in Customer - required customer logged in

```
{
  customer {
    firstname
    lastname
    suffix
    email
    addresses {
      firstname
      lastname
      street
      city
      region {
        region_code
        region
      }
      postcode
      country_code
      telephone
    }
    seller {
        shop_title
        thumbnail
        logo_pic
        banner_pic
        url_key
        telephone
        product_count
        total_sold
        offers
        benefits
        product_shipping_info
        prepare_time
        response_ratio
        response_time
        creation_time
    }
  }
}
```

15. Get Seller Ratings List by Seller Url Key

Query:

```
{
  sellerRatings(
    seller_url: "seller1"
    filter: {}
    pageSize: 5
    currentPage: 1
    sort: { created_at: DESC }
  ) {
    items {
      rating_id
      rate1
      rate2
      rate3
      rate4
      rate5
      rating
      title
      status
      detail
      nickname
      created_at
      verified_buyer
      is_recommended
      is_hidden
      answer
      admin_note
      like_about
      not_like_about
      guest_email
      plus_review
      minus_review
      report_abuse
      country
    }
    total_count
    page_info {
      page_size
      current_page
      total_pages
    }
  }
}
```
