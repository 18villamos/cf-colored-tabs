<?php

function cfdump($object) {
	ob_start();
	print_r($object);
	$dump = ob_get_contents();
	ob_end_clean();
	$output = "<pre>" . $dump . "</pre>";
	echo $output;
}

function resize_this($src,$width,$height,$crop_x,$crop_y) {
	$resize = beans_edit_image( $src,
		array(
			'resize' => array( $width,$height, array($crop_x,$crop_y ) )
		),
		'OBJECT'
	);
	return $resize->src;
}


// Include Beans. Nothing will work without this.
require_once( get_template_directory() . '/lib/init.php' );

register_sidebars(4, array('name'=>'Footer %d'));

// Goodies that make this child theme tick
require_once( get_stylesheet_directory() . '/includes/class.cf-menus.php');
require_once( get_stylesheet_directory() . '/includes/class.child-theme-setup.php');

function cf_post_id() {
    global $wp_query;

    $post_id = $wp_query->get_queried_object_id();
    $cf_layout  =  new CF_Layout($post_id);
}

add_action('wp','cf_post_id');
