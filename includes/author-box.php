<?php
/**
 * @package Iconfidner_Portfolio
 * Adds User profile logic to Iconfinder Portfolio.
 */

add_action( 'show_user_profile', 'icf_add_user_profile_fields' );
add_action( 'edit_user_profile', 'icf_add_user_profile_fields' );

/**
 * Adds form fields to the Profile admin screen.
 * @param \WP_User $user    The WP_User object
 */
function icf_add_user_profile_fields( $user ) { ?>

    <h3>Iconfinder Profile Information</h3>

    <table class="form-table">
        <tr>
            <th><label for="twitter">Iconfinder Username</label></th>
            <td>
                <input type="text" name="iconfinder_username" id="iconfinder_username" value="<?php echo esc_attr( get_the_author_meta( 'iconfinder_username', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Enter your Iconfinder.com username</span>
            </td>
        </tr>
        <tr>
            <th><label for="twitter">Twitter Username</label></th>
            <td>
                <input type="text" name="twitter_username" id="twitter_username" value="<?php echo esc_attr( get_the_author_meta( 'twitter_username', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Enter your Twitter.com username</span>
            </td>
        </tr>
    </table>
<?php }


add_action( 'personal_options_update',  'icf_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'icf_save_extra_profile_fields' );

/**
 * Saves the custom user profile fields.
 * @param   int   $user_id
 * @return  bool
 */
function icf_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    update_user_meta( $user_id, 'iconfinder_username', $_POST['iconfinder_username'] );
    update_user_meta( $user_id, 'twitter_username', $_POST['twitter_username'] );
}


/**
 * Displays an Author Box.
 * @param array $attrs      The shortcode attributes
 * @param bool  $refresh    Whether or not to refresh any cached version
 * @return string
 *
 * Allowed values:
 *
 *      username        Iconfinder username
 *      wp_username     WordPress username (may differ from Iconfinder username)
 *      bio             1 or 0 (whether or not to show bio)
 *      count           Number of iconset previews to show
 *      sets            Iconset IDs of specific sets to show (over-rides count)
 *
 * Username Priority:
 *    - If a wp_username value is given, it will over-ride the user metadata for current blog post's author
 *    - If an `username` (iconfinder username) is given, will be used for API call.
 *    - If no wp_username or username value is given, values from author of current post will be used.
 *
 *      In most cases the wp_username and Iconfinder username will likely be the same. But since the two
 *      systems are independent of one another, it is possible for the usernames to be different. If the
 *      Profile information you want to display is for the author of the current post, then the wp_username
 *      is not needed because it will be pulled from the current post's author metadata. But the shortcode
 *      allows you to display author bio information of any user and is not tightly coupled to the current
 *      blog post, which is why the wp_username can be explicitly indicated.
 *
 * @example
 *
 *      [iconfinder_author username=iconify wp_username=vectoricons bio=1 count=3]
 *      [iconfinder_author username=iconify wp_username=vectoricons bio=0 sets=1245,1246,1247]
 */
function icf_author_box( $attrs=array(), $refresh=false ) {

    $username     = get_val( $attrs, 'username', get_the_author_meta( 'iconfinder_username' ) );
    $wp_username  = get_val( $attrs, 'wp_username' );
    $show_bio     = is_true(get_val( $attrs, 'bio', true ));
    $count        = get_val( $attrs, 'count', 3 );
    $sets         = get_val( $attrs, 'sets' );

    /**
     * Create a unique key for caching the shortcode data.
     */

    $cache_key    = "authbox_{$username}_{$show_bio}_sets_";

    if (! empty($sets)) {
        $cache_key .= str_replace(',', '_', $sets);
    }
    else {
        $cache_key .= $count;
    }

    if ( ! $refresh && $cache = get_transient( $cache_key ) ) {
        $output = $cache;
    }
    else {
        $user_id = null;

        if (! empty($wp_username)) {
            $user = get_user_by( 'login', $wp_username );
            $user_id = $user->ID;
        }
        else if (! empty($username)) {
            $user_query = new WP_User_Query( array(
                'meta_key'   => 'iconfinder_username',
                'meta_value' => $username
            ));
            $results = $user_query->get_results();
            if (is_array( $results )) {
                $user = $results[0];
                $user_id = $user->ID;
            }
        }

        /**
         * Theme the shorcode output.
         */
        $theme_args = array(
            'username' => $username,
            'show_bio' => $show_bio,
            'count'    => $count,
            'user_id'  => $user_id,
            'author_iconsets' => icf_author_iconsets( $attrs, true )
        );

        $output = do_buffer( ICF_TEMPLATE_PATH . "shortcode-author-box.php", $theme_args );
        set_transient( $cache_key, $output, 3600 );
    }

    return $output;
}
add_shortcode( 'iconfinder_author', 'icf_author_box' );

