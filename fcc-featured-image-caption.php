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
# DEBUG HELPER FUNCTIONS
--------------------------------------------------------------*/

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'imagecap_meta_boxes_setup' );
add_action( 'load-post-new.php', 'imagecap_meta_boxes_setup' );

/* Meta box setup function. */
function imagecap_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'add_imagecap_meta_boxes' );

  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'save_image_cap_meta', 10, 2 );
}

function add_imagecap_meta_boxes() {

  add_meta_box(
    'image-cap',      // Unique ID
    esc_html__( 'Featured Image Caption', 'example' ),    // Title
    'image_cap_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}

/* Display the post meta box. */
function image_cap_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'image_cap_nonce' ); ?>

  <p>
    <label for="image-cap"><?php _e( "Caption text", 'example' ); ?></label>
    <br />
    <textarea style="box-sizing:border-box;width:100%;min-height:40px;padding-left:5px;padding-right:5px;" type="textarea" name="image-cap" id="image-cap" ><?php echo esc_attr( get_post_meta( $object->ID, 'image_cap', true ) ); ?> </textarea>
  </p>
<?php }


/* Save the meta box's post metadata. */
function save_image_cap_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
  	if ( ! isset( $_POST['image_cap_nonce'] ) || ! wp_verify_nonce( $_POST['image_cap_nonce'], basename( __FILE__ ) ) ) {
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
  $meta_key = 'image_cap';

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
}



function prefix_featured_image_meta( $content ) {
	wp_nonce_field( basename( __FILE__ ), 'image_cap_nonce' );

	global $post;
	$cap_text = __( 'Caption Text', 'prefix' );
	$cap_id = 'image_cap';
	$cap_value = esc_attr( get_post_meta( $post->ID, $cap_id, true ) );
	$cap_label = '<p><label for="' . $cap_id . '">' . $cap_text . '</label><br/><textarea
	style="box-sizing:border-box;width:100%;min-height:40px;padding-left:5px;padding-right:5px;" name="' . $cap_id . '"
	id="' . $cap_id . '">' . $cap_value . '</textarea></p>'
	?>

	<?php

    $text = __( 'Open in new window', 'prefix' );
    $id = 'open_featured_new_window';
    $value = esc_attr( get_post_meta( $post->ID, $id, true ) );
    $label = '<p><label for="' . $id . '" class="selectit"><input name="' . $id . '"
		type="checkbox" id="' . $id . '" value="' . $value . ' "'. checked( $value, 1, false) .'> ' . $text .'</label></p>';
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
add_filter( 'post_thumbnail_html', 'featured_image_caption' );

function featured_image_caption( $html ) {

  /* Get the current post ID. */
  $post_id = get_the_ID();

  /* If we have a post ID, proceed. */
  if ( !empty( $post_id ) ) {

    /* Get the custom post class. */
    $image_cap = get_post_meta( $post_id, 'image_cap', true );
		$hide_featured = get_post_meta($post_id, 'open_featured_new_window', true);

    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( !empty( $image_cap ) )
      $html .= $image_cap;
			$html .= $hide_featured;
  	}

  return $html;
}
