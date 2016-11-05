<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2 style="margin: 20px 0;"><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <div class="notice notice-info">
    	<p><?php _e( 'Contact Iconfinder support at <a href="#">support@iconfinder.com</a>', 'iconfinder-portfolio' ); ?></p>
	</div>
	
	<h3 class="notice">Displaying Content on Your site</h2>

	<p>Iconfinder Portfolio uses shortcodes to display content that you specify anywhere on your site.</p>
	
	<p>The most basic version of the shortcode will display up to 100 iconsets from your Iconfinder account. If no count is specified, the default API limit of 100 sets will be displayed. </p>
	
	<p>To display an unfiltered list of iconsets, simply add this token to any blog post or page on your site:</p>
	
	<input value="[iconfinder_portfolio]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">Shortcode Paramaters </h3>
	
	<p>You can also use the following list of parameters to filter the content that displayed. Details for each parameter are included below.</p>
	
	<ul>
		<li><strong>sets</strong> - Shows a specific list of iconsets by iconset IDs</li>
		<li><strong>count</strong> - Limits the number of iconsets that are displayed (1 - 100, 100 is the default if no count is specified)</li>
		<li><strong>style</strong> - Shows only iconsets matching a valid style identifier</li>
		<li><strong>type</strong> - Shows only iconsets matching a valid type identifier</li>
		<li><strong>categories</strong> - Shows only iconsets matching a comma-separated list of 1 or more category identifiers</li>
		<li><strong>mixed</strong> - You can combine any of the above (sets overrides all other filters except count)</li>
		<li><strong>omit</strong> - a comma-separated list of iconset IDs to omit from display</li>
		<li><strong>sort_by</strong> - the iconset field name to sort by</li>
		<li><strong>sort_order</strong> - whether to sort in ASC or DESC order</li>
		<li><strong>collection</strong> - The collection_id of a collection of iconsets to display</li>
		<li><strong>img_size</strong> - The preview image size (normal, large)</li>
	</ul>
	
	<h3 class="notice">count</h3>
	
	<p>The shortcode below will display the first 20 iconsets from your Iconfinder account. ordered by newest to oldest.</p>
	
	<input value="[iconfinder_portfolio count=20]" class="shortcode" onclick="this.select()" />
	
		
	<h3 class="notice">sort_by and sort_order</h3>
	
	<p>You can specify the display order or iconsets in either ascending (ASC) or descending (DESC) order based on the  `date`, `name`, or `iconset_id`.</p>
	
	<input value="[iconfinder_portfolio sort_by=date sort_order=DESC]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid sort_by values:</h4>
	
	<ul>
	    <li><strong>date</strong> - the publication date of the iconset</li>
	    <li><strong>name</strong> - the name of the iconset)</li>
	    <li><strong>iconset_id</strong></li>
	</ul>
	
	<h4>Valid sort_order values:</h4>
	
	<ul>
	    <li><strong>ASC</strong> - oldest to newest, Z-A, lowest to highest ID</li>
	    <li><strong>DESC</strong> - newest to oldest, A-Z, highest to lowest ID</li>
	</ul>
	
	<h3 class="notice">omit</h3>
	
	<p>You can omit one or more iconsets from display by including the `omit` parameter like in the example below</p>
	
	<input value="[iconfinder_portfolio style=outline omit=10234,56078,98706]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">style</h3>
	
	<p>You can show only iconsets that are a particular style by including the 'style' parameter.</p>
	
	<input value="[iconfinder_portfolio style=outline]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid Style Values:</h4>
	
	<ul>
	    <li>3d</li>
	    <li>cartoon</li>
	    <li>flat</li>
	    <li>glyph</li>
	    <li>handdrawn</li>
	    <li>outline</li>
	    <li>photorealistic</li>
	    <li>pixel</li>
	    <li>smooth</ii>
	</ul>
	
	<h3 class="notice">type</h3>
	
	<p>You can show only free or premium iconsets by including the 'type' parameter.</p>
	
	<input value="[iconfinder_portfolio style=outline]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid Type Values:</h4>
	
	<ul>
	    <li>premium</li>
	    <li>free</li>
	</ul>
	
	<h3 class="notice">collection</h3>
	
	<p>You can specify a Collection of iconsets from your Iconfinder profile to display.</p>
	
	<input value="[iconfinder_portfolio collection=12345]" class="shortcode" onclick="this.select()" />
	
	<p>You can combine the `collection` parameter with other filtering parameters as well.</p>
	
	<input value="[iconfinder_portfolio collection=12345 sort_by=name sort_order=DESC omit=98765,98764]" class="shortcode" onclick="this.select()" />
	
	
	<h3 class="notice">sets</h3>
	
	<p>You can show specific sets by including a comma-separated list of iconset IDS. You can find the iconset ID in the <em>WP Admin / Iconfinder Porfolio / My Iconsets</em> menu</p>
	
	<input value="[iconfinder_portfolio sets=18389,16747,16745]" class="shortcode" onclick="this.select()" />
	
	<p>Note that if you specify a list of iconset IDs, all other filters will be ignored.</p>
	
	
	<h3 class="notice">categories</h3>
	
	<p>You can show iconsets from specific categories by including the 'categories' parameter.</p>
	
	<input value="[iconfinder_portfolio categories=christmas,halloween,easter]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid Category Values:</h4>
	<ul>
	    <li>abstract</li>
	    <li>animal</li>
	    <li>arrow</li>
	    <li>avatars-smiley</li>
	    <li>business-finance</li>
	    <li>christmas</li>
	    <li>clothes-accessory</li>
	    <li>computer-hardware</li>
	    <li>desktop-app</li>
	    <li>easter</li>
	    <li>education-science</li>
	    <li>events-and-entertainment</li>
	    <li>fall</li>
	    <li>family-home</li>
	    <li>file-folder</li>
	    <li>flag</li>
	    <li>food-drinks</li>
	    <li>gaming-gambling</li>
	    <li>halloween</li>
	    <li>health-beauty-and-fashion</li>
	    <li>healthcare-medical</li>
	    <li>interior-building</li>
	    <li>ios7-optimized</li>
	    <li>maps-navigation</li>
	    <li>mixed</li>
	    <li>mobile-app</li>
	    <li>music-multimedia</li>
	    <li>nature-outdoor</li>
	    <li>network-communication</li>
	    <li>nsfw</li>
	    <li>photography-graphic-design</li>
	    <li>real-estate</li>
	    <li>recreation-hobby</li>
	    <li>romance</li>
	    <li>seasons</li>
	    <li>security</li>
	    <li>seo-web</li>
	    <li>shipping-delivery-and-fulfillment</li>
	    <li>shopping-ecommerce</li>
	    <li>sign-symbol</li>
	    <li>social-media</li>
	    <li>sports-award</li>
	    <li>spring</li>
	    <li>summer</li>
	    <li>touch-gesture</li>
	    <li>transportation</li>
	    <li>travel-hotel</li>
	    <li>ui</li>
	    <li>weather</li>
	    <li>winter</li>
	</ul>
	
	<h3 class="notice">img_size</h3>
	
	<p>You can choose which iconset preview image size to display on your site.</p>
	
	<input value="[iconfinder_portfolio style=outline img_size=large]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid Type Values:</h4>
	<ul>
	    <li>normal</li>
	    <li>large</li>
	</ul>
	
	<h3 class="notice">Mixing Filters</h3>
	
	<p>You can also display iconsets that match multiple filters. Keep in mind, however, that 'sets' (a list of specific iconset IDs, overrides all other filters)</p>
	
	<input value="[iconfinder_portfolio style=outline type=free categories=business-finance,shopping-ecommerce,seo-web]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">Calling the Shortcode in Theme files</h3>
	
	<p>You can also call the Iconfinder Portfolio shortcode from your theme files with the following:</p>
	
	<input value="&lt;?php do_shortcode('[iconfinder_portfolio style=outline]'); ?&gt;" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">Theming the Iconfinder Portfolio output</h3>
	
	<p>You can create your own templates for the output from the Iconfinder Portfolio plugin. Add your custom theme file to <em>wp-content/iconfinder-portfolio/public/partials/</em>.</p>
	
	<p>Note that theme names are required to follow the format `theme-identifier.php` where `theme-` is the required prefix and `idenfitier` is your custom theme name. `identifier` is the only part of the name you can modify.</p>
	
	<p>For complete details for creating your own theme,  See the example theme in:</p>
	
	<p><em>wp-content/plugins/iconfinder-portfolio/public/partials/theme-mytheme.php</em></p>
	
	<p>You can specify a custom theme by including the 'theme' parameter to the Iconfinder Portfolio shortcode. The name of your theme is the middle part of the theme file name. For example, if your theme file name is `theme-mytheme.php`. The theme name would simply be `mytheme`.</p>
	
	<input value="[iconfinder_portfolio theme=mytheme]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">Credits</h3>
	
	<ul>
	    <li>The Iconfinder Portfolio plugin is built on the WordPress Plugin Boilerplate by <a href="#">http://wppb.io</a></li>
	    <li>The initial code was auto-generated using WPPB generator at <a href="#">http://wppb.me</a></li>
	</ul>

</div>
