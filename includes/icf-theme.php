<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * @Package Iconfinder Portfolio
 * @author Iconfinder
 * @since 1.1.0
 */
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
 * Iconfinder Portfolio link hierarchy:
 *
 * - Icons always link to the parent iconset WP Custom Post
 * - Icon sets allow three different custom fields for links/buttons:
 * ---- purchase_html      -> Takes button html in a custom field. For instance, GetDPD button HTML
 * ---- purchase_shortcode -> Takes an EDD or other button/buy now link shorcode
 * ---- purchase_link      -> Takes a URL
 * ---- link to iconset on Iconfinder (with your referrer code)
 *
 * @param null|int $post_id
 */
function icf_the_permalink($post_id=null) {
    echo icf_get_the_permalink($post_id);
}

/**
 * @param null|int $post_id
 * @return string
 */
function icf_get_the_permalink($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return icf_get_permalink($post_id);
}

/**
 * @param null|int $post_id
 * @param string $text
 * @param array|null $attrs
 */
function icf_the_product_button($post_id=null, $text='', $attrs=null) {
    echo icf_get_the_product_button($post_id, $text, $attrs);
}

/**
 * @param null|int $post_id
 * @param string $text
 * @param array|null $attrs
 * @return mixed|null|string
 */
function icf_get_the_product_button($post_id=null, $text='', $attrs=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return icf_build_the_product_button($post_id, $text, $attrs);
}

/**
 * @param int $post_id
 * @param string $size
 * @param array|null $attr
 */
function icf_the_preview_by_id($post_id, $size='full', $attr=null) {
    echo icf_get_the_preview_by_id( $post_id, $size, $attr );
}

/**
 * @param int $post_id
 * @param string $size
 * @param array|null $attr
 * @return string
 */
function icf_get_the_preview_by_id($post_id, $size='full', $attr=null) {
    return get_the_post_thumbnail( $post_id, $size, $attr );
}

/**
 * @param null|int $post_id
 * @param string $size
 * @param array|null $attr
 */
function icf_the_preview($post_id=null, $size='full', $attr=null) {
    echo icf_get_the_preview($post_id, $size, $attr);
}

/**
 * @param null|int $post_id
 * @param string $size
 * @param array|null $attr
 * @param int|null $post_id
 * @return string
 */
function icf_get_the_preview($post_id=null, $size='full', $attr=null, $post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_the_post_thumbnail($post_id, $size, $attr);
}

/**
 * @param null|int $post_id
 */
function icf_the_price($post_id=null) {
    echo icf_get_the_price($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_price($post_id=null) {
    if (empty($post_id)) $post_id = get_the_ID();
    return get_post_meta( $post_id, 'price', true );
}

/**
 * @param null|int $post_id
 */
function icf_the_iconset_id($post_id=null) {
    echo icf_get_the_iconset_id($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_iconset_id($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'iconset_id', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_icon_id($post_id=null) {
    echo icf_get_the_icon_id($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_icon_id($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'icon_id', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_slug($post_id=null) {
    echo icf_get_the_slug($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_slug($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'iconset_identifier', true);
}

/**
 * @return bool|null
 */
function icf_show_links() {
    
    $show_links = get_val($GLOBALS, 'icf_show_links', null);
    if (null === $show_links) {
        $show_links = icf_get_option('use_purchase_link', false);
    }
    return is_true($show_links);
}

/**
 * @return bool|null
 */
function icf_show_price() {

    $show_price = get_val($GLOBALS, 'icf_show_price', null);
    if (null === $show_price) {
        $show_price = icf_get_option('show_price', false);
    }
    return is_true($show_price);
}

/**
 * @return mixed
 */
function icf_img_size() {
    return get_query_var('icf_img_size');
}

/**
 * @param null|int $post_id
 */
function icf_the_license_url($post_id=null) {
    echo icf_get_the_license_url($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_license_url($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'license_url', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_license_name($post_id=null) {
    echo icf_get_the_license_name($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_license_name($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'license_name', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_iconset_style_name($post_id=null) {
    echo icf_get_the_iconset_style_name($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_iconset_style_name($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'iconset_style_name', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_iconset_style_slug($post_id=null) {
    echo icf_get_the_iconset_style_slug($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_iconset_style_slug($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'iconset_style_identifier', true);
}

/**
 * @param null|int $post_id
 */
function icf_the_iconset_type($post_id=null) {
    echo icf_get_the_iconset_type($post_id);
}

/**
 * @param null|int $post_id
 * @return mixed
 */
function icf_get_the_iconset_type($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'iconset_type', true);
}

function icf_the_currency_symbol() {
    echo icf_get_the_currency_symbol();
}

/**
 * @return mixed|null
 */
function icf_get_the_currency_symbol() {
    return icf_get_currency_symbol(
        icf_get_option('currency_symbol', ICF_DEFAULT_CURRENCY)
    );
}

/**
 * @param null|int $post_id
 */
function icf_the_post_tags($post_id=null) {
    echo icf_get_the_post_tags($post_id);
}

/**
 * @param null|int $post_id
 * @return string
 */
function icf_get_the_post_tags($post_id=null) {
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return implode(', ', wp_get_post_terms( $post_id, 'icon_tag', array('fields' => 'names')));
}

function icf_pagination() {
    wpbeginner_numeric_posts_nav();
}

/**
 * Output the icon count.
 * @return void
 */
function icf_the_icon_count() {
    echo icf_get_icon_count();
}

/**
 * Return the icon count
 * @return int
 */
function icf_get_icon_count() {
    return icf_count_posts( ICF_POST_TYPE_ICON );
}

/**
 * Output the iconset count
 * @return void
 */
function icf_the_iconset_count() {
    echo icf_get_iconset_count();
}

/**
 * Return the iconset count
 * @return int
 */
function icf_get_iconset_count() {
    return icf_count_posts( ICF_POST_TYPE_ICONSET );
}

/**
 * @param null $post_id
 * @param null $args
 * @param bool $all
 * @return array
 */
function icf_get_children($post_id=null, $args=null, $all=true) {

    $children = null;
    $posts_per_page = -1;
    if (! $all) {
        $posts_per_page = icf_get_option(
            'search_posts_per_page',
            ICF_SEARCH_POSTS_PER_PAGE
        );
    }

    if (empty($post_id)) $post_id = get_the_ID();
    if (null === $args) {
        $args = array(
            'post_type'   => 'icon',
            'post_parent' => $post_id,
            'numberposts' => $posts_per_page,
            'post_status' => 'publish'
        );
    }
    return get_children($args);
}

function icf_icon_searchform() {
    icon_searchform();
}

function icf_iconset_searchform() {
    iconset_searchform();
}