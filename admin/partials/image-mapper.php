<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Iconfinder Portfolio
 */

?>
<div id="image-mapper">
    <input type="hidden" name="image-mapper-nonce" id="image-mapper-nonce" value="<?php echo wp_create_nonce( 'image-mapper-nonce' ); ?>" />
    <p id="image-mapper-message">To map images, select the image on the right (your uploads) and an Iconfinder image on the left and click, "Update".</p>
    <div class="inner">
        <div class="controls">
            <a href="javascript:void(0);" class="button button-primary image-updater">Update</a>
        </div>
        <div class="clear clearfix"></div>
        <div class="suggestions" style="display: none;">
            <h3>Suggested Matches</h3>
            <div class="content"></div>
            <!-- Suggest closest matches using Resemble.js -->
        </div>
        <div class="clear clearfix"></div>
        <div id="iconfinder-api-images" class="image-mapper-column">
            <h3>Iconfinder API Images</h3>
            <?php
            /**
             * 1. Ajax call to load all icon posts whose parent is the Iconset post that was clicked.
             * 2. data-args attribute set to JSON object with post_id of the Icon post
             * 3. Clicking preview image selects the corresponding post for update.
             * 4. When preview is clicked, closest match from user-uploaded images set as 'Suggested match' using Resemble.js
             * 5. Once an icon post has a featured image set, preview is grayed out (opacity set to 30%)
             */
            ?>
            <div class="content"></div>
        </div>
        <div id="user-uploaded-images" class="image-mapper-column">
            <h3>User-Uploaded Images <a href="javascript:void(0);" class="button button-secondary" id="icf-media-uploader" style="float: right;">Upload Media</a></h3>
            <div class="content">
                <?php
                /**
                 * 1. Ajax call to load all attachments uploaded by user.
                 * 2. data-args attribute set to JSON object with attachment_id of the the media attachment
                 * 3. Clicking preview image selects the corresponding attachment to be assigned as featured image of corresponding icon post also selected.
                 * 4. Clicking 'Update' makes Ajax call to save attachment as featured image on selected icon post.
                 * 5. Confirmation message show to user.
                 * 6. Once an image has been used as a featured image, its opacity is set to 30% to indicate it has been used. Can still be updated, however.
                 */
                ?>
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