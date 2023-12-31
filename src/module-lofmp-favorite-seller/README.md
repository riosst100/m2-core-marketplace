# MAGENTO 2 Favorite Sellers

Allow customer follow sellers, add seller to his favorite list. Then will have a page show favorite seller's products.

## Filter Products by Type
1. Featured
- admin should create new product attribute with code: featured with type Dropdown, Yes/No
- seller enabled the attribute Featured for his products.
- then subsriber customers of sellers will display there featured products on site frontend.

2. New Arrival
- Seller should set value "Set Product as New From", "To" Date value to set product as new from,to date
- then subscriber customer of sellers will see new arrival products.

3. Deals
- Seller should set value "Set Special Price for products"
- then subscriber customer of sellers will see deals products.

### seller get 
```
{
  favoritelistSeller(
   filter:{}
  ){
    total_count
    items{
      id
      customer_id
      status
      creation_time
      customer{
        customer_id
        name
      }
    }
  }
}
```
