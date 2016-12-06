<?php
/**
 * The template for displaying portfolio posts
 *
 * @since 1.0
 */
get_header(); ?>
		<header class="page-titles">
			<div class="container clearfix">
				<div class="page-titles-wrap">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( get_post_meta( $post->ID, 'subtitle', true ) ) : ?>
						<h3 class="entry-subtitle"><?php echo get_post_meta( $post->ID, 'subtitle', true ) ?></h3>
					<?php endif; ?>
				</div>
			</div>
		</header>
		<section class="main">
			<div class="container">
				<div id="content">
					<div class="posts">
						<?php get_template_part('single-iconset-body'); ?>
					</div>
				</div>
			</div>
		</section>
		<?php get_footer(); ?>