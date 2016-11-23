<?php
/**
 * Iconfinder portfolio global settings
 */
 
define( 'ICF_PLUGIN_NAME', 'iconfinder_portfolio' );

define('ICONFINDER_URL',     'http://iconfinder.com/'); 
define('ICONFINDER_API_URL', 'https://api.iconfinder.com/v2/');
define('ICONFINDER_CDN_URL', 'https://cdn4.iconfinder.com/');

define('ICONFINDER_TYPE_PREMIUM',      'premium');
define('ICONFINDER_TYPE_FREE',         'free');

define('ICONFINDER_API_SSLVERIFY',     false);
define('ICONFINDER_API_MAX_COUNT',     100);

define(
    'ICONFINDER_SERVER_ERROR_MSG', 
    'Our site seems to be experiencing some technical difficulties. We are working on the problem and should have it restored soon. Thanks for your patience'
);

define( 'ICF_PLUGIN_MODE_ADVANCED', 'advanced' );
define( 'ICF_PLUGIN_MODE_BASIC', 'basic' );
define( 'ICF_PLUGIN_MODE_DEFAULT', 'basic' );

define(
    'ICF_CONFIRM_DELETE',
    'Are you sure you want to delete this item? It cannot be undone.'
);

define(
    'ICF_CONFIRM_UPDATE',
    'Are you sure you want to update this item? Your local data will be overwritten by the data on Iconfinder.'
);




/**
 * Global Iconfinder Portfolio settings. Be careful changing these.
 */
$icf_settings = array(
    
    // We don't want to import all of the preview images so 
    // we limit what is imported to only a few reasonable sizes.
    
    'icon_import_sizes' => array('@64', '@128', '@256', '@512'),
    
    // The default preview size that is set as the post thubmnail (featured image)
    
    'icon_default_preview_size' => '@128'
);

/**
 * We use a function here rather than a variable so that we don't need to use globals.
 * Use icf_settings() in 
 * @link iconfinder-portfolio-functions.php to get a given value.
 * @return <mixed>
 */
function _icf_settings() {
    return array(
    
        // We don't want to import all of the preview images so 
        // we limit what is imported to only a few reasonable sizes.

        'icon_import_sizes' => array('@64', '@128', '@256', '@512'),

        // The default preview size that is set as the post thubmnail (featured image)

        'icon_default_preview_size' => '@128'
    );
}