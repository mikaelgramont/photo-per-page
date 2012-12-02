<?php
/*
Plugin Name: Photo-Per-Page
Description: Associates a photo with a page so that it can be displayed in its header.
Version: 0.1
Author: Mikael Gramont
License: GPL2
*/

add_action( 'add_meta_boxes', 'photo_per_page_add_custom_box' );
function photo_per_page_add_custom_box() {
    add_meta_box(
        'photo-per-page-meta',
        __( 'Header photo', 'photo_per_page' ), 
        'photo_per_page_meta_boxes',
        'page'
    );
}

add_action( 'save_post', 'photo_per_page_save_postdata' );
function photo_per_page_meta_boxes( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'photo_per_page_noncename' );
  
  $header_photo_url = $header_photo_title = '';
  $header_photo_meta = get_post_meta($post->ID, 'header_photo');
  if ($header_photo_meta && isset($header_photo_meta[0])) {
    $header_photo_url = $header_photo_meta[0][0];
    $header_photo_title = $header_photo_meta[0][1];
  }
  // The actual fields for data entry
  echo '<p><label for="photo_per_page_header_photo_url">';
       _e("Adress of the photo", 'photo_per_page' );
  echo '</label> ';
  echo '<input type="text" id="photo_per_page_url" name="photo_per_page_url" value="'.$header_photo_url.'" size="50" /></p>';

  echo '<p><label for="photo_per_page_header_photo_title">';
       _e("Title of the photo (optional)", 'photo_per_page' );
  echo '</label> ';
  echo '<input type="text" id="photo_per_page_title" name="photo_per_page_title" value="'.$header_photo_title.'" size="50" /></p>';
  
}

/* When the post is saved, saves our custom data */
function photo_per_page_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
      return;
  }

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if (!wp_verify_nonce($_POST['photo_per_page_noncename'], plugin_basename(__FILE__))) {
      return;
  }
  
  // Check permissions
  if ( !current_user_can( 'edit_page', $post_id ) ) {
      return;
  }
  
  update_post_meta($post_id, 'header_photo', array(
  	$_POST['photo_per_page_url'],
  	$_POST['photo_per_page_title']
  ));
  
}

/* Template tag */
function photo_per_page($post) {
	$url = $title = '';
 	$meta = get_post_meta($post->ID, 'header_photo');
  	if (!$meta || !$meta[0] || !$meta[0][0]) {
  		return '';
  	}
  	
    $url = $meta[0][0];
    $title = $meta[0][1];
	
	$html = <<<HTML
	<div id="photo-per-page-container">
	  <img src="$url" alt="" title="$title">
	</div>
	
HTML;
	return $html;
}