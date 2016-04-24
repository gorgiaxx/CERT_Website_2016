<?php
/*
Plugin Name: Markdown on Save Improved
Description: Deprecated. Please use the Markdown module in the Jetpack plugin!
Version: 2.5
Author: Matt Wiebe
Author URI: http://somadesign.ca/
License: GPL v2
*/

/*
 * Copyright 2011-14 Matt Wiebe. GPL v2, of course.
 *
 * This software is forked from the original Markdown on Save plugin (c) Mark Jaquith
 * It uses the Markdown Extra and Markdownify libraries. Copyrights and licenses indicated in said libararies.
 *
 */

class SD_Markdown {

	const PM = '_sd_disable_markdown';
	const MD = '_sd_is_markdown';
	const VERSION = '2.5';
	const VERSION_OPT = 'mosi-version';
	const NAG_OPTION = 'mosi-nag';

	protected $new_api_post = false;

	protected $parser = false;

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		register_activation_hook( __FILE__, array($this, 'activate') );
	}

	public function init() {
		load_plugin_textdomain( 'markdown-osi', NULL, basename( dirname( __FILE__ ) ) );

		$this->add_post_type_support();

		add_action( 'do_meta_boxes', array( $this, 'do_meta_boxes' ), 20, 2 );
		add_action( 'xmlrpc_call', array($this, 'xmlrpc_actions') );
		add_action( 'load-post.php', array( $this, 'load' ) );
		add_action( 'load-post-new.php', array( $this, 'load' ) );
		add_action( 'xmlrpc_call_success_mw_newPost', array( $this, 'xmlrpc_new_post' ), 10, 2 );

		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 10, 2 );
		add_filter( 'edit_post_content', array( $this, 'edit_post_content' ), 10, 2 );
		add_filter( 'edit_post_content_filtered', array( $this, 'edit_post_content_filtered' ), 10, 2 );

		add_action( 'admin_menu', array( $this, 'jetpack_convert_page_register' ) );
		add_action( 'admin_notices', array( $this, 'jetpack_nag_notice' ) );

		// Markdown breaks autoembedding by wrapping URLs on their own line in paragraphs
		if ( get_option( 'embed_autourls' ) )
			add_filter( 'the_content', array($this, 'oembed_fixer' ), 8 );

		if ( defined( 'XMLRPC_REQUEST') && XMLRPC_REQUEST )
			$this->maybe_prime_post_data();
	}

	public function jetpack_convert_page_register() {
		add_management_page( 'Markdown on Save Improved Convert', 'MoSI Convert', 'manage_options', 'mosi-convert', array( $this, 'jetpack_convert_page') );
	}

	public function jetpack_convert_page() {
		$did_conversion = false;
		$do_conversion = isset( $_POST['mosi-convert'] ) && wp_verify_nonce( $_POST['mosi-convert'], 'mosi-convert' );
		if ( $do_conversion ) {
			$did_conversion = $this->convert_posts_to_jetpack();
		}
		?>
		<h2><?php _e( 'Markdown on Save Improved Convert to Jetpack', 'markdown-osi' ); ?></h2>
		<?php if ( $do_conversion && $did_conversion ) {
			// turn off nag
			update_option( self::NAG_OPTION, true );
			printf( '<p>%s</p>', esc_html__( 'Congratulations! You have successfully updated your posts to work with Jetpack&rsquo;s Markdown module. Be sure to disable Markdown on Save Improved and enable the Jetpack Markdown module.', 'markdown-osi' ) );
			printf( '<p><a target="_blank" href="%s">%s</a></p>', 'http://jetpack.me/support/markdown/', 'How to enable the Jetpack Markdown module.' );
		} else { ?>
			<p><?php _e( 'Click the shiny button below to convert your posts to work with Jetpack&rsquo;s Markdown module.', 'markdown-osi' ) ?></p>
			<form action="<?php echo esc_url( admin_url( 'tools.php?page=mosi-convert' ) );  ?>" method="post">
				<?php wp_nonce_field( 'mosi-convert', 'mosi-convert' ); ?>
				<?php submit_button( __( 'Convert!', 'markdown-osi' ) ); ?>
			</form>
		<?php } ?>

		<?php
	}

	protected function convert_posts_to_jetpack() {
		// we need a bit of custom SQL here since core stuff will only be insufficient and annoying.
		global $wpdb;
		$new_meta_key = '_wpcom_is_markdown';
		$query = $wpdb->prepare( "SELECT ID, post_content FROM $wpdb->posts LEFT JOIN $wpdb->postmeta AS meta ON ID = meta.post_id WHERE meta.meta_key = %s", self::MD );
		$results = $wpdb->get_results( $query );
		foreach ( $results as $result ) {
			// unset old meta
			delete_metadata( 'post', $result->ID, self::MD );
			// set new meta
			add_metadata( 'post', $result->ID, $new_meta_key, true );
			// Jetpack Markdown stores content without the <p>s
			$result->post_content = $this->unp( $result->post_content );
			// Update the <p>-less post
			$wpdb->update( $wpdb->posts, array( 'post_content' => $result->post_content ), array( 'ID' => $result->ID ) );
		}
		return true;
	}

	public function unp( $text ) {
		return preg_replace( "#<p>(.*?)</p>(\n|$)#ums", '$1$2', $text );
	}

	public function jetpack_nag_notice() {
		global $plugin_page;
		if ( get_option( self::NAG_OPTION ) ) {
			return;
		}
		if ( 'mosi-convert' === $plugin_page  ) {
			if ( isset( $_GET['action' ] ) && 'dismiss-nag' === $_GET['action'] ) {
				echo '<div class="updated"><p>';
				_e( 'You will no longer be nagged about changing to Jetpack, but you can always come back to this page to convert.', 'markdown-osi' );
				echo '</p></div>';
				update_option( self::NAG_OPTION, true );
			}
			return;
		}
		echo '<div class="update-nag"><p>';
		_e( 'Markdown on Save Improved is no longer being updated, as I have now put all of my Markdown efforts into Jetpack&rsquo;s Markdown module. I highly recommend you use that instead, as it will be better supported and continue to receive updates. Your options are:' , 'markdown-osi' );
		echo '</p><ol>';
		printf( '<li><a href="%s" id="mosi-convert">%s</a></li>',
			admin_url( 'tools.php?page=mosi-convert' ),
			esc_html__( 'Update your posts to work with Jetpack', 'markdown-osi' )
		);
		printf( '<li><a href="%s" id="mosi-dismiss">%s</a></li>',
			admin_url( 'tools.php?page=mosi-convert&amp;action=dismiss-nag' ),
			esc_html__( 'Dismiss this notice (keep using Markdown on Save Improved)', 'markdown-osi' )
		);
		echo '</ol></div>';
	}

	protected function add_post_type_support() {
		add_post_type_support( 'post', 'markdown-osi' );
		add_post_type_support( 'page', 'markdown-osi' );
	}

	public function xmlrpc_new_post( $post_id, $args ) {
		$this->new_api_post = true;
		remove_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 10, 2 );
		$post = (array) get_post( $post_id );
		$post = $this->wp_insert_post_data( $post, $post );

		wp_update_post( $post );
	}

	public function xmlrpc_actions( $xmlrpc_method ) {
		$make_filterable = array( 'metaWeblog.getRecentPosts', 'wp.getPosts', 'wp.getPages' );

		if ( in_array( $xmlrpc_method, $make_filterable ) )
			add_action( 'parse_query', array($this, 'make_filterable'), 10, 1 );
	}

	// we have to do it early and ghetto like this since metaWeblog.getPost && wp.getPage
	// fire *after* get_post is called in their methods
	public function maybe_prime_post_data() {
		global $HTTP_RAW_POST_DATA;
		require_once( ABSPATH . WPINC . '/class-IXR.php' );
		$message = new IXR_Message( $HTTP_RAW_POST_DATA );
		if ( ! $message->parse() ) {
			unset( $message );
			return;
		}

		$methods_to_prime = array( 'metaWeblog.getPost', 'wp.getPost', 'wp.getPage' );
		if ( ! in_array( $message->methodName, $methods_to_prime ) ) {
			unset( $message );
			return;
		}

		// different ID arg for wp.getPage
		$post_id = ( 'wp.getPage' === $message->methodName ) ? $message->params[1] : array_shift( $message->params );
		$post_id = (int) $post_id;
		// prime the post cache
		if ( $this->is_markdown( $post_id ) ) {
			$post = get_post( $post_id );
			if ( ! empty( $post->post_content_filtered ) )
				$post->post_content = $post->post_content_filtered;
			wp_cache_delete( $post->ID, 'posts' );
			wp_cache_add( $post->ID, $post, 'posts' );
		}
		unset( $message );
	}

	public function make_filterable( $wp_query ) {
		$wp_query->set( 'suppress_filters', false );
		add_action( 'the_posts', array( $this, 'the_posts' ), 10, 2 );
	}

	public function the_posts($posts, $wp_query) {
		foreach ( $posts as $key => $post ) {
			if ( $this->is_markdown($post->ID) )
				$posts[ $key ]->post_content = $posts[ $key ]->post_content_filtered;
		}
		return $posts;
	}

	public function load() {
		if ( ! ( isset( $_GET['post'] ) && ! $this->is_markdown( $_GET['post'] ) ) )
			add_filter( 'user_can_richedit', '__return_false', 99 );
	}

	public function wp_insert_post_data( $data, $postarr ) {
		// run once
		remove_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 10, 2 );

		// checks
		$nonced = ( isset( $_POST['_sd_markdown_nonce'] ) && wp_verify_nonce( $_POST['_sd_markdown_nonce'], 'sd-markdown-save' ) );
		$disable_ticked = ( $nonced && isset( $_POST['sd_disable_markdown'] ) );
		$disable_comment_inserted = ( false !== stripos( $data['post_content'], '<!--no-markdown-->' ) );
		$id = ( isset( $postarr['ID'] ) ) ? $postarr['ID'] : 0;
		$post_type_to_check = isset( $postarr['post_type'] ) ? $postarr['post_type'] : '';
		// we need to check the parent of a revision to determine support
		if ( 'revision' === $post_type_to_check ) {
			$parent = get_post( $data['post_parent'] );
			$post_type_to_check = $parent->post_type;
		}
		$supports = post_type_supports( $post_type_to_check, 'markdown-osi' );

		// double check in case this is a new xml-rpc post. Disable couldn't be checked.
		if ( $this->new_api_post ) {
			$disable_ticked = false;
		}

		// Make sure markdown processing isn't disabled for this post
		if ( $supports && ! ( $disable_ticked || $disable_comment_inserted ) ) {
			$data['post_content_filtered'] = $data['post_content'];
			// Do markdown processing
			$data['post_content'] = $this->process( $data['post_content'], $id );

			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$data['post_content'] = wp_kses_post( $data['post_content'] );
			}
			if ( $id ) {
				update_post_meta( $id, self::MD, 1 );
			}
		} else {
			$data['post_content_filtered'] = '';
			if ( $id ) {
				update_post_meta( $id, self::MD, false );
			}
		}

		return $data;
	}

	/**
	 * Fixes oEmbed auto-embedding of single-line URLs
	 *
	 * WP's normal autoembed assumes that there's no <p>'s yet because it runs before wpautop
	 * But, when running Markdown, we have <p>'s already there, including around our single-line URLs
	 */
	public function oembed_fixer( $content ) {
		global $wp_embed;
		return preg_replace_callback( '|^\s*<p>(https?://[^\s"]+)</p>\s*$|im', array( $wp_embed, 'autoembed_callback' ), $content );
	}

	protected function process( $content, $id ) {
		$this->maybe_load_markdown();
		// $content is slashed, but Markdown parser hates it precious.
		$content = stripslashes( $content );
		// convert to Markdown
		$content = $this->parser->transform( $content );
		// reference the post_id to make footnote ids unique
		$content = preg_replace( '/fn(ref)?:/', "fn$1-$id:", $content );
		// WordPress expects slashed data. Put needed ones back.
		$content = addslashes( $content );
		return $content;
	}

	protected function maybe_load_markdown() {
		// In case another plugin has included it - hopefully it's compatible
		if ( ! class_exists( 'MarkdownExtra_Parser' ) )
			require_once( dirname( __FILE__ ) . '/markdown-extra/markdown-extra.php' );
		if ( ! $this->parser )
			$this->parser = new MarkdownExtra_Parser;
	}

	public function do_meta_boxes( $type, $context ) {
		// allow disabling for folks who think markdown should always be on.
		if ( defined( 'SD_HIDE_MARKDOWN_BOX') && SD_HIDE_MARKDOWN_BOX )
			return;

		if ( 'side' == $context && in_array( $type, array_keys( get_post_types() ) ) && post_type_supports( $type, 'markdown-osi' ) )
			add_meta_box( 'sd-markdown', __( 'Markdown', 'markdown-osi' ), array( $this, 'meta_box' ), $type, 'side', 'high' );
	}

	public function meta_box() {
		global $post;
		$screen = get_current_screen();
		wp_nonce_field( 'sd-markdown-save', '_sd_markdown_nonce', false, true );
		echo '<p><input type="checkbox" name="sd_disable_markdown" id="sd_disable_markdown" value="1" ';
		// we get false positives on new post screens. Do not want.
		if ( 'add' !== $screen->action )
			checked( ! get_post_meta( $post->ID, self::MD, true ) );
		echo ' /> <label for="sd_disable_markdown">' . __( 'Disable Markdown formatting', 'markdown-osi' ) . '</label></p>';
	}

	private function is_markdown( $id ) {
		return (bool) get_post_meta( $id, self::MD, true );
	}

	public function edit_post_content( $content, $id ) {
		if ( $this->is_markdown( $id ) ) {
			$post = get_post( $id );
			if ( $post && ! empty( $post->post_content_filtered ) )
				$content = $post->post_content_filtered;
		}
		return $content;
	}

	public function edit_post_content_filtered( $content, $id ) {
		return $content;
	}

	public function activate() {
		$previous_version = get_option( self::VERSION_OPT, '2.1' );
		// upgrade to new determining of MD
		if ( version_compare( '2.1', $previous_version, '=' ) ) {
			$this->update_schema();
		}
		update_option( self::VERSION_OPT, self::VERSION );
	}

	/**
	* Previously, we only set a meta value for disabling. Now we'll set one regardless. Old options need updating.
	*/
	private function update_schema() {
		global $wpdb;
		// formerly MD-disabled posts get updated to new meta key with false value
		$query = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s, meta_value = '' WHERE meta_key = %s ", self::MD, self::PM );
		$wpdb->query( $query );

		// posts with stuff in post_content_filtered get the new meta key with true value
		$ids = $wpdb->get_col( "SELECT ID from {$wpdb->posts} WHERE post_content_filtered != '' " );
		if ( $ids && ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				// reprocess markdown -> we used to strip <p> tags and rely on wpautop
				$post = get_post( $id );
				$post->post_content = $this->process( $post->post_content_filtered, $id );
				wp_update_post( $post );
				update_post_meta( $id, self::MD, 1 );
			}
		}
	}
}

new SD_Markdown;