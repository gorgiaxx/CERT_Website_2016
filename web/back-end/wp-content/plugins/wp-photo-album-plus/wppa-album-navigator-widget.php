<?php
/* wppa-album-navigator-widget.php
* Package: wp-photo-album-plus
*
* display album names linking to content
* Version 6.4.00
*/

class AlbumNavigatorWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_album_navigator_widget', 'description' => __( 'WPPA+ Album navigator', 'wp-photo-album-plus') );
		parent::__construct('wppa_album_navigator_widget', __('Album navigator', 'wp-photo-album-plus'), $widget_ops);
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $wpdb;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		wppa( 'in_widget', 'albnav' );
		wppa_bump_mocc();

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array(
													'title' => '',		// Widget title
													'parent' => '0',	// Parent album
													'skip' => 'yes'		// Skip empty albums
													) );

		$widget_title = apply_filters('widget_title', $instance['title']);

		$page 	= wppa_get_the_landing_page('wppa_album_navigator_widget_linkpage', __('Photo Albums', 'wp-photo-album-plus'));
		$parent = $instance['parent'];
		$skip 	= $instance['skip'];


		$widget_content = "\n".'<!-- WPPA+ Album Navigator Widget start -->';
		$widget_content .= '<div style="width:100%; overflow:hidden; position:relative; left: -12px;" >';
		if ( $parent == 'all' ) {
			$widget_content .= $this->do_album_navigator( '0', $page, $skip, '' );
			$widget_content .= $this->do_album_navigator( '-1', $page, $skip, '' );
		}
		elseif ( $parent == 'owner' ) {
			$widget_content .= $this->do_album_navigator( '0', $page, $skip, '', " AND ( `owner` = '--- public ---' OR `owner` = '".wppa_get_user()."' ) " );
			$widget_content .= $this->do_album_navigator( '-1', $page, $skip, '', " AND ( `owner` = '--- public ---' OR `owner` = '".wppa_get_user()."' ) " );
		}
		else {
			$widget_content .= $this->do_album_navigator( $parent, $page, $skip, '' );
		}
		$widget_content .= '</div>';
		$widget_content .= '<div style="clear:both"></div>';

		$widget_content .= "\n".'<!-- WPPA+ Album Navigator Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		echo $widget_content . $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['parent'] = $new_instance['parent'];
		$instance['skip'] = $new_instance['skip'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		global $wpdb;

		//Defaults

		$instance = wp_parse_args( (array) $instance, array(
															'title' 	=> __('Photo Albums', 'wp-photo-album-plus'),
															'parent' 	=> '0',
															'skip' 		=> 'yes' ) );
 		$parent 		= $instance['parent'];
		$skip 			= $instance['skip'];
		$widget_title 	= $instance['title'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('parent'); ?>"><?php _e('Album selection or Parent album:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('parent'); ?>" name="<?php echo $this->get_field_name('parent'); ?>" >

				<option value="all" <?php if ($parent == 'all') echo 'selected="selected"' ?>><?php _e('--- all albums ---', 'wp-photo-album-plus') ?></option>
				<option value="0"  <?php if ($parent == '0')  echo 'selected="selected"' ?>><?php _e('--- all generic albums ---', 'wp-photo-album-plus') ?></option>
				<option value="-1" <?php if ($parent == '-1') echo 'selected="selected"' ?>><?php _e('--- all separate albums ---', 'wp-photo-album-plus') ?></option>
				<option value="owner" <?php if ($parent == 'owner') echo 'selected="selected"' ?>><?php _e('--- owner/public ---', 'wp-photo-album-plus') ?></option>
				<?php $albs = $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."` ORDER BY `name`", ARRAY_A);
				if ( $albs ) foreach( $albs as $alb ) {
					echo '<option value="'.$alb['id'].'" ';
					if ( $parent == $alb['id'] ) echo 'selected="selected" ';
					if ( ! wppa_has_children($alb['id']) ) echo 'disabled="disabled" ';
					echo '>'.__(stripslashes($alb['name']), 'wp-photo-album-plus').'</option>';
				} ?>

			</select>
		</p>
		<p>
			<?php _e('Skip "empty" albums:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('skip'); ?>" name="<?php echo $this->get_field_name('skip'); ?>">
				<option value="no" <?php if ($skip == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($skip == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>

<?php
	}

	function get_widget_id() {
		$widgetid = substr( $this->get_field_name( 'txt' ), strpos( $this->get_field_name( 'txt' ), '[' ) + 1 );
		$widgetid = substr( $widgetid, 0, strpos( $widgetid, ']' ) );
		return $widgetid;
	}

	function do_album_navigator( $parent, $page, $skip, $propclass, $extraclause = '' ) {
	global $wpdb;
	static $level;
	static $ca;

		if ( ! $level ) {
			$level = '1';
			if ( isset( $_REQUEST['wppa-album'] ) ) $ca = $_REQUEST['wppa-album'];
			elseif ( isset( $_REQUEST['album'] ) ) $ca = $_REQUEST['album'];
			else $ca = '0';
			$ca = wppa_force_numeric_else( $ca, '0' );
			if ( $ca && ! wppa_album_exists( $ca ) ) {
//				wppa_log('dbg', 'Non-existent album '.$ca.' in url. Referrer= '.$_ENV["HTTP_REFERER"].', Request uri= '.$_ENV["REQUEST_URI"]);
				$ca = '0';
			}
		}
		else {
			$level++;
		}

		$slide = wppa_opt( 'album_navigator_widget_linktype' ) == 'slide' ? '&amp;wppa-slide=1' : '';

		$w = $this->get_widget_id();
		$p = $parent;
		$result = '';
		$albums = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s ".$extraclause.wppa_get_album_order( max( '0', $parent ) ), $parent ), ARRAY_A );
		wppa_dbg_q( 'Q-WidANav' );
		if ( ! empty( $albums ) ) {
			wppa_cache_album( 'add', $albums );
			$result .= '<ul>';
			foreach ( $albums as $album ) {
				$a = $album['id'];
				$treecount = wppa_treecount_a( $a );
				if ( $treecount['albums'] || $treecount['photos'] > wppa_opt( 'min_thumbs' ) || $skip == 'no' ) {
					$result .= '
						<li class="anw-'.$w.'-'.$p.$propclass.'" style="list-style:none; display:'.( $level == '1' ? '' : 'none' ).';">';
						if ( wppa_has_children($a) ) $result .= '
							<div style="cursor:default;width:12px;float:left;text-align:center;font-weight:bold;" class="anw-'.$w.'-'.$a.'-" onclick="jQuery(\'.anw-'.$w.'-'.$a.'\').css(\'display\',\'\'); jQuery(\'.anw-'.$w.'-'.$a.'-\').css(\'display\',\'none\');" >'.( $a == $ca ? '&raquo;' : '+').'</div>
							<div style="cursor:default;width:12px;float:left;text-align:center;font-weight:bold;display:none;" class="anw-'.$w.'-'.$a.'" onclick="jQuery(\'.anw-'.$w.'-'.$a.'-\').css(\'display\',\'\'); jQuery(\'.anw-'.$w.'-'.$a.'\').css(\'display\',\'none\'); jQuery(\'.p-'.$w.'-'.$a.'\').css(\'display\',\'none\');" >'.( $a == $ca ? '&raquo;' : '-').'</div>';
						else $result .= '
							<div style="width:12px;float:left;" >&nbsp;'.( $a == $ca ? '&raquo;' : '').'</div>';
						$result .= '
							<a href="'.wppa_encrypt_url(wppa_get_permalink( $page ).'&amp;wppa-album='.$a.'&amp;wppa-cover=0&amp;wppa-occur=1'.$slide).'">'.wppa_get_album_name( $a ).'</a>
						</li>';
					$newpropclass = $propclass . ' p-'.$w.'-'.$p;
					$result .= '<li class="anw-'.$w.'-'.$p.$propclass.'" style="list-style:none;" >' . $this->do_album_navigator( $a, $page, $skip, $newpropclass, $extraclause ) . '</li>';
				}
			}
			$result .= '</ul>';
			if ( $level == '1' && $ca ) { // && $parent != '-1' ) {
				$result .= '<script type="text/javascript" >';
					while ( $ca != '0' && $ca != '-1' ) {
						$result .= '
								jQuery(\'.anw-'.$w.'-'.$ca.'\').css(\'display\',\'\'); jQuery(\'.anw-'.$w.'-'.$ca.'-\').css(\'display\',\'none\');';
						$ca = wppa_get_parentalbumid($ca);
					}
				$result .= '</script>';
			}
		}
		$level--;
		return str_replace( '<ul></ul>', '', $result );
	}

} // class AlbumNavigatorWidget
// register AlbumNavigatorWidget widget
add_action('widgets_init', 'wppa_register_AlbumNavigatorWidget' );

function wppa_register_AlbumNavigatorWidget() {
	register_widget("AlbumNavigatorWidget");
}
