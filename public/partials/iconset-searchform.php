<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div>   
    <!-- <h3>Search Icons</h3> -->
    <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform">
        <input type="text" name="s" value="<?php echo get_query_var('s'); ?>" placeholder="Search Icon Sets"/>
        <input type="hidden" name="post_type" value="iconset" />
        <input type="submit" alt="Search" value="Search" />
    </form>
</div>