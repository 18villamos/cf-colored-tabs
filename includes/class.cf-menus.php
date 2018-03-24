<?php
class CF_Menus {
    public function __construct($post_id) {

        //For demo purposes, creates a menu with items corresponding to the demo_menu_items() array
        add_action( 'init', array($this,'default_menu') );

        //Since Beans gives an ID to just about every element on the page, it is possible to hook into specific
        //menu items and assign them class names that will determine their background color via LESS.
        $this->assign_menu_item_classes("Navigation");

        //Removes the default triangle icon indicating a submenu in a vanilla Beans install.
        beans_remove_action('beans_menu_item_child_indicator');

        $this->post_id = $post_id;
        $this->demo_items = $this->demo_menu_items();

    }

    function demo_menu_items() {
        $menu_items = array (
            "Home"          => "home",
            "Who We Are"    => "who",
            "What We Do"    => "what",
            "Join Us"       => "join",
            "Contact"       => "contact"
        );

        return $menu_items;
    }

    public function default_menu() {
        /*
        * For demo purposes, populates the default Beans "Navigation" menu with these starter top-level pages and corresponding menu items.
        * In a real situation, the list of affected pages and menu items can be established dynamically in any number of ways.
        */
        $menu_name      = 'Navigation';
        $menu_obj       = wp_get_nav_menu_object( $menu_name );

        //Insert starter pages if they don't already exist
        foreach ($this->demo_items as $this_name => $this_slug) {
            if (!get_page_by_title( $this_name )) {
                wp_insert_post(
                    array(
                        'post_author'    => $user_id,
                        'post_content'   => 'This is the blank "' . $this_name . '" page.',
                        'post_title'     => $this_name,
                        'post_name'      => $this_slug,
                        'post_status' => 'publish',
                        'post_type' => 'page',
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


    function assign_menu_item_classes($which_menu) {
        //Adds a class to each menu item corresponding to the list of pages, in this case the demo list.

        $menu_object = wp_get_nav_menu_items($which_menu);
        $menu_ids = wp_list_pluck($menu_object,'ID','title');

        $menu_colors = array();

        foreach($this->demo_menu_items() as $item_name=>$item_slug) {
            $menu_colors[$item_slug] = $menu_ids[$item_name];
        }

        foreach ($menu_colors as $item_slug=>$item_id) {

            beans_add_attribute('beans_menu_item_' . $item_id,'class','menu-' . $item_slug);
        }
    }

    public function section_color() {
        //Determines what section a given current page is, based on its ancestry and assigns a section header background color.
        
        $section_color_map = array();

        foreach ($this->demo_menu_items() as $this_name=>$this_slug) {
            $this_page  = get_page_by_title($this_name);
            $section_color_map[$this_page->ID] = $this_slug;
        }

        $ancestors = array_reverse(get_ancestors($this->post_id, 'page', 'post_type' ));

        $root_ancestor = $ancestors[0];

        if (!$root_ancestor) {
            $root_ancestor = $this->post_id;
        }

        $color = $section_color_map[$root_ancestor];

        return $color;
    }
}
