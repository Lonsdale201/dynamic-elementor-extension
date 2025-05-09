# Dynamic Elementor extension

Stable tag: 2.3.2

This plugin adds over 50 dynamic tags to Elementor, covering WooCommerce, Woo Membership, Woo Subscriptions, and LearnDash.
In addition to tags, it extends JetEngine's dynamic visibility conditions and introduces several new macros. It also registers new Elementor theme conditions, mostly focused on WooCommerce product specifics, with some more general conditions included as well. As a bonus, it enhances the Elementor Finder with new quick-access features.

## Github updates

We've updated the full source codes of the plugin on github, so you can now always download the latest version directly from there, but we've also started to introduce the release system, also on github.

> [!IMPORTANT]
> Minimum PHP version: 8.0

> [!IMPORTANT]
> Minimum Elementor version: 3.22.0

> [!Note]
> The plugin supports Woo membership, Subscription, JetEngine, LearnDash, Name Your Pricem WooCommerce Tab manager (by SkyVerge) and Product Bundles for WooCommerce.

> [!Note]
> Most of functions, like dynamic tags work without Elementor Pro

---

> [!CAUTION]
> To use this plugin you need 1 required plugin: Elementor (free)

---

## INSTRUCTIONS

**How to download?**

1, method In the right section the green button: **<>Code** click, and in the dropdown menu, select the **Download ZIP** option. The downloaded file just upload it, you can easily install the downloaded file as an plugin within wordpress.

2, method You can download directly to the latest release. You can access this from the right sidebar in the release section.

---

## Tutorials, How to & help

Full help and descriptions can be found on github WIki: https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here

---

#### Woo Product specific dynamic tags

_You can use with Loop / Listing, or single product templates_

- Advanced Sale Badge
- Advanced Product Category _(dont use product single template or loop!)_
- Advanced Stock
- Advanced Price
- Featured Badge
- Product Attributes
- Product Gallery Image
- Purchased Badge
- Product Height
- Product Shipping Class
- Product Tabs (Default / WooCommerce Tab Manager plugin (By SkyVerge))
- Product Weight
- Product Width
- Sale Time
- Special Badge
- Stock Quantity
- Stock Quantity Extra
- Next Product
- Next Product Image
- Previous Product
- Previous Product Image
- Variable price
- ACF Taxonomy Meta

#### Cart specific dynamic tags

_You can use it anywhere in your site_

- Cart Values
- Cart Tax Values
- Free Shipping Amount

#### Customer specific Dynamic tags

_You can use it anywhere in your site_

- Completed Order
- Customer Details (include shipping and billing selectable user meta)
- Total Spent
- Last order (include selectable data)
- My Account menu links
- Purchased Products
- User Role
- Customer logout (link)

#### Woo membership

_You can use it anywhere in your site_

- Active Membership
- Active Membership Data
- Current Membership Data
- Restricted Products View (beta)
- Membership Myaccount link

#### Woo Subscriptions

_You can use it anywhere in your site_

- Active Subscription
- Active Subscription Data
- Subscriptons myaccount link

#### LearnDash (Course)

_You can use with Loop / Listing, or single post/course templates_

- Access Expires
- Awarded on Completion
- Course Access Type
- Certificates Link
- Course Materials
- Course Prerequisites List
- Course Price
- Progress Percentage
- Course Resume URL
- Course Resume Text
- Course Start Date
- Course Status
- Students Number
- Last Activity
- Lessons Number
- Quiz Numbers
- Required Points for Access
- Student Limit
- Topics Numbers
- Course Content
- Course Part of Groups

#### LearnDash Global

_You can use it anywhere in your site_

- User Achieved Certificates Count
- User Enrolled Courses Count
- User Completed Courses Count
- User Course Points
- User Groups Count

---

#### JetEngine Dynamic Visibility

_You can use it anywhere in your site_

- Access Memberships (Woo Membership)
- Current Membership Expired (Woo Membership) (only for JetEngine listing)
- Has Active Subscriptions With Selected Statuses (Woo Subscriptions) (Improved!)
- User Membership Access Can View (Woo Membership)
- User Reviewed Product
- User Registration Time Elapsed

**LeanrDash specific**

- Course Access Type
- Course Certificates Available (not for user)
- Course Not Enrolled
- Course Enrolled
- Not Have Enough Points
- Course Reached Student Limit
- Course Completed
- Course Not started
- Course Part of Any Group
- Course part of group (selectable)

