<?php

/**
 * Plugin Name: FCC Featured Image Caption
 * Plugin URI:  http://www.forumcomm.com/
 * Author:      Forum Communications Company (Ryan Veitch, Braden Stevenson)
 * Author URI:  http://www.forumcomm.com/
 * Version:     0.0.1
 * Description: Adds standardized featured image caption and attribution functionality to posts.
 * License:     GPL v2 or later
 * Text Domain: fcc-Featured-Image-Caption
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/*--------------------------------------------------------------
# FEATURED IMAGE CAPTION FUNCTIONS
--------------------------------------------------------------*/

wp_enqueue_script("jquery");

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'imagecap_meta_boxes_setup' );
add_action( 'load-post-new.php', 'imagecap_meta_boxes_setup' );

/* Meta box setup function. */
function imagecap_meta_boxes_setup() {
  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'save_featured_image_caption_text_meta', 10, 2 );
}




/* Save the meta box's post metadata. */
function save_featured_image_caption_text_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
  	if ( ! isset( $_POST['featured_image_caption_nonce'] ) || ! wp_verify_nonce( $_POST['featured_image_caption_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
	}

	/* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

		/* Get the posted data */
  $new_meta_value = ( isset( $_POST['image-cap'] ) ? $_POST['image-cap'] : '' );

	/* Get the meta key. */
  $meta_key = 'featured_image_caption';

	/* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
  	elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value );
			}

		/* If there is no new meta value but an old value exists, delete it. */
  	elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );

		$new_attrib_value = ( isset( $_POST['image-attrib'] ) ? $_POST['image-attrib'] : '' );

		/* Get the meta key. */
	  $attrib_key = 'featured_image_attribution';

		/* Get the meta value of the custom field key. */
	  $attrib_value = get_post_meta( $post_id, $attrib_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
	  if ( $new_attrib_value && '' == $attrib_value )
	    add_post_meta( $post_id, $attrib_key, $new_attrib_value, true );

			/* If the new meta value does not match the old value, update it. */
	  	elseif ( $new_attrib_value && $new_attrib_value != $attrib_value ) {
				update_post_meta( $post_id, $attrib_key, $new_attrib_value );
				}

			/* If there is no new meta value but an old value exists, delete it. */
	  	elseif ( '' == $new_attrib_value && $attrib_value )
	    delete_post_meta( $post_id, $attrib_key, $attrib_value );

			$new_url_value = ( isset( $_POST['image-url'] ) ? $_POST['image-url'] : '' );

			/* Get the meta key. */
			$url_key = 'featured_image_attribution_url';

			/* Get the meta value of the custom field key. */
			$url_value = get_post_meta( $post_id, $url_key, true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_url_value && '' == $url_value )
				add_post_meta( $post_id, $url_key, $new_url_value, true );

				/* If the new meta value does not match the old value, update it. */
				elseif ( $new_url_value && $new_url_value != $url_value ) {
					update_post_meta( $post_id, $url_key, $new_url_value );
					}

				/* If there is no new meta value but an old value exists, delete it. */
				elseif ( '' == $new_url_value && $url_value )
				delete_post_meta( $post_id, $url_key, $url_value );


}



function prefix_featured_image_meta( $content ) {
	wp_nonce_field( basename( __FILE__ ), 'featured_image_caption_nonce' );

	global $post;
	$cap_text = __( 'Caption Text', 'prefix' );
	$cap_id = 'image-cap';
	$cap_value = esc_attr( get_post_meta( $post->ID, 'featured_image_caption', true ) );
	$cap_label = '<p><label for="' . $cap_id . '">' . $cap_text . '</label><br/><textarea
	style="box-sizing:border-box;width:100%;min-height:40px;padding-left:5px;padding-right:5px;" name="' . $cap_id . '"
	id="' . $cap_id . '">' . $cap_value . '</textarea></p>';

	$attrib_text = __( '<strong>Source Attribution</strong><br/> Text', 'prefix' );
	$attrib_id = 'image-attrib';
	$attrib_value = esc_attr( get_post_meta( $post->ID, 'featured_image_attribution', true ) );
	$attrib_label = '<p><label for="' . $attrib_id . '">' . $attrib_text . '</label><br/><input type="text"
	style="box-sizing:border-box;width:100%;padding-left:5px;padding-right:5px;" name="' . $attrib_id . '"
	id="' . $attrib_id . '" value="' . $attrib_value . '"></p>';

	$url_text = __( 'URL', 'prefix' );
	$url_id = 'image-url';
	$url_value = esc_attr( get_post_meta( $post->ID, 'featured_image_attribution_url', true ) );
	$url_label = '<p><label for="' . $url_id . '">' . $url_text . '</label><br/><input type="text"
	style="box-sizing:border-box;width:100%;padding-left:5px;padding-right:5px;" name="' . $url_id . '"
	id="' . $url_id . '" value="' . $url_value . '"></p>';

    $text = __( 'Open in new window', 'prefix' );
    $id = 'open_featured_new_window';
    $value = esc_attr( get_post_meta( $post->ID, $id, true ) );
    $label = '<p><label for="' . $id . '" class="selectit"><input name="' . $id . '"
		type="checkbox" id="' . $id . '" value="' . $value . ' "'. checked( $value, 1, false) .'> ' . $text .'</label></p>';
		$cap_label .= $attrib_label;
		$cap_label .= $url_label;
		$cap_label .= $label;
    return $content .= $cap_label;
}
add_filter( 'admin_post_thumbnail_html', 'prefix_featured_image_meta' );

/**
 * Save featured image meta data when saved
 *
 * @param int $post_id The ID of the post.
 * @param post $post the post.
 */
function prefix_save_featured_image_meta( $post_id, $post, $update ) {

    $value = 0;
    if ( isset( $_REQUEST['open_featured_new_window'] ) ) {
        $value = 1;
    }


    // Set meta value to either 1 or 0
    update_post_meta( $post_id, 'open_featured_new_window', $value );

}
add_action( 'save_post', 'prefix_save_featured_image_meta', 10, 3 );




/* filter the post thumbnail html hook with image caption. */
add_filter( 'post_thumbnail_html', 'featured_image_caption', 200, 5 );

function featured_image_caption( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

  /* Get the current post ID. */
  //$post_id = get_the_ID();

  /* If we have a post ID, proceed. */
  if ( !empty( $post_id ) ) {

    /* Get the custom post class. */
    $featured_image_caption = get_post_meta( $post_id, 'featured_image_caption', true );
		$featured_image_attrib = get_post_meta( $post_id, 'featured_image_attribution', true );
		$featured_image_url = get_post_meta( $post_id, 'featured_image_attribution_url', true );
		$new_window = get_post_meta($post_id, 'open_featured_new_window', true);

		if (strpos($featured_image_url, 'http://') == false){
			$featured_image_url_correct = 'http://' . $featured_image_url . '';
		}
		else{
			$featured_image_url_correct = $featured_image_url;
		}

		if ($new_window == 0){
			$featured_image_full = '<div class="featured_image_cap" style="position:relative; z-index:30; background-color: #fff;
			font-size: 11px; font-style: italic; text-align: center; padding-top: 5px;
			 color: #999 !important;">'. $featured_image_caption . ' <br/>photo by
			<a href="' . $featured_image_url_correct . '">' . $featured_image_attrib . '</a></div>';
		}
		if ($new_window == 1){
			$featured_image_full = '<div class="featured_image_cap" style="position:relative; z-index:30; background-color: #fff;
			font-size: 11px; font-style: italic; text-align: center; padding-top: 5px; color: #999 !important;">'. $featured_image_caption . ' <br/>photo by
			<a target="_blank" href="' . $featured_image_url_correct . '">' . $featured_image_attrib . '</a></div>';
		}



    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( !empty( $featured_image_caption ) )
		{
			$html_full = $html . ' ' . $featured_image_full;
			?>
			<script>
			jQuery(document).ready(function( $ ) {
				$('.post-thumb-container').css("cssText", "max-height: none !important;");

			});
			</script>
			<?php

		}
		else{
			$html_full = $html;
		}

  	}

  return $html_full;
}
