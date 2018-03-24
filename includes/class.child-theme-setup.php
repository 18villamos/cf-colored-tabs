<?php

class CF_Layout {

    public function __construct($post_id) {
        $this->cf_menus   =  new CF_Menus($post_id);
        $this->post_id = $post_id;

        add_action( 'beans_uikit_enqueue_scripts',  array($this,'cf_enqueue_uikit_assets') );

        add_action( 'template_redirect',            array($this,'override_beans_defaults' ) );

        //This doesn't work for some reason, and not sure which action hook to use
        //It's in functions.php for now.
        add_action( '',                             array($this,'demo_footer_sidebars'));

        add_filter( 'beans_layout',                 array($this,'cf_blog_sidebar') );
    }

    public function cf_enqueue_uikit_assets() {

        beans_compiler_add_fragment( 'uikit', get_stylesheet_directory_uri() . '/style.less', 'less' );
        beans_uikit_enqueue_components( array( 'overlay','cover','flex','modal') );
    }

    public function override_beans_defaults() {
        $parent = wp_get_post_parent_id( $this->post_id );
        if (is_page($this->post_id) ) {
            $this->section_color    = $this->cf_menus->section_color();

            if(!$parent && $this->cf_menus->section_color=="no-section") {
                //
            } else {
                beans_replace_action('beans_post_title','cf_content_header_box_prepend_markup');
            }
        } else {
            $this->$section_color   = "no-section";
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
        beans_add_attribute('beans_footer','class', 'cf-header-' . $this->section_color);

        //Adds footer columns and centered copyright block.
        add_action( 'beans_footer_prepend_markup',        array($this,'cf_footer_columns'));
        add_action( 'beans_footer_after_markup',          array($this,'cf_footer_credit'));

        //Hides the default Beans copyright block.
        beans_modify_action_callback( 'beans_footer_content',   array($this,'hide_beans_copyright'));
    }

    public function demo_footer_sidebars() {
        //currently not used. See functions.php.
        register_sidebars(4, array('name'=>'Footer %d'));
    }

    public function cf_blog_sidebar() {

        if (is_single() || is_post_type_archive('post') ) {
            //per Beans, "content" + "sidebar primary"
            return 'c_sp';
        } else {
            //per Beans, "content", ie, no sidebar
            return 'c';
        }
    }

    public function cf_content_header() {

        //div with image or colored background
        echo beans_open_markup('cf_content_header_full','div',array('class'=>'cf-header cf-header-'. $this->section_color ));

            //centered container
            echo beans_open_markup('cf_content_header_container','div',array('class'=>'cf-content-header uk-container uk-container-center'));
                //div indented to work around logo
                //echo beans_open_markup('cf_content_header_indented','div',array('class'=>'cf-content-header'));

                    echo beans_open_markup('cf_content_header_grid','div',array('class'=>'uk-grid'));
                        echo beans_open_markup('cf_content_header_cell','div',array('class'=>'uk-width-medium-1-1'));

                            echo beans_open_markup('cf_content_header_box','div',array('class'=>'uk-panel-box cf-content-header-box uk-padding-remove uk-flex uk-flex-middle uk-flex-center'));

                            echo beans_close_markup('cf_content_header_box','div');
                        echo beans_close_markup('cf_content_header_cell','div');
                    echo beans_close_markup('cf_content_header_grid','div');

                //echo beans_close_markup('cf_content_header_indented','div');
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

    function hide_beans_copyright() {
        return "";
    }
}
