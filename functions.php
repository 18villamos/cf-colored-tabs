<?php

// Include Beans. Nothing will work without this.
require_once( get_template_directory() . '/lib/init.php' );

// Goodies that make this child theme tick
require_once( get_stylesheet_directory() . '/includes/class.cf-menus.php');
require_once( get_stylesheet_directory() . '/includes/class.child-theme-setup.php');

function cf_call_layout() {
    global $wp_query;

    $post_id = $wp_query->get_queried_object_id();
    $cf_layout  =  new CF_Layout($post_id);
    add_action( 'template_redirect',            array($cf_layout,'override_beans_defaults' ) );
    add_action( 'beans_uikit_enqueue_scripts',  array($cf_layout,'cf_enqueue_uikit_assets') );
    add_filter( 'beans_layout',                 array($cf_layout,'cf_blog_sidebar') );
}

add_action('wp','cf_call_layout');

//Adds a sidebar area corresponding to each of the 4 footer columns created by CF_Layout
register_sidebars(4, array('name'=>__('CF Footer %d')));
