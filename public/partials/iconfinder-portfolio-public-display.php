<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://iconfinder.com
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/public/partials
 */
 
if (! isset($options)) $options = array(
    'show_links' => 1,
    'show_price' => 1
);
$col_count = 4;

?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<div class="container">
    <div class="row">
        <?php if ($content['type'] == 'iconsets') : ?>
            <?php foreach ( $content['items'] as $iconset ) : ?>
                <?php if (empty($iconset['preview'])) : continue; endif; ?>
                <?php if (empty($iconset['permalink'])) : continue; endif; ?>
                <div class="col-md-<?php echo $col_count; ?> icf-item iconset-<?php echo $iconset['identifier']; ?>">
                    <?php if ($options['show_links']) : ?>
                    <a href="<?php echo $iconset['permalink']; ?>" target="_blank">
                        <?php endif; ?>
                        <img src="<?php echo $iconset['preview']; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
                    </a>
                    <p class="info">
                        <?php if ($options['show_links']) : ?>
                            <a href="<?php echo $iconset['permalink']; ?>" target="_blank">
                        <?php endif; ?>
                            <?php echo $iconset['name']; ?>
                        <?php if ($options['show_links']) : ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($options['show_price']) : ?>
                            <span class="price"><?php echo $iconset['price'] != "" ? "$" . $iconset['price'] : "Free"; ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ( $content['items'] as $icon ) : ?>
                <?php
                    $preview = $icon['previews']['@256'];
                    $price   = $icon['price']['price'];
                ?>
                <div class="col-md-2 icf-item icon-<?php echo $icon['icon_id']; ?>">
                    <?php if ($options['show_links']) : ?>
                    <a href="<?php echo $icon['permalink']; ?>" target="_blank">
                        <?php endif; ?>
                        <img src="<?php echo $preview['src']; ?>" alt="<?php echo implode(' ', $icon['tags']); ?> preview image" />
                    </a>
                    <p class="info">
                        <?php if ($options['show_links']) : ?>
                        <a href="<?php echo $icon['permalink']; ?>" target="_blank">
                            <?php endif; ?>
                            <?php # echo $iconset['name']; ?>
                            Buy on Iconfinder
                            <?php if ($options['show_links']) : ?>
                        </a>
                    <?php endif; ?>
                        <?php if ($options['show_price']) : ?>
                            <span class="price"><?php echo $price != "" ? "$" . $price : "Free"; ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>