/**
 * Show samples from a designer's Iconfinder profile.
 * @param array $attrs      The shortcode attributes
 * @param bool  $refresh    Whether or not to refresh any cached version
 * @return string
 *
 * Allowed values:
 *
 *      username    Iconfinder username
 *      count       The number of iconsets to show
 *      sets        Iconset IDs of specific sets to show (over-rides count)
 *
 *      In most cases the wp_username and Iconfinder username will likely be the same. But since the two
 *      systems are independent of one another, it is possible for the usernames to be different. If the
 *      Profile information you want to display is for the author of the current post, then the wp_username
 *      is not needed because it will be pulled from the current post's author metadata. But the shortcode
 *      allows you to display author bio information of any user and is not tightly coupled to the current
 *      blog post, which is why the wp_username can be explicitly indicated.
 *
 * Username Priority:
 *    - If a wp_username value is given, it will over-ride the user metadata for current blog post's author
 *    - If an `username` (iconfinder username) is given, will be used for API call.
 *    - If no wp_username or username value is given, values from author of current post will be used.
 *
 * @example
 *
 *      [iconfinder_author username=iconify count=3]
 *      [iconfinder_author username=iconify sets=1245,1246,1247]
 */
function icf_author_iconsets( $attrs=array(), $refresh=false ) {

    $username     = get_val( $attrs, 'username', get_the_author_meta( 'iconfinder_username' ) );
    $count        = get_val( $attrs, 'count', 3 );
    $sets         = get_val( $attrs, 'sets' );

    /**
     * Create a unique key for caching the shortcode data.
     */

    $cache_key    = "authbox_{$username}_sets_";

    if (! empty($sets)) {
        $cache_key .= str_replace(',', '_', $sets);
    }
    else {
        $cache_key .= $count;
    }

    /**
     * Try to retrieve the requested data from the cache.
     */

    $cached = null;

    if ( ! $refresh ) {
        $cached = get_transient( $cache_key );
    }

    if ( empty( $cached )) {
        /**
         * Get all of the user's iconsets
         */
        $iconsets = icf_get_user_iconsets( $username );
        if ( isset( $iconsets['items'] ) ) {
            $iconsets = $iconsets['items'];
        }

        /**
         * If specific sets have been specified, filter for those sets, or
         * if a count has been given, return the specified number of previews.
         */
        if ( ! empty($sets) ) {
            $iconsets = filter_by_iconsets( $iconsets, explode(',', $sets) );
        }
        else if ( $count > 0 ) {
            $iconsets = array_slice( $iconsets, 0, $count );
        }

        /**
         * Save this data for subsequent requests.
         */
        set_transient( $cache_key, $iconsets, 3600 );
    }

    /**
     * If no sets have been found, exit.
     */
    if (empty($iconsets)) return;

    /**
     * Theme the shorcode output.
     */
    $theme_args = array(
        'iconsets' => $iconsets,
        'username' => $username
    );

    return do_buffer( ICF_TEMPLATE_PATH . "shortcode-author-iconsets.php", $theme_args );
}

add_shortcode( 'iconfinder_author_iconsets', 'icf_author_iconsets' );