<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (! function_exists('is_true')) {
    /**
     * Tests a mixed variable for true-ness.
     * @param int|null|bool|string $value
     * @param null|string|bool|int $default
     * @return bool|null
     */
    function is_true($value, $default=null) {
        $result = $default;
        $trues  = array(1, '1', 'true', true, 'yes', 'da', 'si', 'oui', 'absolutment', 'yep', 'yeppers', 'fuckyeah');
        $falses = array(0, '0', 'false', false, 'no', 'non', 'nein', 'nyet', 'nope', 'nowayjose');
        if (in_array(strtolower($value), $trues, true)) {
            $result = true;
        }
        else if (in_array(strtolower($value), $falses, true)) {
            $result = false;
        }
        return $result;
    }
}

/**
 * Append or create a WP_Error
 * @param int|null|object|\WP_Error $result
 * @param int|null|object|\WP_Error $error
 * @param null|array $messages
 * @return null|\WP_Error
 */
function icf_append_error($result, $error, $messages=array()) {
    
    if (! is_wp_error($result) && ! is_wp_error($error)) {
        return null;
    }
    
    if (! is_wp_error($result)) {
        $result = new WP_Error( 'iconfinder_error', '');
    }
    
    if (! empty($messages) && ! is_array($messages)) {
        $messages = array($messages);
    }
    if (is_wp_error($error)) {
        $messages = array_merge($messages, $error->get_error_messages());
    }
    
    $n = 0;
    foreach ($messages as $message) {
        $result->add(
            "iconfinder_error_{$n}",
            __($message, ICF_PLUGIN_NAME),
            null
        );
        $n++;
    }
    return $result;
}

/**
 * Check to see if a post exists by iconset_id
 * @param integer $iconset_id
 * @return boolean or integer
 * 
 * @since 1.1.0
 */
function icf_post_exists($iconset_id) {
    $result = false;
    $post = get_post_by_iconset_id($iconset_id);    
    if (is_post($post)) {
        $result = $post->ID;
    }
    return $result;
}

/**
 * Tests if a variable is an instance of WP_Post and has an ID.
 * @param mixed $post
 * @return boolean
 */
function is_post($post) {
    if (! is_a($post, 'WP_Post')) { return false; }
    if (! isset($post->ID)) { return false; }
    if (empty($post->ID)) { return false; }
    return true;
}

/**
 * Wrappre function to count posts of a certain type.
 * @param string $post_type
 * @return integer
 */
function icf_count_posts($post_type) {
    $posts_count = wp_count_posts( $post_type );
    return $posts_count->publish;
}

/**
 * Get a setting value.
 * @param string $key
 * @param mixed $default
 * @return mixed
 * 
 * @since 1.1.0
 */
function icf_get_setting($key, $default=null) {
    
    $settings = _icf_settings();
    $value = $default ;
    if (! empty($key) && isset($settings[$key])) {
        $value = $settings[$key];
    }
    return $value;
}        

/**
 * Splits a character-delimited string into words.
 * @param string $str
 * @param string $delim
 * @return array
 * 
 * @since 1.1.0
 */
function str_to_words($str, $delim='-') {
    return array_map('trim', explode($delim, $str));
}

/**
 * Converts a dash-delimited identifier into a name of only words (no numbers).
 * @param string $str
 * @return string
 * 
 * @since 1.1.0
 */
function nice_name($str) {

    $clean = array();
    $words = explode(' ', str_to_words($str));
    foreach ($words as $word) {
        if (is_numeric($word)) {
            continue;
        }
        array_push($clean, $word);
    }
    return implode(' ', $clean);
}

/**
 * A wrapper for WP's get_option to return a single value.
 * @param string $name
 * @param string|null $default
 * @return mixed
 */
function icf_get_option($name, $default=null) {
    $value = $default;
    $options = get_option( ICF_PLUGIN_NAME );
    if (isset($options[$name])) {
        $value = $options[$name];
    }
    return $value;
}

/**
 * Add JS `onclick` to a delete button/link.
 * 
 * @since 1.1.0
 */
function onclick_confirm_delete() {
    echo onclick(ICF_CONFIRM_DELETE);
}

/**
 * Add JS `onclick` to a the Update button.
 * 
 * @since 1.1.0
 */
