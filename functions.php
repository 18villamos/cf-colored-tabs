<?php

// Include Beans. Nothing will work without this.
require_once( get_template_directory() . '/lib/init.php' );

//This should ideally be in the CF_Layout class.  Not yet.
register_sidebars(4, array('name'=>'Footer %d'));

// Goodies that make this child theme tick
require_once( get_stylesheet_directory() . '/includes/class.cf-menus.php');
require_once( get_stylesheet_directory() . '/includes/class.child-theme-setup.php');

function cf_call_layout() {
    global $wp_query;

    $post_id = $wp_query->get_queried_object_id();
    $cf_layout  =  new CF_Layout($post_id);
}

add_action('wp','cf_call_layout');
