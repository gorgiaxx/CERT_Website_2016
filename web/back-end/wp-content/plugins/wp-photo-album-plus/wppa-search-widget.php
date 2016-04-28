<?php
/* wppa-searchwidget.php
* Package: wp-photo-album-plus
*
* display the search widget
* Version 6.4.03
*
*/

class SearchPhotos extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_search_photos', 'description' => __( 'WPPA+ Search Photos', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_search_photos', __('Search Photos', 'wp-photo-album-plus'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $widget_content;
		global $wpdb;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		wppa( 'mocc', wppa( 'mocc' ) + 1 );
		wppa( 'in_widget', 'search' );

        extract( $args );

		$instance = wp_parse_args( (array) 	$instance,
									array( 	'title' 		=> __('Search Photos', 'wp-photo-album-plus'),
											'label' 		=> '',
											'root' 			=> false,
											'sub' 			=> false,
											'album' 		=> '',
											'landingpage' 	=> '0',
											) );

 		$widget_title = apply_filters('widget_title', $instance['title']);

		// Display the widget
		echo $before_widget;

		if ( ! empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }

		echo wppa_get_search_html( $instance['label'], $instance['sub'], $instance['root'], $instance['album'], $instance['landingpage'] );

		echo $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['label']			= $new_instance['label'];
		$instance['root']  			= $new_instance['root'];
		$instance['sub']   			= $new_instance['sub'];
		$instance['album'] 			= $new_instance['album'];
		$instance['landingpage']	= $new_instance['landingpage'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form( $instance ) {
		global $wpdb;

		//Defaults
		$instance 		= wp_parse_args( 	(array) $instance,
											array(
												'title' 		=> __( 'Search Photos', 'wp-photo-album-plus' ),
												'label' 		=> '',
												'root' 			=> false,
												'sub' 			=> false,
												'album' 		=> '',
												'landingpage' 	=> '',
												) );
		$title 			= $instance['title'];
		$label 			= $instance['label'];
		$root  			= $instance['root'];
		$sub   			= $instance['sub'];
		$album 			= $instance['album'];
		$landingpage  	= $instance['landingpage'];
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e('Title:', 'wp-photo-album-plus'); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('label'); ?>">
				<?php _e('Text:', 'wp-photo-album-plus');  ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo esc_attr($label) ?>" />
		</p>
		<small><?php _e('Enter optional text that will appear before the input box. This may contain HTML so you can change font size and color.', 'wp-photo-album-plus'); ?></small>
		<p>
			<input type="checkbox" <?php if ( $root ) echo 'checked="checked"' ?> id="<?php echo $this->get_field_id('root'); ?>" name="<?php echo $this->get_field_name('root'); ?>" />
			<label for="<?php echo $this->get_field_id('root'); ?>">
				<?php _e('Enable rootsearch', 'wp-photo-album-plus'); ?>
			</label>
		</p>
		<p>
			<small>
				<?php _e('If you want the search to be limited to a specific album and its (grand)children, select the album here.', 'wp-photo-album-plus'); ?>
				<br />
				<?php _e('If you select an album here, it will overrule the previous checkbox using the album as a \'fixed\' root.', 'wp-photo-album-plus'); ?>
			</small>
			<select id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" style="max-width:100%" >
				<?php echo wppa_album_select_a( array( 	'selected' 			=> $album,
														'addblank' 			=> true,
														'sort'				=> true,
														'path' 				=> true,
														 ) )
				?>
			</select>
		</p>
		<p>
			<input type="checkbox" <?php if ( $sub ) echo 'checked="checked"' ?> id="<?php echo $this->get_field_id('sub'); ?>" name="<?php echo $this->get_field_name('sub'); ?>" />
			<label for="<?php echo $this->get_field_id('sub'); ?>">
				<?php _e('Enable subsearch', 'wp-photo-album-plus'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('landingpage'); ?>" >
				<?php _e( 'Landing page', 'wp-photo-album-plus' ); echo ': '.$landingpage ?>
			</label>
			<select id="<?php echo $this->get_field_id('landingpage'); ?>" name="<?php echo $this->get_field_name('landingpage'); ?>" style="max-width:100%" >
				<?php

				// First option
				$selected = $landingpage == '0' ? ' selected="selected"' : '';
				?>
				<option value="0" <?php echo $selected ?> >
					<?php _e( '--- Default ---', 'wp-photo-album-plus' ) ?>
				</option>
				<?php

				// Pages if any
				$query = 	"SELECT ID, post_title, post_content, post_parent " .
							"FROM " . $wpdb->posts . " " .
							"WHERE post_type = 'page' AND post_status = 'publish' " .
							"ORDER BY post_title ASC";
				$pages = 	$wpdb->get_results( $query, ARRAY_A );

				if ( $pages ) {

					// Add parents optionally OR translate only
					if ( wppa_switch( 'wppa_hier_pagesel' ) ) $pages = wppa_add_parents( $pages );

					// Just translate
					else {
						foreach ( array_keys( $pages ) as $index ) {
							$pages[$index]['post_title'] = __( stripslashes($pages[$index]['post_title']  ) );
						}
					}

					// Sort alpahbetically
					$pages = wppa_array_sort( $pages, 'post_title' );

					// Display option
					foreach ( $pages as $page ) {
						$selected = $page['ID'] == $landingpage ? ' selected="selected"' : '';
						$d = strpos( $page['post_content'], '[wppa' ) === false && strpos( $page['post_content'], '%%wppa%%' ) === false;
						$disabled = $d ? ' disabled="disabled"' : '';
						?>
						<option value="<?php echo $page['ID'] ?>"<?php echo $selected ?><?php echo $disabled ?> >
							<?php _e( $page['post_title'] ) ?>
						</option>
						<?php
					}
				}
				?>
			</select>
		</p>
		<p>
			<small>
				<?php _e( 'The default page will be created automaticly', 'wp-photo-album-plus' ) ?>
			</small>
		</p>
<?php
    }

} // class SearchPhotos

// register SearchPhotos widget
add_action('widgets_init', 'wppa_register_SearchPhotos' );

function wppa_register_SearchPhotos() {
	register_widget("SearchPhotos");
}
