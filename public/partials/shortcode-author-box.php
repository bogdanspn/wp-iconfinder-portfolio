<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="iconfinder-author-profile vcard">
    <div class="avatar">
        <?php echo get_avatar( get_the_author_meta( 'user_email', $user_id ), '128' ); ?>
    </div>
    <div class="bio">
        <h4 class="author-name fn n">Article by <?php the_author_meta( 'nickname', $user_id ); ?></h4>
        <?php if ( $show_bio ) : ?>
            <p class="author-bio">
                <?php the_author_meta( 'description', $user_id ); ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="iconsets col-4">
        <?php echo isset($author_iconsets) ? $author_iconsets : '' ; ?>
    </div>
</div>
