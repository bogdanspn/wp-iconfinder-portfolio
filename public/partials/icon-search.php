<?php
/*
Template Name: Icon Search
*/
?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<?php get_header(); ?>
<?php echo do_shortcode('[iconfinder_search type="icon"]'); ?>    
<?php get_footer(); ?>

