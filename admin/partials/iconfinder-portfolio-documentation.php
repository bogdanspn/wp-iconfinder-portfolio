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
	
	<table class="iconfinder-portfolio-params">
		<head>
		    <tr>
		        <th>Parameter</th>
		        <th>Accepted Values</th>
		        <th>Description</th>
		    </tr>
		</head>
		<tbody>
		    <tr>
		    	<td>sets</td>
		    	<td>comma-separated list of iconset ids</td>
		    	<td>Displays a specific list of icon sets. Over-rides all other parameters except sort_by and sort_order.</td>
		    </tr>
			<tr>
				<td>count</td>
				<td>inegeger 0-100</td>
				<td>Limits the number of iconsets that are displayed.</td>
			</tr>
			<tr>
				<td>style</td>
				<td>string identifier</td>
				<td>Shows only iconsets matching a valid style identifier.</td>
			</tr>
			<tr>
				<td>type</td>
				<td>premium or free</td>
				<td>Shows only iconsets matching a valid type identifier.</td>
			</tr>
			<tr>
				<td>categories</td>
				<td>comma-separated list of string identifiers</td>
				<td>Shows only iconsets matching a comma-separated list of 1 or more category identifiers.</td>
			</tr>
			<tr>
				<td>omit</td>
				<td>comma-separated list of iconset ids</td>
				<td>Filters from display 1 or more icon sets.</td>
			</tr>
			<tr>
				<td>sort_by</td>
				<td>string field name</td>
				<td>Sorts the icon sets by the field name given. Requires sort_order param.</td>
			</tr>
			<tr>
				<td>sort_order</td>
				<td></td>
				<td>Whether to sort in ASC or DESC order.</td>
			</tr>
			<tr>
				<td>collection</td>
				<td>integer collection ID</td>
				<td>Displays all icon sets within a given collection. Requires sort_by param. Over-rides `sets` param.</td>
			</tr>
			<tr>
				<td>img_size</td>
				<td>normal or large</td>
				<td>The icon set preview image size.</td>
			</tr>
		</tbody>
	</table>
	
	<h3 class="notice">count</h3>
	
	<p>The shortcode below will display the first 20 iconsets from your Iconfinder account. ordered by newest to oldest.</p>
	
	<input value="[iconfinder_portfolio count=20]" class="shortcode" onclick="this.select()" />
	
		
	<h3 class="notice">sort_by and sort_order</h3>
	
	<p>You can specify the display order or iconsets in either ascending (ASC) or descending (DESC) order based on the  `date`, `name`, or `iconset_id`.</p>
	
	<input value="[iconfinder_portfolio sort_by=date sort_order=DESC]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid sort_by values:</h4>
	
	<table class="iconfinder-portfolio-params">
		<head>
		    <tr>
		        <th>Option</th>
		        <th>Description</th>
		    </tr>
		</head>
		<tbody>
		    <tr>
		    	<td>date</td>
		    	<td>the publication date of the iconset</td>
		    </tr>
		    <tr>
		    	<td>name</td>
		    	<td>the name of the iconset</td>
		    </tr>
		    <tr>
		    	<td>iconset_id</td>
		    	<td>the integer ID of the iconset</td>
		    </tr>
		</tbody>
	</table>
	
	<h4>Valid sort_order values:</h4>
	
	<table class="iconfinder-portfolio-params">
		<head>
		    <tr>
		        <th>Option</th>
		        <th>Description</th>
		    </tr>
		</head>
		<tbody>
		    <tr>
		    	<td>ASC</td>
		    	<td>oldest to newest, Z-A, lowest to highest ID</td>
		    </tr>
		    <tr>
		    	<td>DESC</td>
		    	<td>newest to oldest, A-Z, highest to lowest ID</td>
		    </tr>
		</tbody>
	</table>
	
	<h3 class="notice">omit</h3>
	
	<p>You can omit one or more iconsets from display by including the `omit` parameter like in the example below</p>
	
	<input value="[iconfinder_portfolio style=outline omit=10234,56078,98706]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">style</h3>
	
	<p>You can show only iconsets that are a particular style by including the 'style' parameter.</p>
	
	<input value="[iconfinder_portfolio style=outline]" class="shortcode" onclick="this.select()" />
	
	<h4>Valid Style Values:</h4>

	<?php if (isset($data['styles'])) : ?>
	    <ul class="iconfinder-portfolio-styles iconfinder-portfolio-options">
	    <?php foreach ($data['styles'] as $style): ?>
	        <li><?php echo $style['identifier']; ?></li>
	    <?php endforeach; ?>
	    </ul>
	<?php endif; ?>
	
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
	
	<?php if (isset($data['categories'])) : ?>
	    <ul class="iconfinder-portfolio-categories iconfinder-portfolio-options">
	    <?php foreach ($data['categories'] as $category): ?>
	        <li><?php echo $category['identifier']; ?></li>
	    <?php endforeach; ?>
	    </ul>
	<?php endif; ?>
	
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
	
	<h3 class="notice">Owly image carousel theme</h3>
	
	<p>Iconfinder Portfolio comes with a built-in image slider based on the Owl image slider for jQuery. You can specify the theme by adding the following parameter to your shortcode:</p>
	
	<input value="[iconfinder_portfolio theme=owly]" class="shortcode" onclick="this.select()" />
	
	<h3 class="notice">Credits</h3>
	
	<ul>
	    <li>The Iconfinder Portfolio plugin is built on the WordPress Plugin Boilerplate by <a href="#">http://wppb.io</a></li>
	    <li>The initial code was auto-generated using WPPB generator at <a href="#">http://wppb.me</a></li>
	    <li>Bogdan Rosu contributed the Owly image slider front-end theme - <a href="http://bogdanrosu.com">http://bogdanrosu.com</a></li>
	</ul>

</div>
