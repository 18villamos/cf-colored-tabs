<?php

class CF_Layout {

    public function __construct() {
        add_action( 'beans_uikit_enqueue_scripts',  array($this,'cf_enqueue_uikit_assets') );
        add_action( 'wp',                           array($this,'override_beans_defaults' ) );
        add_filter( 'beans_layout',                 array($this,'cf_set_sidebars') );
    }

    public function cf_enqueue_uikit_assets() {
        beans_compiler_add_fragment( 'uikit', get_stylesheet_directory_uri() . '/style.less', 'less' );
        beans_uikit_enqueue_components( array( 'overlay','cover','flex','modal') );
    }

    public function override_beans_defaults() {
        global $post;
        $parent = wp_get_post_parent_id( $post->ID );
        if (is_page($post->ID) ) {
            $section_color    = $this->section_color();

            if(!$parent && $this->cf_menus->section_color=="no-section") {
                //
            } else {
                beans_replace_action('beans_post_title','cf_content_header_box_prepend_markup');
            }
        } else {
            $section_color   = "no-section";
        }

        beans_remove_action('beans_site_title_tag');
        beans_remove_action('beans_post_image');
        beans_remove_action('beans_breadcrumb');

        beans_remove_attribute( 'beans_header',                 'class',    'uk-block');
        beans_remove_attribute( 'beans_breadcrumb_item_active', 'class',    'uk-text-muted');

        beans_add_attribute(    'beans_main',                   'class',    'uk-padding-remove');
        beans_add_attribute(    'beans_post_title',             'class',    'uk-margin-top');
        beans_add_attribute(    'beans_breadcrumb',             'class',    'uk-text-right');

        //Inserts a custom full-width content header area to be given a section-specific color
        add_action('beans_header_after_markup', array($this,'cf_content_header'));


        //Removes unwanted padding from the footer
        beans_add_attribute('beans_footer','class','uk-padding-remove');

        //Use the section color for the footer,too, if desired.
        beans_add_attribute('beans_footer','class', 'cf-header-' . $section_color);

        //Adds footer columns and centered copyright block.
        add_action( 'beans_footer_prepend_markup',        array($this,'cf_footer_columns'));
        add_action( 'beans_footer_after_markup',          array($this,'cf_footer_credit'));



        //Since Beans gives an ID to just about every element on the page, it is possible to hook into specific
        //menu items and assign them class names that will determine their background color via LESS.
        $this->assign_menu_item_classes("Navigation");

        //Removes the default triangle icon indicating a submenu in a vanilla Beans install.
        beans_remove_action('beans_menu_item_child_indicator');
    }

    public function cf_set_sidebars() {
        if (is_single() || is_post_type_archive('post') ) {
            //per Beans, "content" + "sidebar primary"
            return 'c_sp';
        } else {
            //per Beans, "content", ie, no sidebar
            return 'c';
        }
    }

    public function demo_menu_items() {
        $menu_items = array (
            "Home"          => "home",
            "Who We Are"    => "who",
            "What We Do"    => "what",
            "Join Us"       => "join",
            "Contact"       => "contact"
        );

        return $menu_items;
    }

    public function cf_content_header() {

        //div with image or colored background
        echo beans_open_markup('cf_content_header_full','div',array('class'=>'cf-header cf-header-'. $this->section_color() ));

            //centered container
            echo beans_open_markup('cf_content_header_container','div',array('class'=>'cf-content-header uk-container uk-container-center'));

                    echo beans_open_markup('cf_content_header_grid','div',array('class'=>'uk-grid'));
                        echo beans_open_markup('cf_content_header_cell','div',array('class'=>'uk-width-medium-1-1'));

                            echo beans_open_markup('cf_content_header_box','div',array('class'=>'uk-panel-box cf-content-header-box uk-padding-remove uk-flex uk-flex-middle uk-flex-center'));

                            echo beans_close_markup('cf_content_header_box','div');
                        echo beans_close_markup('cf_content_header_cell','div');
                    echo beans_close_markup('cf_content_header_grid','div');

            echo beans_close_markup('cf_content_header_container','div');

        echo beans_close_markup('cf_content_header_full','div');
    }

    public function cf_footer_columns() {

        echo beans_open_markup('cf_footer_container','div',array('class'=>'uk-container uk-container-center cf-footer-columns' ));
        echo beans_open_markup('cf_footer_grid','div',array('class'=>'uk-grid'));
            echo beans_open_markup('cf_footer_column1','div',array('class'=>'uk-width-medium-1-4'));
                dynamic_sidebar('Footer 1');
            echo beans_close_markup('cf_footer_column1','div');
            echo beans_open_markup('cf_footer_column1','div',array('class'=>'uk-width-medium-1-4'));
                dynamic_sidebar('Footer 2');
            echo beans_close_markup('cf_footer_column1','div');
            echo beans_open_markup('cf_footer_column1','div',array('class'=>'uk-width-medium-1-4'));
                dynamic_sidebar('Footer 3');
            echo beans_close_markup('cf_footer_column1','div');
            echo beans_open_markup('cf_footer_column1','div',array('class'=>'uk-width-medium-1-4'));
                dynamic_sidebar('Footer 4');
            echo beans_close_markup('cf_footer_column1','div');
        echo beans_close_markup('cf_footer_grid','div');
        echo beans_close_markup('cf_footer_container','div');
    }

    public function cf_footer_credit() {
        echo beans_open_markup('cf_copyright','div',array('class'=>'uk-width-1-1 uk-text-muted uk-text-center uk-text-small uk-margin-bottom'));
            echo "Copyright &copy; " . date(Y) . " by " . get_bloginfo("name") . ". All rights reserved.";
        echo beans_close_markup('cf_copyright','div');
    }

    public function assign_menu_item_classes($which_menu) {
        //Adds a class to each menu item corresponding to the list of pages, in this case the demo list.

        $menu_object    = wp_get_nav_menu_items($which_menu);
        $menu_ids       = wp_list_pluck($menu_object,'ID','title');

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
        global $post;
        $section_color_map = array();

        foreach ($this->demo_menu_items() as $this_name=>$this_slug) {
            $this_page  = get_page_by_title($this_name);
            $section_color_map[$this_page->ID] = $this_slug;
        }

        $ancestors = array_reverse(get_ancestors($this->post_id, 'page', 'post_type' ));

        $root_ancestor = $ancestors[0];

        if (!$root_ancestor) {
            $root_ancestor = $post->ID;
        }

        $color = $section_color_map[$root_ancestor];

        return $color;
    }
}
