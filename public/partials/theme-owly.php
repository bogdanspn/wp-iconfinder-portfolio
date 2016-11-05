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

<div class="owl-carousel owl-theme" id="owl-carousel1">
<?php foreach ($items as $iconset) : ?>
  <div class="item">
      <a href="<?php echo $iconset['permalink']; ?>" rel="external" class="screenshot">
          <img src="<?php echo $iconset['preview']; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
      </a>
      <p>
          <a href="<?php echo $iconset['permalink']; ?>" rel="external" class="name"><?php echo $iconset['name']; ?></a>
          <span class="price"><?php echo $iconset['price'] != "" ? "$" . $iconset['price'] : "Free"; ?></span>
      </p>
  </div>
<?php endforeach; ?>
</div>


<script type="text/javascript">
    jQuery('#owl-carousel1').owlCarousel({
    loop:true,
    margin:22,
    dots:true,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true
        },
        600:{
            items:3,
            nav:false
        },
        1000:{
            items:4,
            nav:true,
            loop:false
        }
    }
    });
   jQuery('a[rel="external"]').attr('target', '_blank');
</script>