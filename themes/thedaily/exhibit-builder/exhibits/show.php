
<?php
queue_js_file(array('thedaily-exhibits'), 'js');
queue_js_string('
    jQuery("document").ready(function() {
        var levels = jQuery(".parent").length;
        jQuery(".current ul, .parent ul").parents("#exhibit-pages").addClass("max-tree-" + levels);
    });
');
echo head(array(
    'title' => metadata('exhibit_page', 'title') . ' &middot; ' . metadata('exhibit', 'title'),
    'bodyclass' => 'exhibits show'));
?>


<?php //CUSTOM PHP-----------------------------------------
    //set nav tree
    $pageTree = exhibit_builder_page_tree();
    //grab page slug
    $slug = metadata('exhibit_page', 'slug');
    //make nav null if in exhibit
    if ('exhibit')
        $pageTree = NULL;
    //if not in exhibit print nav
    if ($pageTree):
?>


<nav id="exhibit-pages">
    <h4><?php //not custom 
        echo exhibit_builder_link_to_exhibit($exhibit); ?></h4>
    <?php //print nav
        echo $pageTree;?>
</nav>
<?php endif; //END CUSTOM PHP--------------------------------
?>

<?php //added custom id?>
<h1><span class="exhibit-page" id="exhibit_header"><?php echo metadata('exhibit_page', 'title'); ?></span></h1>

<div id="exhibit-blocks">
<?php exhibit_builder_render_exhibit_page(); ?>
</div>

<?php //Custom PHP to dynamically generate links
    $children = exhibit_builder_child_pages();
        $slug_parent = $exhibit_page->getParent()['slug'];
        $i = 0;
        $wrap_count = 3;
        $row_track = 3;
        $list_size = sizeof($children);
        foreach($children as $child){
            $i+=1;
            
            if( (($list_size % $wrap_count) == 1) && (($list_size - $i) == 0) )
            {
                $row_track = 1;
            }
            if( (($list_size % $wrap_count) == 2) && (($list_size - $i) == 1) )
            {
                $row_track = 2;
            }
            
            if ($row_track == 1)
            {
                echo '<div class="schools_third_row">';
                $row_track = 0;
            }
            if ($row_track == 2)
            {
                echo '<div class="schools_second_row">';
                $row_track = 0;
            }
            if ($row_track == 3)
            {
                if ( ($i % $wrap_count) == 1 ){
                        echo '<div class="schools">';
                }
            }
            
            $child_slug = $child['slug'];
            $child_title = $child['title'];
            echo '<div class="wrap_formatting wrap_' . $child_slug . '">
                    <div class="overlay"><a class="box" href = "http://harley-comet.rit.edu/exhibits/show/' . $exhibit['slug'] . '/' . $slug_parent . '/'
                . $slug . '/' . $child_slug . '">' . $child_title . '</a></div>
                </div>';
            
            if($i % $wrap_count == 0)
            {
                echo '</div>';
            }
            
       }
?>


<?php echo foot();?>
