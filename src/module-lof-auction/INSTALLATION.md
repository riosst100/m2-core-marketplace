## How to installation

1. Setup module via FTP and run magento 2 commands:

The extension include 2 module: Lof_All, Lof_Auction

- Connect your server with FTP client (example: FileZilla).
- Upload module files in the module packages in to folder: app/code/Lof/Auction , app/code/Lof/All
- Access SSH terminal, then run commands:

```
php bin/magento setup:upgrade --keep-generated
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
```

- To config the module please. Go to admin > Store > Configuration > Landofcoder Extensions > Auction
- To manage subscribers. Go to admin > Landofcoder > Auction
- Create Auction Product before
- Config module as default
- Can create widget Best Auctions on any position and pages with widget type: Lof Auction: Best Auctions
- Customer bidding auction
- Admin set winner for bidding or auto set winner for higher bidding
- Winner of auction will purchase products after time