function onclick_confirm_update() {
    echo onclick(ICF_CONFIRM_UPDATE);
}

/**
 * Add JS `onclick` to a the Import button.
 * 
 * @since 1.1.0
 */
function onclick_confirm_import() {
    echo onclick(ICF_CONFIRM_IMPORT, true);
}

/**
 * This is a debug function and ideally should be removed from the production code.
 * @param array|object $what
 */
function debug($what) {
    die ('<pre>' . print_r($what, true) . '</pre>');
}

/**
 * Generates an `onclick` JS handler
 * @param string $message
 * @param boolean $undo
 * @return string
 * 
 * @since 1.1.0
 */
function onclick($message, $undo=false) {
    if (! $undo) {
        $message .= ' ' . __('This action cannot be undone.');
    }
    return ' onclick="return confirm(\'' . $message . '\');"';
}

function confirm_js($message) {
    return "return confirm('" . addslashes($message) . "');";
}

/**
 * Saves error message strings as transient to be displayed by action callback.
 * @param array $notices
 * @param string|null $type
 * @return bool
 */
function icf_queue_notices($notices, $type='success') {
    if (! is_array($notices)) {
        $notices = array($notices);
    }
    $message = "";
    foreach ($notices as $notice) {
        $message .= "{$notice}<br/>";
    }
    return set_transient( ICF_PLUGIN_NAME . '_' . $type, $message, HOUR_IN_SECONDS );
}


/**
 * Show a success notice.
 */
function icf_admin_notices() {
    
    $types = array('success', 'error');

    foreach ($types as $type) {
        $transient_key = ICF_PLUGIN_NAME . '_' . $type;
        if (! empty($transient_key)) {
            $messages = get_transient( $transient_key );
            delete_transient( $transient_key );
        }

        if (! empty($messages)) {
            if (! is_array($messages)) {
                $messages = array($messages);
            }
            foreach ($messages as $message) {
                printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', $type, __( $message, ICF_PLUGIN_NAME) ); 
            }
            $message = null;
        }
    }
}
add_action( 'admin_notices' , 'icf_admin_notices' );

/**
 * Create numeric paginated results.
 * @global \WP_Query $wp_query
 * @return void
 * @author WPBeginner
 * @link http://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme/
 * @since 1.1.0
 */
function wpbeginner_numeric_posts_nav() {

	if( is_singular() )
		return;

	global $wp_query;

	$links = null;

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="navigation"><ul>' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link() );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li>…</li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( "<li%s><a href=\"%s\">%s</a></li>\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		printf( '<li>%s</li>' . "\n", get_next_posts_link() );

	echo '</ul></div>' . "\n";

}

/**
 * Builds the admin pagination for the iconsets page.
 * @param int $page_count
 * @param int $current_page
 */
function icf_admin_iconsets_pagination($page_count, $current_page=1) {

    $current_page = get_val($_REQUEST, 'page_num', $current_page);
    $admin_url = admin_url("admin.php?page=iconfinder-portfolio-iconsets");
    echo "<div class=\"icf_iconset_pagination clear clearfix\"><span>Pages:</span> \n";
    echo "<form method=\"post\" action=\"{$admin_url}\" name='icf_iconsets_admin_pagination'>\n";
    wp_nonce_field( 'icf_iconsets_admin_pagination', 'icf_iconsets_admin_pagination', $admin_url, true );
    echo "<input type=\"hidden\" name=\"action\" value=\"iconsets_admin_pagination\" />\n";
    for ($i=0; $i<$page_count; $i++) {
        $class = $i + 1 == $current_page ? 'primary' : 'secondary' ;
        printf(
            "<input type=\"submit\" name=\"page_num\" id=\"submit\" class=\"button button-{$class}\" value=\"%s\" />",
            $i + 1
        );
    }
    echo "<input type=\"hidden\" name=\"" . ICF_PLUGIN_NAME . "[callback]\" value=\"display_iconsets_page\" />\n";
    echo "</form>\n";
    echo "</div>\n";
}

/**
 * Echos the page number.
 */
function icf_page_number() {
    echo icf_get_page_number();
}

/**
 * Grabs the current page number from the _REQUEST array
 * @return int mixed
 */
