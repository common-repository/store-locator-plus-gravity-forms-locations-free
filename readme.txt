=== Store Locator Plus : Gravity Forms Locations ===
Plugin Name:  Store Locator Plus : Gravity Forms Locations
Contributors: DeBAAT, charlestonsw
Donate link: https://www.storelocatorplus.com/
Tags: gravity forms, gravity forms integration, store locator plus, data tables, google maps, locations, integration
Requires at least: 4.0
Tested up to: 5.0.2
Stable tag: 5.0.0

A free add-on pack for Store Locator Plus that supports basic locations for Gravity Forms.

== Description ==

[SLP](https://www.storelocatorplus.com/) | [Location and Directory SaaS](https://my.storelocatorplus.com)| [WordPress Plugins](https://wordpress.storelocatorplus.com)  | [Documentation](https://docs.storelocatorplus.com) | [Demo](https://demo.storelocatorplus.com)

Add location data fields to Gravity Forms to support locations in Store Locator Plus.

= How does it work? =

Ensure you have the right versions of Store Locator Plus and Gravity Forms installed.
Create a form with Gravity Forms or use an existing one.
Create new Gravity Forms Locations mapping ((GFL Mapping)) to assign form fields to location fields.
The GFL Mapping supports a condition from the form input to determine whether or not to map the form entry to a location.
Create a post or page with the [[SLPLUS]] shortcode to show the locations entered.

= Features =

Adds the following fields to location data:

* Store Name
* Address
* Email
* Description

= Additional Pro Features =

On the [Store Locator Plus Website](https://www.storelocatorplus.com/), you can purchase an additional [SLP Gravity Forms Integration](https://www.storelocatorplus.com/product/gravity-forms-integration/) add-on pack to enhance this Gravity Forms integration even more with the following features:

* Supports extended location data supported by other add-ons.
* Supports additional search features to search for locations entered by a certain form.
* Supports additional shortcode attributes '[slplus gfl_form_id_selector="hidden" gfl_form_id=7]':
** 'gfl_form_id_selector="hidden"': Include the selector to enable filtering on gfl_form_id.
** 'gfl_form_id=7': The id of the form to be used for the filtering.
* Support for an additional option 'Skip Geocoding' to determine to publish entered locations immediately or not.
* Assign or Remove gfl_form_id values to existing locations with bulk actions.

== Installation ==

= Requirements =

* Store Locator Plus: 5.0
* WordPress: 5.0
* Gravity Forms: 2.4+
* PHP: 5.2.4+

= Install After SLP =

1. Go fetch and install [Store Locator Plus](https://www.storelocatorplus.com/product/store-locator-plus/).
2. Install this plugin from the WordPress org site.

OR

2. Download this plugin from the WordPress org site to get the latest .zip file.
3. Go to plugins/add new.
4. Select upload.
5. Upload the zip file.

== Frequently Asked Questions ==

= What are the terms of the license? =

The license is GPL.  You get the code, feel free to modify it as you
wish.  We prefer that our customers pay us because they like what we do and
want to support our efforts to bring useful software to market.  Learn more
on our [SLP License Terms](https://www.storelocatorplus.com/products/general-eula/).

== Changelog ==

= 5.0.00 =
* Updated to work with WP and SLP 5.0.

= 4.7.10 =

* Tested with WordPress 4.7.5
* Required for SLP 4.7.10 or higher.

= 4.6.00 =
* Tested with WordPress 4.6.
* Updated to support SLP 4.6.
* Fixed handling of skip_geocoding to prevent publishing locations immediately

= 4.5.03 =
* Added support for editing Form Entries in [SLP Gravity Forms Integration](https://www.storelocatorplus.com/product/gravity-forms-integration/) add-on.

= 4.5.02 =
* Added support for GF Resume Token as needed for editing Form Entries in [SLP Gravity Forms Integration](https://www.storelocatorplus.com/product/gravity-forms-integration/) add-on.

= 4.5.01 =
* Added support for GF Post ID as needed for editing Form Entries in [SLP Gravity Forms Integration](https://www.storelocatorplus.com/product/gravity-forms-integration/) add-on.
* Tested with WordPress 4.5.2.

= 4.5.00 =
* Tested with WordPress 4.5.
* Updated to support SLP 4.5.

= 4.4.00 =
* Change: Requires SLP 4.4
* Tested on WordPress 4.4.2
* Renamed Text Domain to reflect name of plugin.
* Fixed use of admin_page_slug and addition of new location to database.
* Implemented Gravity Forms functionality using GFAPI calls.
* Fixed issue with linking image url data.

= 4.3.01 =
* Test with WP 4.3.1

= 4.3 =
* Change: Requires SLP 4.3

= 4.2.05 =
* Fix: the install directory and plugin slug now goes in ./plugins/slp-gravity-forms-locations-free/

= 4.2.04 =
* Improved validation dependency check.
* Prepared to be base for adding [SLP Gravity Forms Integration](https://www.storelocatorplus.com/product/gravity-forms-integration/) add-on.
* Improved Gravity Forms settings tab.

= 4.2.03 =
* Improved translations and added nl_NL.

= 4.2.02 =
* Fixed activation bug.

= 4.2.01 =
* Initial release.
