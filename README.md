# Iconfinder Portfolio

* Tags: iconfinder, portfolio, referral content, icons
* Requires at least: 3.0.1
* Tested up to: 4.3
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that integrates the Iconfinder API with a WordPress-powered site

## Description

This plugin allows anyone to display content from Iconfinder.com on a WordPress-powered site. In order to use this plugin, you will need an active account on Iconfinder.com and a valid API application key.

## Installation


### Before You Begin

In order to use this plugin you will need a valid (free) account on Iconfinder. To create an account, simply visit http://iconfinder.com and click the "Sign Up" link in the upper right-hand corner of the home page.

You will also need a valid API Application on Iconfinder.com. You can request an API application by visiting https://www.iconfinder.com/api-solution and clicking the Request A Demo button or email support@iconfinder.com

### Installing the Plugin

1. Upload `iconfinder-portfolio` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Admin Menu > Settings > Iconfinder Portfolio
4. Enter your valid userid. You can location your USERID by ... TBD
5. Enter your valid username that you selected when you created your Iconfinder account
6. Enter the API Application Client ID and Client Secret, which you can locate ... TBD

### Displaying Content on Your site

Iconfinder Portfolio uses shortcodes to display content that you specify anywhere on your site.

The most basic version of the shortcode will display up to 100 iconsets from your Iconfinder account. If no count is specified, the default API limit of 100 sets will be displayed. 

To display an unfiltered list of iconsets, simply add this token to any blog post or page on your site:

`[iconfinder_portfolio]`

### Shortcode Paramaters 

You can also use the following list of parameters to filter the content that displayed. Details for each parameter are included below.

* sets  - Shows a specific list of iconsets by iconset IDs
* count - Limits the number of iconsets that are displayed (1 - 100, 100 is the default if no count is specified)
* style - Shows only iconsets matching a valid style identifier
* type - Shows only iconsets matching a valid type identifier
* categories - Shows only iconsets matching a comma-separated list of 1 or more category identifiers
* mixed - You can combine any of the above (sets overrides all other filters except count)

#### count

`[iconfinder_portfolio count=20]`

The above token will display the first 20 iconsets from your Iconfinder account. ordered by newest to oldest.

#### sourt_by and sort_order

You can specify the display order or iconsets in either ascending (ASC) or descending (DESC) order based on the  `date`, `name`, or `iconset_id`.

`[iconfinder_portfolio sort_by=date sort_order=DESC]`

Valid sort_by values:

* date (the publication date of the iconset)
* name (the name of the iconset)
* iconset_id

Valid sort_order values:

* ASC (oldest to newest, Z-A, lowest to highest ID)
* DESC (newest to oldest, A-Z, highest to lowest ID)

#### style

You can show only iconsets that are a particular style by including the 'style' parameter.

`[iconfinder_portfolio style=outline]`

Valid Style Values:

* 3d
* cartoon
* flat
* glyph
* handdrawn
* outline
* photorealistic
* pixel
* smooth

#### type (premium or free icons)

You can show only free or premium iconsets by including the 'type' parameter.

`[iconfinder_portfolio style=outline]`

Valid Type Values:

* premium
* free

#### sets (a comma-separated list of specific iconset ids)

You can show specific sets by including a comma-separated list of iconset IDS. You can find the iconset ID by ... TBD

`[iconfinder_portfolio sets=18389,16747,16745]`

Note that if you specify a list of iconset IDs, all other filters will be ignored.

#### categories (a comma-separated list of category identifiers)

You can show iconsets from specific categories by including the 'categories' parameter.

`[iconfinder_portfolio categories=christmas,halloween,easter]`

Valid Category Values:

* abstract
* animal
* arrow
* avatars-smiley
* business-finance
* christmas
* clothes-accessory
* computer-hardware
* desktop-app
* easter
* education-science
* events-and-entertainment
* fall
* family-home
* file-folder
* flag
* food-drinks
* gaming-gambling
* halloween
* health-beauty-and-fashion
* healthcare-medical
* interior-building
* ios7-optimized
* maps-navigation
* mixed
* mobile-app
* music-multimedia
* nature-outdoor
* network-communication
* nsfw
* photography-graphic-design
* real-estate
* recreation-hobby
* romance
* seasons
* security
* seo-web
* shipping-delivery-and-fulfillment
* shopping-ecommerce
* sign-symbol
* social-media
* sports-award
* spring
* summer
* touch-gesture
* transportation
* travel-hotel
* ui
* weather
* winter

#### Mixed Filters

You can also display iconsets that match multiple filters. Keep in mind, however, that 'sets' (a list of specific iconset IDs, overrides all other filters)

`[iconfinder_portfolio style=outline type=free categories=business-finance,shopping-ecommerce,seo-web]`

### Calling the Shortcode in Theme files

You can also call the Iconfinder Portfolio shortcode from your theme files with the following:

`<?php do_shortcode("[iconfinder_portfolio style=outline]"); ?>`

### Theming the Iconfinder Portfolio output

You can create your own templates for the output from the Iconfinder Portfolio plugin. Add your custom theme file to `wp-content/iconfinder-portfolio/public/partials/`.

Note that theme names are required to follow the format `theme-identifier.php` where `theme-` is the required prefix and `idenfitier` is your custom theme name. `identifier` is the only part of the name you can modify.

For complete details for creating your own theme,  See the example theme in:

`wp-content/plugins/iconfinder-portfolio/public/partials/theme-mytheme.php`

You can specify a custom theme by including the 'theme' parameter to the Iconfinder Portfolio shortcode. The name of your theme is the middle part of the theme file name. For example, if your theme file name is `theme-mytheme.php`. The theme name would simply be `mytheme`.

`[iconfinder_portfolio theme=mytheme]`

## Known Issues

* None as of October 30, 2016

## Roadmap

* Allow multiple style identifiers
* Add WP text editor integration
* Add UI to create new smartcodes for copy/paste or direct insert
* Widgetize output

## Changelog

### 1.0

* Initial release

## Credits

* The Iconfinder Portfolio plugin is built on the WordPress Plugin Boilerplate by http://wppb.io
* The initial code was auto-generated using WPPB generator at http://wppb.me
* Additional guidance was provided by the article https://scotch.io/tutorials/how-to-build-a-wordpress-plugin-part-1