function icf_get_page_number() {
    return get_val($_REQUEST, 'page_num');
}

/**
 * Get the current plugin mode (advanced or basic).
 * @return string
 */
function icf_get_mode() {
    return icf_get_option('plugin_mode', ICF_PLUGIN_MODE_BASIC);
}

/**
 * Determine if the plugin is currently in advanced mode.
 * @return boolean
 */
function icf_is_advanced_mode() {
    return icf_get_mode() === ICF_PLUGIN_MODE_ADVANCED;
}

/**
 * Determine if the plugin is currently in basic mode.
 * @return boolean
 */
function icf_is_basic_mode() {
    return icf_get_mode() === ICF_PLUGIN_MODE_BASIC;
}

/**
 * Splits a multi-word phrase into individual words.
 * @param string $str
 * @return array
 * @since 1.1.0
 */
function all_search_words($str) {
    $words = explode(',', $str);

    $words = array_map('trim', $words);
    $more_words = array();
    foreach ($words as $word) {
        $more_words = array_merge($more_words, explode(' ', $word));
    }
    $words = array_merge($words, $more_words);
    $words = array_map('trim', $words);

    return $words;
}

/**
 * Load the icon search form.
 * @since 1.1.0
 */
function icon_searchform() {
    if (! icf_is_advanced_mode()) {
        return null;
    }
    if (locate_template('icon-searchform.php') === '') {
        require_once(ICF_TEMPLATE_PATH . 'icon-searchform.php');
    }
}
add_action('icf_icon_searchform', 'icon_searchform');

/**
 * Load the iconset search form.
 * @since 1.1.0
 */
function iconset_searchform() {
    if (! icf_is_advanced_mode()) {
        return null;
    }
    if (locate_template('iconset-searchform.php') === '') {
        require_once(ICF_TEMPLATE_PATH . 'iconset-searchform.php');
    }
}
add_action('icf_iconset_searchform', 'iconset_searchform');

/**
 * Adds the Iconfinder Porfolio template directory to the locate_template search. Always search
 * the wp-content/theme/{current-theme} folder first. But if the template isn't found, we
 * can fall back on the default theme that comes with the plugin.
 * @global \WP_Query $wp_query
 * @param string $template
 * @return string
 * @since 1.1.0
 */
function template_chooser($template) {    
    global $wp_query;
    
    if (! is_iconfinder()) {
        return $template;
    }
    
    $post_type = get_query_var('post_type');
    if (empty($post_type)) {
        $post_type = $_REQUEST['post_type'];
        if (! empty($post_type)) {
            set_query_var($post_type);
        }
    }

    $template_filename = basename($template);

    $find_file = locate_template($template_filename, false);

    if (empty($find_file)) {
        if ( file_exists(ICF_TEMPLATE_PATH . $template_filenmae)) {
            $template = ICF_TEMPLATE_PATH . $template_filename;
        }
    }

//    if ( $post_type === ICF_POST_TYPE_ICON ) {
//        $template = locate_template('icon-search.php', false);
//        if ($template === '') {
//            $template = ICF_TEMPLATE_PATH . 'icon-search.php';
//        }
//    }
//    else if ( is_search() && $post_type === ICF_POST_TYPE_ICONSET ) {
//        $template = locate_template('iconset-search.php', false);
//        if ($template === '') {
//            $template = ICF_TEMPLATE_PATH . 'iconset-search.php';
//        }
//    }
    # icf_dump(get_defined_vars());
    # the_permalink();die();
    return $template;   
}
add_filter('template_include', 'template_chooser');

/**
 * Determine if the current code is being executed
 * inside the iconfinder-portfolio plugin.
 * @return bool
 */
function is_iconfinder() {
    $is_iconfinder = defined('IS_ICONFINDER');
    if (! $is_iconfinder) {
        $post_type = get_val($_REQUEST, 'post_type');
        if ( empty($post_type)) {
            $post_type = get_query_var('post_type');
        }
        if (in_array($post_type, icf_get_setting('icf_post_types'))) {
            $is_iconfinder = true;
        }
    }
    return $is_iconfinder;
}

/**
 * Explicitly set the post_type query_var.
 * TODO: Need to verify if this is necessary
 * @param \WP_Query $query
 * @return \WP_Query
 */
