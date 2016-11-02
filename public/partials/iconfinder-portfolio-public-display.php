<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/public/partials
 */
?>
<div class="gs_drib_area gs_drib_theme1">
    <div class="container">
        <div class="row">
            <?php foreach ($iconsets as $iconset) : ?>
                <div class="col-md-3 drib-shots">
                    <a href="<?php echo $iconset['permalink']; ?>" target="_blank">
                        <img src="<?php echo $iconset['preview']; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
                    </a>
                    <p style="font-size: .8em;">
                        <a href="<?php echo $iconset['permalink']; ?>" target="_blank"><?php echo $iconset['name']; ?></a> 
                        <span style="float: right; margin-right: 10px;"><?php echo $iconset['price'] != "" ? "$" . $iconset['price'] : "Free"; ?></span>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>