**Memberpress specific**

- Access memberships

**WordPress specific**

- Check URL PATH
- Is Front Page
- Current User Role

---

#### JetEngine Callbacks (for dynamic field widget)

- Convert Units

---

#### JetEngine Macros

- WC Current User Purchased Products
- WC Membership Access Posts
- WC Membership Access All Posts
- WC Membership Discounted Products
- WC Membership Access All Products
- WC Current User Active Memberships

- WC Loop Products (Use Wc Product Query -> useful to build a complete archive query)

**LearnDash specific**

- LD Course Access Type Query
- LD Course Prerequisites Query
- LD User Courses
- User groups (Will return all groups, that associated to the current user)

---

#### Elementor Finder

What is the Elementor finder?
https://elementor.com/help/the-finder/

Type WooCommerce, and you will see a new WooCommerce management category with new actions like create new product, category, shipping zone, class etc. Also type Shipping, or Payment, and you will see all your available shipping and payment methods with status, and instance id
![image](https://github.com/Lonsdale201/dynamic-elementor-extension/assets/23199033/f6c87169-cd74-42a1-b67e-d306e5b8ca5a)

JetEngine & LearnDash specific finder elements:

For jetEngine

- New Query
- New CPT
- New Taxonomy
- New Meta Boxes
- Shortcode generator
- Macro generator

For LearnDash

- New course
- New Lesson
- New Topics
- New quiz
- New group
- Submitted Essays

---

#### Wp Toolbar product info

![image](https://github.com/Lonsdale201/dynamic-elementor-extension/assets/23199033/7762702e-8700-4d3e-9c9a-8ebf0095bd3d)
This is a small addition that can be useful for development and testing. You can get more important information about a product without having to enter the editor or admin (obviously you have to be logged in to the toolbar)

#### Wp Toolbar Learndash info



---

#### Elementor Theme Conditions

For product type template

- Is Product Out Of Stock
- Is Product Virtual
- Is Product Downloadable
- Is Product External
- Is Product Featured
- Is Name Your Price (only if you are using name Your price plugin)
- Is Product On Sale
- Is Product Individually Sold
- Is Purchased By User
- Is Product Subscription (Only if you are using the Woo Subscriptions official plugin)
- Is product Bundle (Product Bundles for WooCommerce plugin)
- Is Product Variable

Other Theme Conditions

- Current User Not Have Active Subscriptions
- User Logged In
- User Logged Out
- By User role types

### ROADMAP

- [x] v2.0.0 version will migrate the previously created plugin functions: _Elementor Extra Theme Conditions_
- [x] v2.1.0 will add many new Woo Membership and subscriptions dynamic tags and settings page
- [x] v2.2.0 Merge to exiting Leanrdash Extension
- [x] v2.3 Removed the WooCommerce dependency
- [ ] 2.4 New Widgets

## CHANGELOG

### V 2.3.2 _2025.03.21_

* Fixed the **Course Resume** and **Course Resume Text** problems, now they work correctly even if the course has not started yet
* Improved the **Lessons Number** dynamic tag (learndash), now it can be set to take into account topics and aggregate them with lessons
* New JetEngine Macro: Current User Role
* New JetEngine Dynamic Visibility for Learndash: Course Not started
* New JetEngine Dynamic Visibility for global usage: User Registration Time Elapsed

---

### V 2.3.1 _2025.02.14_

* Fixed the Advanced price dynamic tag WC_Tax::calc_tax issue

---

### V 2.3 _2025.02.12_

- **Removed the WooCommerce dependency**

- New Dynamic tag for woocommerce: Advanced price
- New Dynamic Visibility - **Memberpress** - Access memberships
- New Elementor Theme conditions for woocommerce: **Is Product Individually Sold**
- New elementor Theme Conditions for woocommerce: **Is product Bundle** _(Product Bundles for WooCommerce plugin)_

- Improvements _Spec badge dynamic tag_ support: Woo Subs, Woo Bundle, and Sale product

- New JetEngine dynamic field callback: **Convert Units**

- Backend updates
  New badge if new functions avaliable
  New Settings design

---

### V 2.2.5 _2025.01.30_

- New jetengine Dynamic Visibility for Learndash: Course part of group
- New Learndash Wp toolbar option. You can enable it in the Plugins settings page.

---

### V 2.2.4 _2024.12.10_

New JetEngine Macros

- WC Membership Access All Posts
    <details>
    <summary>Details</summary>
    
    This macro considers all active memberships of the current user and returns the posts configured within those memberships. It is not limited by post type; all posts specified in the membership rules are returned.
    
    </details>
- WC Membership Access All Products
    <details>
    <summary>Details</summary>
    
    This macro returns all products available to the user, based on their active memberships. You can configure it to return only purchasable products, view-only products, or both types.
    
    </details>
- WC Membership Discounted Products
    <details>
    <summary>Details</summary>
    
    This macro similarly returns all products from the user's active memberships that are eligible for membership discounts. It only includes products that are actively enabled within the memberships.
    
    </details>

**improvments**

**Learndash Course Access Type Dynamic tag**

if the design was done that way, the status indicator could be confusing, because it only gives you the access mode of the course. essentially, if you turn on the custom format option, you have the option to display a different message if someone already has access to the course, for example: "you have already purchased" or "you already have access". So this label can be used in both ways.

**Better LoadtextDomain**

---

### V 2.2.3 _2024.11.14._

- Fixed php 8.3 compatibility issue

---

### V 2.2.2 _2024.11.02._

**New Plugin Support: WooCommerce Tab Manager (By Skyverge)**

New Dynamic Tags, JetEngine Macros, JetEngine Dynamic Visibility, and improvements

**LearDash related:**
New Dynamic tag

- User Groups Count
- Course Part of Groups

**Course Content dynamic tag new improvments:**

New Trim function, output format option

**WooCommerce related:**
New Dynamic tag

- Product Tabs (Support defaults, and the WooCommerce Tab Manager plugin (By: SkyVerge)

**JetEngine related:**
New Dynamic Visibility

- Check URL PATH
- Is Front Page

**JetEngine & LearnDash related:**
New Dynamic Visibility

- Course Part of Any Group

New Macro

- User groups

**Improvments**

Renamed some Elements

| Old Names                         |          New Names           |                Plugin / Type |
| --------------------------------- | :--------------------------: | ---------------------------: |
| User Completed Current Course     |       Course Completed       | JetEngine Dynamic Visibility |
| Current user enrolled course      |       Course Enrolled        | JetEngine Dynamic Visibility |
| User Has Not Enrolled this Course |     Course Not Enrolled      | JetEngine Dynamic Visibility |
| Reached Student Limit             | Course Reached Student Limit | JetEngine Dynamic Visibility |
| Awarded Points                    |    Awarded on Completion     |     Elementor / Dynamic Tags |
| Required Points                   |  Required Points for Access  |     Elementor / Dynamic Tags |

**Fixed**

- Fixed the Wikipedia links, and added new for each sections in the settings page.
- Fixed the Learndash Global section

---

### V 2.2.1 _2024.11.01._

New Learndash dynamic tag

Course Content - This dynamic tag retrive a raw course content, without other course informations

---

### V 2.2.0 _2024.10.28._

#### Major UPDATE!

We merged a previous plugin **(Dynamic Learndash for Elementor),** so now this plugin provides dynamic tags and other add-ons related to Learndash

New Dynamic tags for WooCommerce

- Variable price

**Brand new Dynamic tags for Learndash:**

- Access Expires
- Awarded Points
- Course Access Type
- Certificates Link
- Course Materials
- Course Prerequisites List
- Course Price
- Progress Percentage
- Course Resume URL
- Course Resume Text
- Course Start Date
- Course Status
- Students Number
- Last Activity
- Lessons Number
- Quiz Numbers
- Required Points
- Student Limit
- Topics Numbers

- User Achieved Certificates Count
- User Enrolled Courses Count
- User Completed Courses Count
- User Course Points

**New Learndash specific JetEngine Macros:** (useful for the Query Builder)

- LD Course Access Type Query
- LD Course Prerequisites Query
- LD User Courses

**New WooCommerce specific JetEngine Macros:**

- WC Loop Products (Use Wc Product Query -> useful to build a complete archive query)

**New Learndash specific JetEngine Dynamic Visibility conditions:**

- Course Access Type
- Course Certificates Available (not for user)
- User Has Not Enrolled this Course
- Current user enrolled course
- Not Have Enough Points
- Reached Student Limit
- User Completed Current Course

**New WooCOmmerce Theme Conditions with Name Your price plugin**

- Is Name Your Price

New JetEngine & LearnDash specific finder elements:

For jetEngine

- New Query
- New CPT
- New Taxonomy
- New Meta Boxes
- Shortcode generator
- Macro generator

For LearnDash

- New course
- New Lesson
- New Topics
- New quiz
- New group
- Submitted Essays

**Improvments**

Lots of code refactored

-- Product attributes dynamic tag
Removed the ":" colon, and added two new function:
_ Now you can add your own separator after the label (when using Label/value output) default set to : for the backward compatibility
_ New linkable switcher added

-- Advanced Product Category dynamic tag

- New option: Hide the default uncategorized category from the list
- New Back to shop text option added

**FIXES**

Fixed the ACF Taxonomy Meta dynamic tag issue if acf no exist

**Other**

- New Required Elementor version: 3.17.0 to -> 3.22.0
- New Required Woo version: 7.0.0 to -> 9.0.0
- No longer Support php 7.4. Only up to 8.0+
- New wp 6.0+ textdomain loading method
- Plugin settings page renamed from **Dynamic Extension** to **Dynamic Elements**
- Changed the manage_cap to Admin role checker (upon request) for the Settings page

---

### V 2.1.2 _2024.04.30._

- Improved the Advanced product Category dynamic tag now supporting multi level display
- Added new product status option in the wp toolbar (I.e. post status) display.

---

### V 2.1.1 _2024.04.26._

- New features: In the settings, now you can insert html, text or shortcode and append to the woo myaccount dashboard (before, or after), and Orders (before or after)
- Added new dynamic tag: Customer logout (link)
- Imporved: Added a New items count option in the Last order dynamic tag

---

### V 2.1.0 _2024.04.16._

**New features**

- Complete new Settings page to enable disable dynamic tags & others
- Added one new theme conditions: Current User Not Have Active Subscriptions
- Added new jetengine macros: WC Current User Active Memberships

**Improved exiting Dynamic visibility:** prev name: Current user any active subscriptions | New name: Has Active Subscriptions With Selected Statuses (Woo subs with selectable status)

**New Dynamic Visibility:** Current Membership Expired (Use with jetengine listing + Query builder)

**Extend Elementor finder:**
Added WooCommerce management direct actions: Create new - Products, categories, tags, attributes, coupon, shipping zone, shipping class, order

- Shipping method list and link (with marking of active / inactive status, and id)
- Payment methods (with marking of active / inactive status and id)

**New Dynamic tags:** Current user role, Next product, Prev product, Next product image, prev product image, Active Membership data (selectable Plans), Current membership datas, Current subscriptions data, Restricted Products View

**Improved**
Added WooCommerce, and Elementor Dependency
Added settings link, and documentation link in the plugin meta.

Bugfix:
Fixed the Missing elementor plugin issue

---

### V 2.0.1 _2024.03.31._

- Added total of 12 Elementor Theme conditions were included:
- User roles, user status (logged in, logged out), Product is on sale, Product is out of stock, product is featured, product is variable, product is external, Product is virtual, product is downloadable, product is subscription, Product is purchased by current user.
- New Dynamic Tags: **My Account menu links** (works with the link types like button etc) - work with the custom my account endpoints too (if added via the wc api), **Membership Myaccount link** (need Woo Membership), **Subscriptons myaccount link** (Need Woo Subscriptions plugin), **Advanced Product Category**
- New JetEngine Macros (you can use it with the query builder etc): **WC Current User Purchased Products**, **WC Membership Access Posts**

- Improved **Advanced Sale Badge Dynamic tag**: Now can handle if the product is discounted via Woo membership, and can handle if the product is variable
- Improved **Special badge Dynamic tag** added new External option
- Plugin folder better structure
- Bug fixed: Stock Quantity Extra dynamic tag - If the product is external no longer return the In Stock text value.
- Bug fixed: Advanced Sale Badge fixed the issue cause if the product is variable.

---

### V 1.04 _2024.03.23_

- New Dynamic tags: Product Shipping Class
- New JE Dynamic Visibility condition: User Reviewed Product

---

### V 1.03 - _2024.03.21_

- New Dynamic tags: Purchased Products (can output the titles, linkable titles, or the ids -> ideal for shortcodes
- New Dynamic tags: Purchased Badge

---

### V 1.02 - _2024.03.21_

- Added missing index
- New Dynamic Tag: Product lenght

---

### V 1.01 - _2024.03.20_

- NEW Dynamic Visibility (User Membership Access Can View), New Dynamic tag (Active Subscription)

---

### V 1.0 - _2024.03.20_

- Release the kraken
