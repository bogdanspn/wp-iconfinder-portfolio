<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container clearfix icf-searchform iconset">
    <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform" class="search-form the-search-form clearfix">
        <fieldset class="the-search-fieldset">
            <input type="text" class="search-form-input text the-search-field" name="s" placeholder="Search Icons" value="<?php echo get_query_var('s'); ?>">
            <input type="submit" value="Search" class="submit the-search-button">
            <input type="hidden" name="post_type" value="iconset" />
        </fieldset>
    </form>
</div>