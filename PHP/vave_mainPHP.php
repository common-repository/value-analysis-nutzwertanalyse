<?php

if ( ! function_exists('vave_custom_post_type') ) {
  /* Register Custom Post Type */
  function vave_custom_post_type() {

  	$labels = array(
  		'name'                  => _x( 'Value Analysis', 'Post Type General Name', "value_analysis" ),
  		'singular_name'         => _x( 'Value Analysis', 'Post Type Singular Name', "value_analysis" ),
  		'menu_name'             => __( 'Value Analysis', "value_analysis" ),
  		'name_admin_bar'        => __( 'Value Analysis', "value_analysis" ),
  		'archives'              => __( 'Value Analysis Archives', "value_analysis" ),
  		'attributes'            => __( 'Value Analysis Attributes', "value_analysis" ),
  		'parent_item_colon'     => __( 'Parent Value Analysis:', "value_analysis" ),
  		'all_items'             => __( 'All Value Analysis', "value_analysis" ),
  		'add_new_item'          => __( 'Add New Value Analysis', "value_analysis" ),
  		'add_new'               => __( 'Add New', "value_analysis" ),
  		'new_item'              => __( 'New Value Analysis', "value_analysis" ),
  		'edit_item'             => __( 'Edit Value Analysis', "value_analysis" ),
  		'update_item'           => __( 'Update Value Analysis', "value_analysis" ),
  		'view_item'             => __( 'View Value Analysis', "value_analysis" ),
  		'view_items'            => __( 'View Value Analysis', "value_analysis" ),
  		'search_items'          => __( 'Search Value Analysis', "value_analysis" ),
  		'not_found'             => __( 'Not found', "value_analysis" ),
  		'not_found_in_trash'    => __( 'Not found in Trash', "value_analysis" ),
  		'featured_image'        => __( 'Featured Image', "value_analysis" ),
  		'set_featured_image'    => __( 'Set featured image', "value_analysis" ),
  		'remove_featured_image' => __( 'Remove featured image', "value_analysis" ),
  		'use_featured_image'    => __( 'Use as featured image', "value_analysis" ),
  		'insert_into_item'      => __( 'Insert into Value Analysis', "value_analysis" ),
  		'uploaded_to_this_item' => __( 'Uploaded to this Value Analysis', "value_analysis" ),
  		'items_list'            => __( 'Value Analysis list', "value_analysis" ),
  		'items_list_navigation' => __( 'Value Analysis list navigation', "value_analysis" ),
  		'filter_items_list'     => __( 'Filter Value Analysis list', "value_analysis" ),
  	);
  	$args = array(
  		'label'                 => __( 'Value Analysis', "value_analysis" ),
  		'description'           => __( 'Value Analysis - Nutzwertanalyse', "value_analysis" ),
  		'labels'                => $labels,
  		'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
  		'hierarchical'          => false,
  		'public'                => true,
  		'show_ui'               => true,
  		'show_in_menu'          => true,
  		'menu_position'         => 5,
  		'menu_icon'             => 'dashicons-analytics',
  		'show_in_admin_bar'     => true,
  		'show_in_nav_menus'     => true,
  		'can_export'            => true,
  		'has_archive'           => true,
  		'exclude_from_search'   => false,
  		'publicly_queryable'    => true,
  		'rewrite'               => false,
  		'capability_type'       => 'page',
  		'show_in_rest'          => false,
  	);
  	register_post_type( 'value_analysis', $args );

  }
add_action( 'init', 'vave_custom_post_type', 0 );
}

/* Filter the single_template with our custom function */
add_filter('single_template', 'vave_custom_template');

function vave_custom_template($single) {
    global $post;
    /* Checks for single template by post type */
    if ( $post->post_type == 'value_analysis' ) {
        if ( file_exists( vave_PATH . '/PHP/vave_page.php' ) ) {
            return vave_PATH . '/PHP/vave_page.php';
        }
    }
    return $single;
}
function vave_makeid($length = 10) {
    $randomString = '';

		/* zuerst immer einen kleinBuchstaben, so dass der String kompatibel zu keys in Arrays und objekten ist	*/
		$characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
		$randomString .= $characters[rand(0, $charactersLength - 1)];

    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charactersLength = strlen($characters);
    for ($i = 1; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function vave_addURLParameter($url, $paramName, $paramValue) {
     $url_data = parse_url($url);
     if(!isset($url_data["query"]))
         $url_data["query"]="";

     $params = array();
     parse_str($url_data['query'], $params);
     $params[$paramName] = $paramValue;
     $url_data['query'] = http_build_query($params);
     return vave_build_url($url_data);
}

function vave_build_url($url_data) {
   $url="";
   if(isset($url_data['host']))
   {
       $url .= $url_data['scheme'] . '://';
       if (isset($url_data['user'])) {
           $url .= $url_data['user'];
               if (isset($url_data['pass'])) {
                   $url .= ':' . $url_data['pass'];
               }
           $url .= '@';
       }
       $url .= $url_data['host'];
       if (isset($url_data['port'])) {
           $url .= ':' . $url_data['port'];
       }
   }
   $url .= $url_data['path'];
   if (isset($url_data['query'])) {
       $url .= '?' . $url_data['query'];
   }
   if (isset($url_data['fragment'])) {
       $url .= '#' . $url_data['fragment'];
   }
   return $url;
}
function vave_setUrl( $url ){
    echo("<script>history.replaceState({},'','$url');</script>");
}

?>
