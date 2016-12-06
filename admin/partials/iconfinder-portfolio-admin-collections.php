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
    
    <?php if (! isset($items) || empty($items)) : ?>
        <p><?php _e($message, $this->plugin_name ); ?></p>
    <?php else : ?>
    
        <div class="notice notice-info">
            <?php 
                $info  = 'Only one collection can be displayed at a time. ';
                $info .= 'Copy &amp; paste any of the codes below to display the iconsets in that collection. ';
                $info .= 'You can also include other parameters such as `sort_by &amp; sort_order`, `omit`, and `count`';
            ?>
            <p><?php _e( $info, $this->plugin_name ); ?></p>
        </div>
        <ul>
            <?php foreach ($items as $collection) : ?>
                <li>
                    <p class="info"><?php echo $collection['name']; ?></p>
                    <p class="shortcode">
                        <input type="text" value="[iconfinder_portfolio collection=<?php echo $collection['collection_id']; ?>]" size="40" style="font-size: 1em; width: 294px;" onClick="this.select();" />
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>