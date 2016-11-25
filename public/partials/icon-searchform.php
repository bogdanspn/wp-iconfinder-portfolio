<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container clearfix icf-searchform">
    <!-- <h3>Search Icons</h3> -->
    <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform">
        <input type="text" name="s" value="<?php echo get_query_var('s'); ?>" placeholder="Search Icons" class="search-field" />
        <input type="hidden" name="post_type" value="icon" />
        <input type="submit" alt="Search" value="Search" class="submit" />
    </form>
</div>