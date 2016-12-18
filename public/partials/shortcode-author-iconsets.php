<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( $iconsets ) : ?>
    <div class="iconfinder-iconsets">
         <ul>
            <?php foreach ( $iconsets as $iconset ) : ?>
                <?php
                $identifier = get_val( $iconset, 'identifier' );
                $preview    = get_iconfinder_preview_url( 'medium', $identifier );
                $alt_text   = get_val( $iconset, 'name' );
                ?>
                <li>
                    <a href="https://iconfinder.com/iconsets/<?php echo $identifier; ?>?ref=<?php echo $username; ?>" target="_blank">
                        <img src="<?php echo $preview; ?>" title="<?php echo $alt_text; ?>" alt="<?php echo $alt_text; ?>" />
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<p class="iconfinder-username clear">
    <a href="https://iconfinder.com/<?php echo $username; ?>?ref=<?php echo $username; ?>" title="Follow <?php echo $username; ?> on Iconfinder" target="_blank">Follow <?php echo $username; ?> on Iconfinder.com</a>
</p>