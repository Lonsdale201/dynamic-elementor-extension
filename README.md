# Dynamic Elementor extension
The plugin adds more than 20 dynamic tags to Elementor to display additional woocommerce data. These include product data, customer data, cart values data, (Woo membership compatibility), and customer's own Last order data. Additional custom jetEngine Dynamic visibility conditions

> [!IMPORTANT]
> Minimum PHP version: 7.4

> [!IMPORTANT]
> Minimum Elementor Pro version: 3.17.0

> [!IMPORTANT]
> Minimum Elementor version: 3.17.0

> [!IMPORTANT]
> Minimum WooCommerce version: 7.0.0

> [!Note]
> The plugin supports Woo membership and Subscription and JetEngine.

## INSTRUCTIONS

**How to download?**

In the right section the green button: <>Code click, and in the dropdown menu, select the **Download ZIP** option. The downloaded file just upload it, you can easily install the downloaded file as an plugin within wordpress.

#### Woo Product specific dynamic tags
*You can use with Loop / Listing, or single product templates*

* Advanced Sale Badge
* Advanced Product Category *(dont use product single template or loop!)*
* Advanced Stock
* Featured Badge
* Product Attributes
* Product Gallery Image
* Purchased Badge
* Product Height
* Product Shipping Class
* Product Weight
* Product Width
* Sale Time
* Special Badge
* Stock Quantity
* Stock Quantity Extra

#### Cart specific dynamic tags
*You can use it anywhere in your site*

* Cart Values
* Cart Tax Values
* Free Shipping Amount

#### Customer specific Dynamic tags
*You can use it anywhere in your site*

* Completed Order
* Customer Details (include shipping and billing selectable user meta)
* Total Spent
* Last order (include selectable data)
* My Account menu links
* Purchased Products

#### Woo membership
*You can use it anywhere in your site*

* Active Membership
* Membership Myaccount link

#### Woo Subscriptions
*You can use it anywhere in your site*

* Active Subscription
* Subscriptons myaccount link

#### JetEngine Dynamic Visibility
*You can use it anywhere in your site*

* Access Memberships (Woo Membership)
* Current user any active subscriptions (Woo Subscriptions)
* User Membership Access Can View (Woo Membership)
* User Reviewed Product

#### JetEngine Macros

* WC Current User Purchased Products
* WC Membership Access Posts

### ROADMAP

- [X] v2.0.0 version will migrate the previously created plugin functions: *Elementor Extra Theme Conditions*
- [ ] v2.1.0 will add many new Woo Membership and subscriptions dynamic tags
- [ ] v2.2.0 New Woo Widgets

## CHANGELOG

### V 2.0.1 *2024.03.31.*

* Added total of 12 Elementor Theme conditions were included:
* User roles, user status (logged in, logged out), Product is on sale, Product is out of stock, product is featured, product is variable, product is external, Product is virtual, product is downloadable, product is subscription, Product is purchased by current user.
* New Dynamic Tags: **My Account menu links** (works with the link types like button etc) - work with the custom my account endpoints too (if added via the wc api), **Membership Myaccount link** (need Woo Membership), **Subscriptons myaccount link** (Need Woo Subscriptions plugin), **Advanced Product Category**
* New JetEngine Macros (you can use it with the query builder etc): **WC Current User Purchased Products**, **WC Membership Access Posts**

* Improved **Advanced Sale Badge Dynamic tag**: Now can handle if the product is discounted via Woo membership, and can handle if the product is variable
* Improved **Special badge Dynamic tag** added new External option
* Plugin folder better structure
* Bug fixed: Stock Quantity Extra dynamic tag - If the product is external no longer return the In Stock text value.
* Bug fixed: Advanced Sale Badge fixed the issue cause if the product is variable. 

### V 1.04 *2024.03.23*

* New Dynamic tags: Product Shipping Class
* New JE Dynamic Visibility condition: User Reviewed Product

### V 1.03 - *2024.03.21*

* New Dynamic tags: Purchased Products (can output the titles, linkable titles, or the ids -> ideal for shortcodes
* New Dynamic tags: Purchased Badge

### V 1.02 - *2024.03.21*

* Added missing index
* New Dynamic Tag: Product lenght

### V 1.01 - *2024.03.20*

* NEW Dynamic Visibility (User Membership Access Can View), New Dynamic tag (Active Subscription)

### V 1.0 - *2024.03.20*

* Release the kraken

