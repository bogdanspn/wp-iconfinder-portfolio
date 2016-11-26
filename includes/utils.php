<?php

# if (! defined('WP_INC')) 
#    die('Nothing to see here.');

/**
 * Append or create a WP_Error. 
 * @param string $code
 * @param string $message
 * @param string $data
 * @param WP_Error $error
 * @return \WP_Error
 * 
 * @since 1.1.0
 */
function icf_append_error($result, $error, $messages=array()) {
    
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
 * @param type $name
 * @param type $default
 * @return type
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
 */
function icf_dump($what) {
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

/**
 * Saves error message strings as transient to be displayed by action callback.
 * @param mixed $notices
 * 
 * @since 1.1.0
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
 * @param string $live
 * 
 * @since 1.1.0
 */
function icf_admin_notices() {
    
    $types = array('success', 'error');

    foreach ($types as $type) {
        $transient_key = ICF_PLUGIN_NAME . '_' . $type;
        $messages = get_transient( $transient_key, true );
        delete_transient( $transient_key );

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
 * @return type
 * @author WPBeginner
 * @link http://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme/
 * @since 1.1.0
 */
function wpbeginner_numeric_posts_nav() {

	if( is_singular() )
		return;

	global $wp_query;

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
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
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
    if (locate_template('iconset-searchform.php') === '') {
        require_once(ICF_TEMPLATE_PATH . 'iconset-searchform.php');
    }
}
add_action('icf_iconset_searchform', 'iconset_searchform');

/**
 * Loads our custom search results page.
 * @global \WP_Query $wp_query
 * @param string $template
 * @return string
 * @since 1.1.0
 */
function template_chooser($template) {    
    global $wp_query;
    
    if (! is_search()) {
        return $template;
    }
    
    $post_type = get_query_var('post_type');
    if (empty($post_type)) {
        $post_type = $_REQUEST['post_type'];
    }
    $wp_query->set('post_type', $post_type);
    
    if ( is_search() && $post_type === ICF_POST_TYPE_ICON ) {
        $template = locate_template('icon-search.php', false);
        if ($template === '') {
            $template = ICF_TEMPLATE_PATH . 'icon-search.php';
        }
    }
    else if ( is_search() && $post_type === ICF_POST_TYPE_ICONSET ) {
        $template = locate_template('iconset-search.php', false);
        if ($template === '') {
            $template = ICF_TEMPLATE_PATH . 'iconset-search.php';
        }
    } 
    # icf_dump(get_defined_vars());
    # the_permalink();die();
    return $template;   
}
add_filter('template_include', 'template_chooser');

/**
 * Determine if the current code is being executed
 * inside the iconfinder-portfolio plugin.
 * @return boolean
 */
function is_iconfinder() {
    $is_iconfinder = defined('IS_ICONFINDER');
    if (! $is_iconfinder) {
        $post_type = get_val($_REQUEST, 'post_type');
        if (in_array($post_type, icf_get_setting('icf_post_types'))) {
            $is_iconfinder = true;
        }
    }
    return $is_iconfinder;
}

/**
 * Explicitly set the post_type query_var.
 * Note: I have no idea why this is necessary.
 * @param type $query
 */
function set_post_type_query($query) {
    
    if (! is_iconfinder()) {
        return $query;
    }
    
    if (! is_search()) {
        $query->set('post_type', 'iconset');
        return $query;
    }
    
    $post_type = get_query_var('post_type');
    if (empty($post_type)) {
        if (isset($_REQUEST['post_type'])) {
            $post_type = $_REQUEST['post_type'];
        }
    }
    $query->set('post_type', $post_type);
}
add_action('pre_get_posts', 'set_post_type_query');

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
 * @return type
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

function icf_get_permalink($post_id) {
    
    $permalink  = get_post_meta($post_id, 'product_link', true);
    
    if (empty($permalink)) {
        $parent_post_id = get_post_meta($post_id, 'parent_post_id', true);
        if (! empty($parent_post_id)) {
            $permalink  = get_post_meta($parent_post_id, 'product_link', true);
        }
    }
    if (empty($permalink)) {
        $ref = null;
        $username = icf_get_option('username');
        $identifier = get_post_meta($post_id, 'identifier', true);
        if (! empty($username) && ! empty($identifier)) {
            $ref = "?ref={$username}";
        }
        else {
            $identifier = get_post_meta($post_id, 'iconset_id', true);
        }
        if (! empty($identifier)) {
            $permalink = ICONFINDER_LINK_ICONSETS . $identifier . $ref;
        }
    }
    return $permalink;
}

function icf_currency_selector($selected=ICF_DEFAULT_CURRENCY, $selector_name='currency') {
    $currency_symbols = icf_get_currencies();
    $html  = "\n";
    $html .= "<select name=\"{$selector_name}\">";
    $html .= "<option>-- " .  __('Choose', ICF_PLUGIN_NAME) . " --</option>";
    foreach ($currency_symbols as $key=>$value) {
        if (empty($value)) continue;
    	$is_selected = $selected == $key ? ' selected="selected"' : '' ;
    	$html .= "<option name=\"{$key}\" {$is_selected}>{$value}</option>";
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