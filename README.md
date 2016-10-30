# Iconfinder Portfolio

Tags: iconfinder, portfolio, referral content, icons
Requires at least: 3.0.1
Tested up to: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that integrates the Iconfinder API with a WordPress-powered site

## Description ==

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

The most basic version of the shortcode will display up to 100 iconsets from your Iconfinder account. Simply add this token to any blog post or page on your site:

`[iconfinder_portfolio]`

You can also filter content to match the following parameters:

#### style

You can show only iconsets that are a particular style by including the 'style' parameter.

`[iconfinder_portfolio style=outline]`

Valid Style Values:

- 3d
- cartoon
- flat
- glyph
- handdrawn
- outline
- photorealistic
- pixel
- smooth

#### type (premium or free icons)

You can show only free or premium iconsets by including the 'type' parameter.

`[iconfinder_portfolio style=outline]`

Valid Type Values:

- premium
- free

#### sets (a comma-separated list of specific iconset ids)

You can show specific sets by including a comma-separated list of iconset IDS. You can find the iconset ID by ... TBD

`[iconfinder_portfolio sets=18389,16747,16745]`

Note that if you specify a list of iconset IDs, all other filters will be ignored.

#### categories (a comma-separated list of category identifiers)

You can show iconsets from specific categories by including the 'categories' parameter.

`[iconfinder_portfolio categories=christmas,halloween,easter]`

Valid Category Values:

- abstract
- animal
- arrow
- avatars-smiley
- business-finance
- christmas
- clothes-accessory
- computer-hardware
- desktop-app
- easter
- education-science
- events-and-entertainment
- fall
- family-home
- file-folder
- flag
- food-drinks
- gaming-gambling
- halloween
- health-beauty-and-fashion
- healthcare-medical
- interior-building
- ios7-optimized
- maps-navigation
- mixed
- mobile-app
- music-multimedia
- nature-outdoor
- network-communication
- nsfw
- photography-graphic-design
- real-estate
- recreation-hobby
- romance
- seasons
- security
- seo-web
- shipping-delivery-and-fulfillment
- shopping-ecommerce
- sign-symbol
- social-media
- sports-award
- spring
- summer
- touch-gesture
- transportation
- travel-hotel
- ui
- weather
- winter

## Known Issues

* None as of October 30, 2016

## Changelog

= 1.0 =
* Initial release