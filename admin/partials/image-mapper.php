<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Iconfinder Portfolio
 */

?>
<div id="image-mapper">
    <input type="hidden" name="image-mapper-nonce" id="image-mapper-nonce" value="<?php echo wp_create_nonce( 'image-mapper-nonce' ); ?>" />
    <div class="suggestions">
        <h3><?php _e( 'Suggested', ICF_PLUGIN_NAME ); ?></h3>
        <div class="content"></div>
    </div>
    <p id="image-mapper-message">
        <?php _e( 'To map images, select the image on the right (your uploads) and an Iconfinder image on the left and click, "Update".', ICF_PLUGIN_NAME ); ?>
    </p>
    <div class="inner">
        <div class="controls">
            <a href="javascript:void(0);" class="button button-primary image-updater"><?php _e( 'Update', ICF_PLUGIN_NAME ); ?></a>
        </div>
        <div class="clear clearfix"></div>
        <div id="iconfinder-api-images" class="image-mapper-column">
            <h3><?php _e( 'Iconfinder API Images', ICF_PLUGIN_NAME ); ?></h3>
            <div class="content"></div>
        </div>
        <div id="user-uploaded-images" class="image-mapper-column">
            <h3><?php _e( 'User-Uploaded Images', ICF_PLUGIN_NAME ); ?> <a href="javascript:void(0);" class="hide-in-use"><?php _e( 'Hide Used Images', ICF_PLUGIN_NAME ); ?></a> <a href="javascript:void(0);" class="button button-secondary" id="icf-media-uploader" style="float: right;"><?php _e( 'Upload Media', ICF_PLUGIN_NAME ); ?></a></h3>
            <div class="content">
                <ul>
                    <?php foreach ($images as $image) : ?>
                        <?php $class = ! empty($image->post_parent) ? 'in-use' : 'not-in-use'; ?>
                        <li class="<?php echo $class; ?> icon-<?php echo $image->post_parent; ?>"><img data-properties='{"attachment_id": "<?php echo $image->ID; ?>"}' src="<?php echo $image->guid; ?>" title="Attachment ID <?php echo $image->ID; ?>" /></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="clear clearfix"></div>
        <div class="controls" style="display: none;">
            <a href="javascript:void(0);" class="button button-primary image-updater">Update</a>
        </div>
    </div>
</div>