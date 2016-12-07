<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Iconfinder portfolio global settings
 */

define('ICONFINDER_DOMAIN',        'iconfinder.com');
define('ICONFINDER_URL',           'http://iconfinder.com/'); 
define('ICONFINDER_API_URL',       'https://api.iconfinder.com/v2/');
define('ICONFINDER_CDN_URL',       'https://cdn4.iconfinder.com/');
define('ICONFINDER_LINK_ICONS',    'https://www.iconfinder.com/icons/');
define('ICONFINDER_LINK_ICONSETS', 'https://www.iconfinder.com/iconsets/');

/**
 * We define this here and take an indirect approach to using it
 * so that if details like the locations of images changes on the
 * Iconfinder side, we only have one file to update in the WP Plugin
 */

define('ICF_TOKEN_SIZE', '@SIZE');
define('ICF_TOKEN_IDENTIFIER', '@IDENTIFIER');
define('ICF_ICONSET_PREVIEW_URL',  ICONFINDER_CDN_URL . 'data/iconsets/previews/' . ICF_TOKEN_SIZE . '/' . ICF_TOKEN_IDENTIFIER . '.png');


define('ICONFINDER_TYPE_PREMIUM',      'premium');
define('ICONFINDER_TYPE_FREE',         'free');

define('ICF_POST_TYPE_ICON', 'icon');
define('ICF_POST_TYPE_ICONSET', 'iconset');

define('ICONFINDER_API_SSLVERIFY',     false);
define('ICONFINDER_API_MAX_COUNT',     100);

define('ICF_DEFAULT_CURRENCY', 'USD');

define(
    'ICONFINDER_SERVER_ERROR_MSG', 
    'Our site seems to be experiencing some technical difficulties. We are working on the problem and should have it restored soon. Thanks for your patience'
);

define(
    'ICF_ADVANCED_MODE_ONLY',
    'This feature can only be used in Advanced Mode.'
);

define( 'ICF_PLUGIN_MODE_ADVANCED', 'advanced' );
define( 'ICF_PLUGIN_MODE_BASIC',    'basic' );
define( 'ICF_PLUGIN_MODE_DEFAULT',  'basic' );

define( 'ICF_KEY_LAST_ICONSET_ID', 'icf_last_iconset_id' );

define(
    'ICF_CONFIRM_DELETE',
    'Are you sure you want to delete this item? It cannot be undone.'
);

define(
    'ICF_CONFIRM_NUKE CONTENT',
    'Are you sure you want to delete all of your imported data? This cannot be undone.'
);

define(
    'ICF_CONFIRM_UPDATE',
    'Are you sure you want to update this item? Your local data will be overwritten by the data on Iconfinder.'
);

define(
    'ICF_CONFIRM_IMPORT',
    'Are you sure you want to import this item? The import may take up to a minute depending on how many icons are in your set.'
);

define(
    'ICF_ENTER_API_CREDENTIALS',
    'Enter your API credentials on the API Settings page to list your iconsets here'
);

define('ICF_SEARCH_POSTS_PER_PAGE', 24);
define('ICF_SEARCH_POSTS_PER_PAGE_MAX', 100);

define('ICF_DEFAULT_PREVIEW_SIZE', 'medium-2x');
define('ICF_DEFAULT_ICON_PREVIEW_SIZE', '@128');

/**
 * We use a function here rather than a variable so that we don't need to use globals.
 * Use icf_settings() in 
 * @link iconfinder-portfolio-functions.php to get a given value.
 * @return mixed
 */
