<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (! isset($search_collection_id) || empty($search_collection_id) ) {
    $search_collection_id = null;
    $test_input = get_val($_REQUEST, 'search_collection_id');
    if ( is_numeric($test_input) && intval($test_input) < pow(2, 24) ) {
        $search_collection_id = $test_input;
    }
}
?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<div class="container clearfix icf-searchform iconset">
    <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform" class="search-form the-search-form clearfix">
        <fieldset class="the-search-fieldset">
            <input type="text" class="search-form-input text the-search-field" name="s" placeholder="Search Icons" value="<?php echo get_query_var('s'); ?>">
            <input type="submit" value="Search" class="submit the-search-button">
            <input type="hidden" name="post_type" value="iconset" />
            <input type="hidden" name="search_collection_id" value="<?php echo $search_collection_id; ?>" />
        </fieldset>
    </form>
</div>