# Version 1.0.2 - 08/09/2021
- Change controller import csv file, convert value of column price, weight_to, weight_from to float number before save
- fix postal code on query find shipping rates
- remove unuse code
- refactor coding
- add new column for table "lof_marketplace_shippinglist": cost, cart_total
- column cost to calculate shipping cost
- cart_total will been check the current cart total with discount is equal or greater than cart_total value or not, then apply the shipping rates
- support zip code with charactor, number,...
- Updated more in the file: Model/Carrier.php
- New settings "Allow Free shipping for Zero Price", default = No. It allow apply free shipping when min shipping price in rate = 0, else the min shipping price should be greater than 0
- Refactor seller manage table rates shipping, edit rate form, export csv, add new rate, import csv
- Check permission of current seller allow access edit his shipping rates only.

# Version 1.0.3 - 08/31/2021
- Improve Coding Standard
- Compatible with Multi Shipping, Split Cart addon
- Fix some issues with calculator shipping rate for each seller

# Version 1.0.3 - 01/21/2022
- fix common issues
