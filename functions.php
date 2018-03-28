<?php

// Include Beans. Nothing will work without this.
require_once( get_template_directory() . '/lib/init.php' );


// Goodies that make this child theme tick
require_once( get_stylesheet_directory() . '/includes/class.cf_layout.php');

$cf_layout  =  new CF_Layout();

//For demo purposes, creates a menu with items corresponding to the demo_menu_items() array
add_action( 'init', 'default_menu');


function default_menu() {
    /*
    * For demo purposes, populates the default Beans "Navigation" menu with these starter top-level pages and corresponding menu items.
    * In a real situation, the list of affected pages and menu items can be established dynamically in any number of ways.
    */

    $demo_items     = CF_Layout::demo_menu_items();
    $menu_name      = 'Navigation';
    $menu_obj       = wp_get_nav_menu_object( $menu_name );

    //Insert starter pages if they don't already exist
    foreach ($demo_items as $this_name => $this_slug) {
        if (!get_page_by_title( $this_name )) {
            wp_insert_post(
                array(
                    'post_author'    => $user_id,
                    'post_content'   => 'This is the blank "' . $this_name . '" page.',
                    'post_title'     => $this_name,
                    'post_name'      => $this_slug,
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                )
            );
        }
    }

    $homepage = get_page_by_title( 'Home' );
    if ($homepage) {
        update_option( 'page_on_front', $homepage->ID );
        update_option( 'show_on_front', 'page' );
    }

    //Insert menu items corresponding to these pages. Only do this once.
    $menu_items = wp_get_nav_menu_items($menu_obj);
    if(count($menu_items) <=1) {
        $menu_id = wp_create_nav_menu($menu_name);

        //Beans will create a Home menu item by default, so remove this from our array.
        unset($this->demo_items["Home"]);

        foreach ($this->demo_items as $this_name => $this_slug) {
            wp_update_nav_menu_item($menu_obj->term_id, 0, array(
                'menu-item-title' =>  __($this_name),
                'menu-item-classes' => $this_slug,
                'menu-item-url' => home_url( '/'. $this_slug ),
                'menu-item-status' => 'publish')
            );
        }
    }
}


//Hides the default Beans copyright block.
beans_modify_action_callback( 'beans_footer_content',  'hide_beans_copyright');

//Adds a sidebar area corresponding to each of the 4 footer columns created by CF_Layout
register_sidebars(4, array('name'=>__('CF Footer %d')));

function hide_beans_copyright() {
    return "";
}
