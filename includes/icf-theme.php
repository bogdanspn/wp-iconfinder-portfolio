<?php

/* 
 * @Package Iconfinder Portfolio
 * @author Iconfinder
 * @since 1.1.0
 */

/**
 * Search for one of our templates in the plugin theme path 
 * first, then our plugin partials path.
 * @param type $template
 * @return type
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
 * These functions are for use inside the WP loop on Iconset posts.
 * They will technically work with regular WP posts but won't return 
 * any values.
 * 
 * @important THESE ONLY WORK INSIDE THE LOOP.
 */

/**
 * Output the permalink for the icon or iconset. The behavior of 
 * this theme hook is not quite the same as the standard wp_ hook.
 * We are not really interested in the permalink to the post 
 * but rather the product link associated with the icon or 
 * iconset.
 * 
 * If the current post is an icon, and does not have the 
 * `product_link` custom field, the value will be 
 * grabbed from the parent post (iconset).
 * 
 * @example
 * 
 *   <a href="<?php icf_the_permalink(); ?>"><?php the_title();</a>
 * 
 * Ouputs:
 * 
 *   <a href="http://mysite.com/product-one">Product One</a>
 * 
 * @param type $post_id
 */
function icf_the_permalink($post_id=null) {
    echo icf_get_the_permalink($post_id);
}

function icf_get_the_permalink($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return icf_get_permalink($post_id);
}

function icf_the_preview($size='full', $attr=null) {
    echo icf_get_the_preview($size, $attr);
}

function icf_get_the_preview($size='full', $attr=null) {
    return the_post_thumbnail($size, $attr);
}

function icf_the_price() {
    echo icf_get_the_price();
}

function icf_get_the_price() {
    return get_post_meta( get_the_ID(), 'price', true );
}

function icf_the_iconset_id() {
    echo icf_get_the_iconset_id();
}

function icf_get_the_iconset_id() {
    return get_post_meta(get_the_ID(), 'iconset_id', true);
}

function icf_the_icon_id() {
    echo icf_get_the_icon_id();
}

function icf_get_the_icon_id() {
    return get_post_meta(get_the_ID(), 'icon_id', true);
}

function icf_the_slug() {
    echo icf_get_the_identifier();
}

function icf_get_the_slug() {
    return get_post_meta(get_the_ID(), 'iconset_identifier', true);
}

function icf_show_links() {
    return icf_get_option('use_purchase_link', false);
}

function icf_the_license_url() {
    echo icf_get_the_license_url();
}

function icf_get_the_license_url() {
    return get_post_meta(get_the_ID(), 'license_url', true);
}

function icf_the_license_name() {
    echo icf_get_the_license_name();
}

function icf_get_the_license_name() {
    return get_post_meta(get_the_ID(), 'license_name', true);
}

function icf_the_iconset_style_name() {
    echo icf_get_the_iconset_style_name();
}

function icf_get_the_iconset_style_name() {
    return get_post_meta(get_the_ID(), 'iconset_style_name', true);
}

function icf_the_iconset_style_slug() {
    echo icf_get_the_iconset_style_slug();
}

function icf_get_the_iconset_style_slug() {
    return get_post_meta(get_the_ID(), 'iconset_style_identifier', true);
}

function icf_the_iconset_type() {
    echo icf_get_the_iconset_type();
}

function icf_get_the_iconset_type() {
    return get_post_meta(get_the_ID(), 'iconset_type', true);
}

function icf_the_currency_symbol() {
    echo icf_get_the_currency_symbol();
}

function icf_get_the_currency_symbol() {
    return icf_get_currency_symbol(
        icf_get_option('currency_symbol', ICF_DEFAULT_CURRENCY)
    );
}

function icf_the_product_link() {
    echo icf_get_the_product_link();
}

function icf_the_post_tags() {
    echo icf_get_the_post_tags();
}

function icf_get_the_post_tags() {
    return implode(', ', wp_get_post_terms( get_the_ID(), 'icon_tag', array('fields' => 'names')));
}