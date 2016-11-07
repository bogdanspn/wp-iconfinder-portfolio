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
* omit - a comma-separated list of iconset IDs to omit from display
* sort_by - the iconset field name to sort by
* sort_order - whether to sort in ASC or DESC order
* collection - The collection_id of a collection of iconsets to display
* img_size - The preview image size (normal, large)

#### count

`[iconfinder_portfolio count=20]`

The above token will display the first 20 iconsets from your Iconfinder account. ordered by newest to oldest.

#### sort_by and sort_order

You can specify the display order or iconsets in either ascending (ASC) or descending (DESC) order based on the  `date`, `name`, or `iconset_id`.

`[iconfinder_portfolio sort_by=date sort_order=DESC]`

Valid sort_by values:

* date (the publication date of the iconset)
* name (the name of the iconset)
* iconset_id

Valid sort_order values:

* ASC (oldest to newest, Z-A, lowest to highest ID)
* DESC (newest to oldest, A-Z, highest to lowest ID)

#### omit

You can omit one or more iconsets from display by including the `omit` parameter like in the example below

`[iconfinder_portfolio style=outline omit=10234,56078,98706]`

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

#### collection

You can specify a Collection of iconsets from your Iconfinder profile to display.

`[iconfinder_portfolio collection=12345]`

You can combine the `collection` parameter with other filtering parameters as well.

`[iconfinder_portfolio collection=12345 sort_by=name sort_order=DESC omit=98765,98764]`

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

#### img_size (preview image size)

You can choose which iconset preview image size to display on your site.

`[iconfinder_portfolio style=outline img_size=large]`

Valid Type Values:

* normal
* large

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

* Version 1.0 causes an error in WP admin but doesn't cause errors on public pages

## Roadmap

* Allow multiple style identifiers
* Add WP text editor integration
* Add UI to create new smartcodes for copy/paste or direct insert
* Widgetize output
* Integration with Social Media sharing
* Allow site users to view all icons in an iconset
* Update for better SEO
* Show all valid shortcode params in settings panel
* Show Iconset and Collection IDs in plugin settings panel
* Add "Test API credentials" to Iconfinder Porfolio settings page
* Add graceful failure recovery
* Add local caching of objects to display if the API is unreachable

## Changelog

### 1.0

* Initial release
* Added sorting
* Added `omit` to filter out specific sets by iconset_id
* Removed user_id from settings page
* Updated API calls to use username instead of user_id
* Adding constants to settings file for ICONFINDER_URL, ICONFINDER_CDN_URL
* Added collections to output options
* Added Iconset and Collection IDs in plugin settings panel
* Added img_size parameter to shortcode

## Credits

* The Iconfinder Portfolio plugin is built on the WordPress Plugin Boilerplate by http://wppb.io
* The initial code was auto-generated using WPPB generator at http://wppb.me
* Additional guidance was provided by the article https://scotch.io/tutorials/how-to-build-a-wordpress-plugin-part-1