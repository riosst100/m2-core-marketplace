# Version 1.0.6
- Improve check spam: all bidder will wait to bid after time and limit number of bids on continue time
- Send notification emails to bidder subscriber when auction is comming soon ending
- Fix issues with timezone

# Version 1.0.7
- Add new column: is_subscribed for table lof_auction_mix to check the bider is subscriber or not
- Add console commands: 
```
php bin/magento lofauction:subscriber
```
The command allow convert old bidders to auction subscribers to send notification emails when auctions is ending soon.

# Version 1.0.8
- New feature "Allow Customer set maximum Absentee Bid"
+ These bids will be made automatically.
+ If I bid $100.00 on an item today, I can configure my maximum bid to be $380.00
+ Every time a bid is made, I automatically place an incremental higher bid.
+ So if the item goes to $250 , I automatically bid $255, and so on, . .If the bid goes to $380.00, I am automatically out unless I place a Manual bid OR reconfigure my Maximum bid amount.
- Refactor code for coding standard and fix security. 

# Version 1.0.8.1 - 11/26/2021
- New config allow place absentee bid when customer set max absentee bid
- New graphql queries: get my list subscription auction, set max absentee bid, remove max absentee bid

# Version 1.0.8.2 - 04/01/2022
- Refactor display auction info on product detail
- New module setting "Show full auction info for ending auction" allow show auction info when auction ended

# Version 1.0.9 - 07/15/2022
- Use js component and ajax loading show auction details on product view page
- Use js widget and ajax loading load auction data, bidding history on auction listing page
- Refactor phtml files
- Refactor REST APIs
- Refactor module code to work on magento 2.4.4 - PHP 8
- Fix common issues
- New settings allow check max bids for auction
- New column max_bids auction_product allow set max bids for a product.

# Version 1.0.10 - 08/14/2022
- Fix wrong param name on doc Block for WinnerDataRepositoryInterface
