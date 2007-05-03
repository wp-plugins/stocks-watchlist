=== Plugin Name ===
Contributors: domfos
Tags: stocks, finance, money
Requires at least: 1.5.0
Tested up to: 2.1.2
Stable tag: 1.1

Use WP to manage your stocks watchlist. Publish your watchlist on your blog with live prices from Yahoo Finance.

== Description ==

Manage your stocks watchlist with your WP database. Your watchlist is managed in a new DB table and configured in it's own page in the Manage menu of the Admin area.

Publish your watchlist anywhere on your blog with 'live' prices from the Yahoo Finance website. 

Your web server needs to be configured for cURL to work if you want the prices to be retrieved from Yahoo Finance.


== Installation ==

1. Upload `stocks-watchlist.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the options in the Manage->Stocks Watchlist admin page
4. Publish your watchlist on your blog (best in the sidebar) with the code <?php 

== Frequently Asked Questions ==

= Where are the stock symbols stored? =

The stock symbols are stored in the WP database.

= Do I need to mess around with the WP database to get it working? =

No, the plugin checks if the Stocks Watchlist table exists. If it doesn't then the table is added automatically.

= What do I need to configure to get the live prices from Yahoo Finance? =

Nothing, except add your stock symbols to the database. There is a PHP Class in the plugin that does this automatically. Just make sure you spell the stocks symbol correctly.

= How do I get my Stocks Watchlist to publish to my blog? =

Add this code to your template - &lt;?php swl_output() ?&gt; - where you want the watchlist to show.

= Will you be updating the plugin with any cool features? =

I'd love to. Just let me know what you want added and I'll give it some consideration :)


== Screenshots ==
http://www.traderknowledge.com/wp-content/uploads/2007/04/stocks-watchlist-plugin.PNG
http://www.traderknowledge.com/wp-content/uploads/2007/04/stocks-watchlist-admin.thumbnail.PNG