function set_post_type_query($query) {
    
    if (is_admin()) { return $query; }
    
    if (! is_iconfinder()) {
        return $query;
    }

    if (! is_search()) {
        return $query;
    }

    if (isset($_REQUEST['post_type'])) {
        $post_type = $_REQUEST['post_type'];
        if (! empty($post_type)) {
            set_query_var('post_type', $post_type);
        }
    }
    return $query;
}
# add_action('pre_get_posts', 'set_post_type_query');

/**
 * @param \WP_Query $query
 * @return \WP_Query
 */
function set_posts_per_page( $query ) {

    if ( is_iconfinder() ) {
        if ( is_archive() || is_search() ) {
            set_query_var( 'posts_per_page', icf_get_option(
                'posts_per_page', icf_get_setting('posts_per_page')
            ));
        }
    }
    return $query;
}
add_action( 'pre_get_posts', 'set_posts_per_page' );

/**
 * Buffers the output from a file and returns the contents as a string.
 * You can pass named variables to the file using a keyed array. 
 * For instance, if the file you are loading accepts a variable named 
 * $foo, you can pass it to the file  with the following:
 * 
 * @example 
 * 
 *      do_buffer('path/to/file.php', array('foo' => 'bar'));
 * 
 * @param string $path
 * @param array $vars
 * @return string
 */
function do_buffer($path, $vars=null) {
    $output = null;
    if (! empty($vars)) {
        extract($vars);
    }
    if (file_exists($path)) {
        ob_start();
        include_once($path);
        $output = ob_get_contents();
        ob_end_clean();
    }
    return $output;
}

/**
 * Do some manipulations to the posts such as change the guid
 * to point to either the Iconfinder link or the product_link.
 * This change will make sure calls to the_permalink() return 
 * the expected link.
 * @param array $posts
 * @return array
 */
function icf_setup_posts($posts) {
    
    $show_links = icf_get_option('use_purchase_link', false);
    
    foreach ($posts as &$post) {

        $price = get_post_meta( $post->ID, 'price', true );
        if (empty($price))  {
            $price = '0';
        }
        $iconset_id     = get_post_meta($post->ID, 'iconset_id', true);
        $identifier     = get_post_meta($post->ID, 'iconset_identifier', true);
        $permalink      = get_post_meta($post->ID, 'product_link', true);
        $parent_post_id = get_post_meta($post->ID, 'parent_post_id', true);
        
        $icon_id = get_post_meta($post->ID, 'icon_id', true);
        if (! empty($icon_id) && ! empty($parent_post_id)) {
            $parent_post = get_post($parent_post_id);
            $permalink = get_post_meta($parent_post->ID, 'product_link', true);
        }
        if ($show_links && empty($permalink)) {
            $ref = null;
            if (! empty($username)) {
                $ref = "?ref={$username}";
            }
            $permalink = ICONFINDER_LINK_ICONSETS . $identifier . $ref;
        }
        $post->guid = $permalink;
        $post->iconfinder = array(
            'price' => $price,
            'identifier' => $identifier,
            'iconset_id' => $iconset_id,
            'purchase_link' => $permalink
        );
    }
    return $posts;
}

/**
 * Buffers the output from a shortcode.
 * @param string $content
 * @return null|string
 */