function _icf_settings() {
    return array(
        
        'icf_post_types' => array(ICF_POST_TYPE_ICON, ICF_POST_TYPE_ICONSET),

        /**
         * We don't want to import all of the preview images so
         * we limit what is imported to only a few reasonable sizes.
         */

        'icon_import_sizes' => array('@64', '@128', '@256', '@512'),

        /**
         * The default preview size that is set as the post thubmnail (featured image)
         */

        'icon_default_preview_size' => ICF_DEFAULT_ICON_PREVIEW_SIZE ,

        /**
         * Default iconset preview image size
         */
        
        'iconset_default_preview_size' => ICF_DEFAULT_PREVIEW_SIZE,

        /**
         * Available iconset preview sizes
         */
        
        'iconset_preview_sizes' => array('medium', 'medium-2x', 'large'),

        /**
         * Valid API sub-paths
         */
        
        'valid_api_channels' => array('iconsets', 'collections', 'categories', 'styles'),

        /**
         * Commonly-used synonyms for different icon styles (we allow all of these)
         */
        
        'style_aliases' => array(
            'line'        => 'outline',
            'filled-line' => 'filled_outline',
            'filled'      => 'filled_outline',
            '3-d'         => '3d'
        ),
        
        'license_aliases' => array(
            'free'        => 'Creative Commons',
            'premium'     => 'Basic license',
            'filled'      => 'filled_outline',
            '3-d'         => '3d'
        ),

        /**
         * The default template for the iconste search shortcode output.
         */

        'iconset_search_shortcode_template' => 'iconset-search-body.php',

        /**
         * The default template for the icon search shortcode output.
         */

        'icon_search_shortcode_template' => 'icon-search-body.php',

        /**
         * Default posts, search results per page. This setting will only
         * apply to results shown within the Iconfinder Porfolio output.
         * It does not affect the global WP settings and overrides
         * the global WP settins inside the plugin.
         */
        
        'posts_per_page' => ICF_SEARCH_POSTS_PER_PAGE,

        /**
         * Shortcode default values
         */
        
        'shortcode_defaults' => array(
                'id'         => '',
                'username'   => null,
                'count'      => ICF_SEARCH_POSTS_PER_PAGE,
                'style'      => '',
                'type'       => '',
                'collection' => '',
                'iconset'    => '',
                'sets'       => '',
                'categories' => '',
                'theme'      => '',
                'sort_by'    => 'name',
                'sort_order' => SORT_DESC,
                'omit'       => '',
                'img_size'   => ICF_DEFAULT_PREVIEW_SIZE,
                'show_links' => true,
                'show_price' => true,
                'buy_link'   => true,
                'paginate'   => false
        ),

        /**
         * Default options for the plugin
         */
        
        'plugin_default_options'  => array(
            'api_client_id'       => null,
            'api_client_secret'   => null,
            'username'            => null,
            'plugin_mode'         => 'basic',
            'use_powered_by_link' => true,
            'use_purchase_link'   => true,
            'icon_preview_sizes'  => ICF_DEFAULT_ICON_PREVIEW_SIZE
        )
    );
}

/**
 * @return mixed
 */
function register_default_settings() {
    return icf_get_setting('plugin_default_options');
}

/**
 * Default options for the Gee Search Engine. We are not running the plugin as a plugin
 * but rather as a behind-the-scenese search engine inside of our plugin. There is no
 * UI for the search engine so we hard-code the settings.
 * @return array
 */
function _gee_searchplus_options() {
    
    /*
    $options = array();
    $options['version'] = GEE_SP_VERSION;
    $options['enable'] = 1;
    $options['query_type'] = 'and'; // since 1.3.0
    $options['order_type'] = 'relevance'; // since 1.3.0
    $options['stopwords'] = 0; // do not use stopwords
    $options['custom_fields'] = 0; // do not search on custom fields
    $options['highlight'] = 0; // do not highlight searched terms
    $options['highlight_color'] = '#ffffff'; // highlight color
    $options['highlight_area'] = '#content'; // highlight area
    $options['specific_stops'] = 'word1,word2';
    $options['enable_tax'] = 1; // Enable search on taxonomies by default
    update_option( 'gee_searchplus_options', $options );
    */
    
    return array(
        'version' => GEE_SP_VERSION,
        'enable'  => 1,
        'query_type' => 'or',
        'order_type' => 'relevance',
        'stopwords'  => 0,
        'custom_fields' => 0,
        'highlight' => 0,
        'highlight_color' => '#ffffff',
        'highlight_area' => '#content',
        'specific_stops' => 'word1,word2',
        'enable_tax' => 1
    );
}