function icf_buffer_shortcode($content) {
    $output = null;
    ob_start();
    do_shortcode($content);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * @param array $theme_vars
 */
function icf_set_theme_vars($theme_vars) {
    if (! is_array($theme_vars) || empty($theme_vars)) {
        return;
    }
    foreach ($theme_vars as $key=>$value) {
        $GLOBALS["icf_{$key}"] = $value;
    }
}

/**
 * Search for one of our templates in the plugin theme path 
 * first, then our plugin partials path.
 * @param string $template
 * @return string
 * @since 1.1.0
 */
function icf_locate_template($template) {
    $found_path = null;
    $theme_path = locate_template($template, false);
    if ($theme_path === '') {
        $test_path = ICF_TEMPLATE_PATH . $template;
        if (file_exists($test_path)) {
            $found_path = $test_path;
        }
    }
    else {
        $found_path = $theme_path;
    }
    return $found_path;  
}

/**
 * Builds the product 'Buy Now' button. This function assumes that the
 * product link _might_ be a shortcode for getDPD or EDD so we run it
 * through the do_shortcode function first. If the content from
 * the custom field starts with `[` it is a shortcode and we
 * process it. If not, we process it like a normal permalink.
 *
 * @param $post_id
 * @param string $text
 * @param null $attrs
 * @return mixed|null|string
 */
function icf_build_the_product_button($post_id, $text='', $attrs=null) {

    if (! is_array($attrs)) $attrs = array();

    $the_button = null;
    $post = get_post($post_id);
    if (is_object($post)) {
        if ($post->post_type == 'icon') {
            $parent_post_id = get_val($post, 'post_parent', null);
            if (! empty($parent_post_id)) {
                $button_url = get_the_permalink($parent_post_id);
                $the_button = icf_link_button($text, array(
                    'href'   => $button_url,
                    'class'  => 'icf-buy-button'
                ));
            }
        }
        else {
            $button_html = get_post_meta($post_id, 'purchase_html', true);
            if (! empty($button_html)) {
                $the_button = $button_html;
            }
            else {
                $shortcode = get_post_meta($post_id, 'product_shortcode', true);
                if (is_shortcode($shortcode)) {
                    $the_button = do_shortcode($shortcode);
                }
                else {
                    $button_url = get_the_permalink($post->ID);
                    $attrs_str = '';
                    $attrs = array_merge(array(
                        'class' => 'icf-button icf-buy-button'
                    ), $attrs);

                    if (is_array($attrs)) {
                        foreach ($attrs as $attr => $value) {
                            $attrs_str .= " {$attr}=\"{$value}\" ";
                        }
                    }
                    $the_button = "<a href=\"{$button_url}\" target=\"_blank\" {$attrs_str}>{$text}</a>\n";
                }
            }
        }
    }
    return $the_button;
}

/**
 * Creates an anchor button from text and HTML attributes.
 * @param string $text
 * @param array $attrs
 * @return string
 *
 * @example
 *
 *     echo icf_link_button(array(
 *         'href' => http://mysite.com,
 *         'class' => 'my-button',
 *         'target' => '_blank'
 *     ));
 */
function icf_link_button($text, $attrs) {
    $attrs_str = null;
    $attrs = array_merge(array(
        'class' => 'icf-button'
    ), $attrs);

    if (is_array($attrs)) {
        foreach ($attrs as $attr => $value) {
            $attrs_str .= " {$attr}=\"{$value}\" ";
        }
    }
    return "<a {$attrs_str}>{$text}</a>\n";
}

/**
 * Down and dirty test to see if a string looks like a shortcode.
 * @param string $str
 * @return bool
 */
function is_shortcode($str) {
    $regex = get_shortcode_regex();
    if (preg_match_all('/'. $regex .'/s', $str, $matches) 
            && array_key_exists(2, $matches)) 
    {
        return true;
	} 
    return false;
}

/**
 * Appens the referral code to a string.
 * @param string $link
 * @return string
 */
function add_referral_code($link) {

    if (stripos($link, ICONFINDER_DOMAIN) !== false) {
        $username = icf_get_option('username');
        $link .= "?ref={$username}";
    }
    return $link;
}

/**
 * This function contains the real logic for icf_the_permalink. Since 
 * icons can belong to iconsets and we want to link to the iconset, 
 * we first check the current post for a product_link custom field. 
 * If it doesn't have one, it _might_ be an icon and not an iconset
 * so we check to see if it has a parent_post_id so we return the 
 * product link of the parent.
 * @param integer $post_id
 * @return string
 * @since 1.1.0
 */
function icf_get_permalink($post_id) {
    
    $permalink  = get_post_meta($post_id, 'product_link', true);
    
    if (empty($permalink)) {
        $parent_post_id = get_post_meta($post_id, 'parent_post_id', true);
        if (! empty($parent_post_id)) {
            $permalink  = icf_buffer_shortcode(
                get_post_meta($parent_post_id, 'product_link', true)
            );
        }
    }
    if (empty($permalink)) {
        $identifier = get_post_meta($post_id, 'iconset_identifier', true);
        if (empty($identifier)) {
            $identifier = get_post_meta($post_id, 'iconset_id', true);
        }
        if (! empty($identifier)) {
            $permalink = ICONFINDER_LINK_ICONSETS . $identifier;
        }
    }        
    return add_referral_code($permalink);
}

/**
 * The img_size shortcode values are simplified into small, medium, 
 * and large to be more user-friendly but we need to coerce the value
 * to match the Iconfinder API identifiers.
 * @param string $img_size
 * @return string
 */
function coerce_img_size($img_size) {
    if ($img_size === 'medium') {
        $img_size = 'medium-2x';
    }
    else if (in_array($img_size, array('small', 'normal', 'default'))) {
        $img_size = 'medium';
    }
    else if (! in_array($img_size, array('normal', 'large'))) {
        $img_size = 'medium';
    }
    return $img_size;
}

function coerce_style_values($styles) {
    $new_styles = array();
    $aliases = icf_get_setting('style_aliases');
    $return_type = 'array';
    if (! is_array($styles)) {
        $return_type = 'string';
        $styles = array_map('trim', explode(',', $styles));
    }
    foreach ($styles as $style) {
        $value = $style;
        if (isset($aliases[$style])) {
            $value = $aliases[$style];
        }
        $new_styles[] = $value;
    }
    if ($return_type === 'string') {
        return implode(',', $new_styles);
    }
    return $new_styles;
}

function icf_currency_selector($selected=ICF_DEFAULT_CURRENCY, $selector_name='currency') {
    $currency_symbols = icf_get_currencies();
    $html  = "\n";
    $html .= "<select name=\"{$selector_name}\">";
    $html .= "<option value=\"\">-- " .  __('Choose', ICF_PLUGIN_NAME) . " --</option>";
    foreach ($currency_symbols as $key=>$value) {
        if (empty($value)) continue;
    	$is_selected = $selected == $key ? ' selected="selected"' : '' ;
    	$html .= "<option value=\"{$key}\" {$is_selected}>{$value}</option>";
    }
    $html .= "</select>\n";
	return $html;
}

function icf_get_currency_symbol($abbrev) {
    $currency_symbols = icf_get_currencies();
    if (isset($currency_symbols[$abbrev])) {
        return $currency_symbols[$abbrev];
    }
    return null;
}

/**
 * An array of currency abbreviations and HTML entities.
 * @return array
 * @author https://gist.github.com/Gibbs
 * @link https://gist.github.com/Gibbs/3920259
 */
function icf_get_currencies() {
    return array(
        'AED' => '&#1583;.&#1573;', // ?
        'AFN' => '&#65;&#102;',
        'ALL' => '&#76;&#101;&#107;',
        'AMD' => '',
        'ANG' => '&#402;',
        'AOA' => '&#75;&#122;', // ?
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => '&#402;',
        'AZN' => '&#1084;&#1072;&#1085;',
        'BAM' => '&#75;&#77;',
        'BBD' => '&#36;',
        'BDT' => '&#2547;', // ?
        'BGN' => '&#1083;&#1074;',
        'BHD' => '.&#1583;.&#1576;', // ?
        'BIF' => '&#70;&#66;&#117;', // ?
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => '&#36;&#98;',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTN' => '&#78;&#117;&#46;', // ?
        'BWP' => '&#80;',
        'BYR' => '&#112;&#46;',
        'BZD' => '&#66;&#90;&#36;',
        'CAD' => '&#36;',
        'CDF' => '&#70;&#67;',
        'CHF' => '&#67;&#72;&#70;',
        'CLF' => '', // ?
        'CLP' => '&#36;',
        'CNY' => '&#165;',
        'COP' => '&#36;',
        'CRC' => '&#8353;',
        'CUP' => '&#8396;',
        'CVE' => '&#36;', // ?
        'CZK' => '&#75;&#269;',
        'DJF' => '&#70;&#100;&#106;', // ?
        'DKK' => '&#107;&#114;',
        'DOP' => '&#82;&#68;&#36;',
        'DZD' => '&#1583;&#1580;', // ?
        'EGP' => '&#163;',
        'ETB' => '&#66;&#114;',
        'EUR' => '&#8364;',
        'FJD' => '&#36;',
        'FKP' => '&#163;',
        'GBP' => '&#163;',
        'GEL' => '&#4314;', // ?
        'GHS' => '&#162;',
        'GIP' => '&#163;',
        'GMD' => '&#68;', // ?
        'GNF' => '&#70;&#71;', // ?
        'GTQ' => '&#81;',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => '&#76;',
        'HRK' => '&#107;&#110;',
        'HTG' => '&#71;', // ?
        'HUF' => '&#70;&#116;',
        'IDR' => '&#82;&#112;',
        'ILS' => '&#8362;',
        'INR' => '&#8377;',
        'IQD' => '&#1593;.&#1583;', // ?
        'IRR' => '&#65020;',
        'ISK' => '&#107;&#114;',
        'JEP' => '&#163;',
        'JMD' => '&#74;&#36;',
        'JOD' => '&#74;&#68;', // ?
        'JPY' => '&#165;',
        'KES' => '&#75;&#83;&#104;', // ?
        'KGS' => '&#1083;&#1074;',
        'KHR' => '&#6107;',
        'KMF' => '&#67;&#70;', // ?
        'KPW' => '&#8361;',
        'KRW' => '&#8361;',
        'KWD' => '&#1583;.&#1603;', // ?
        'KYD' => '&#36;',
        'KZT' => '&#1083;&#1074;',
        'LAK' => '&#8365;',
        'LBP' => '&#163;',
        'LKR' => '&#8360;',
        'LRD' => '&#36;',
        'LSL' => '&#76;', // ?
        'LTL' => '&#76;&#116;',
        'LVL' => '&#76;&#115;',
        'LYD' => '&#1604;.&#1583;', // ?
        'MAD' => '&#1583;.&#1605;.', //?
        'MDL' => '&#76;',
        'MGA' => '&#65;&#114;', // ?
        'MKD' => '&#1076;&#1077;&#1085;',
        'MMK' => '&#75;',
        'MNT' => '&#8366;',
        'MOP' => '&#77;&#79;&#80;&#36;', // ?
        'MRO' => '&#85;&#77;', // ?
        'MUR' => '&#8360;', // ?
        'MVR' => '.&#1923;', // ?
        'MWK' => '&#77;&#75;',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => '&#77;&#84;',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => '&#67;&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#65020;',
        'PAB' => '&#66;&#47;&#46;',
        'PEN' => '&#83;&#47;&#46;',
        'PGK' => '&#75;', // ?
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PYG' => '&#71;&#115;',
        'QAR' => '&#65020;',
        'RON' => '&#108;&#101;&#105;',
        'RSD' => '&#1044;&#1080;&#1085;&#46;',
        'RUB' => '&#1088;&#1091;&#1073;',
        'RWF' => '&#1585;.&#1587;',
        'SAR' => '&#65020;',
        'SBD' => '&#36;',
        'SCR' => '&#8360;',
        'SDG' => '&#163;', // ?
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&#163;',
        'SLL' => '&#76;&#101;', // ?
        'SOS' => '&#83;',
        'SRD' => '&#36;',
        'STD' => '&#68;&#98;', // ?
        'SVC' => '&#36;',
        'SYP' => '&#163;',
        'SZL' => '&#76;', // ?
        'THB' => '&#3647;',
        'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
        'TMT' => '&#109;',
        'TND' => '&#1583;.&#1578;',
        'TOP' => '&#84;&#36;',
        'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => '',
        'UAH' => '&#8372;',
        'UGX' => '&#85;&#83;&#104;',
        'USD' => '&#36;',
        'UYU' => '&#36;&#85;',
        'UZS' => '&#1083;&#1074;',
        'VEF' => '&#66;&#115;',
        'VND' => '&#8363;',
        'VUV' => '&#86;&#84;',
        'WST' => '&#87;&#83;&#36;',
        'XAF' => '&#70;&#67;&#70;&#65;',
        'XCD' => '&#36;',
        'XDR' => '',
        'XOF' => '',
        'XPF' => '&#70;',
        'YER' => '&#65020;',
        'ZAR' => '&#82;',
        'ZMK' => '&#90;&#75;', // ?
        'ZWL' => '&#90;&#36;',
    );
}