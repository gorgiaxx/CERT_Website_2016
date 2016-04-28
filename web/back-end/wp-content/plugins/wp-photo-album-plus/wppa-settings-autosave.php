<?php
/* wppa-settings-autosave.php
* Package: wp-photo-album-plus
*
* manage all options
* Version 6.4.09
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

function _wppa_page_options() {
global $wpdb;
global $wppa;
global $wppa_opt;
global $blog_id;
global $wppa_status;
global $options_error;
global $wppa_api_version;
global $wp_roles;
global $wppa_table;
global $wppa_subtable;
global $wppa_revno;
global $no_default;
global $wppa_tags;


	// Test area
//	echo '<!-- Start Test area ---><br />';

// To demonstrate wpautop() destructifies inline script:

// Large version:
//$str = '<script type="text/javascript">/* [CDATA[ */
//		wppaStoreSlideInfo( \'12\',\'0\',\'/wp-content/uploads/wppa/133.jpg?ver=233\',\' max-width:1020px; max-height:576px;margin:0 auto; border: 1px solid #777777; background-color:#cccccc; padding:7px; border-radius:7px;\',\'510\',\'288\',\'coverphoto (admin)\',\'coverphoto\',\' 					<div style="float:right; margin-right:6px;" ><a style="color:green;" onclick="_wppaStop( 12 );wppaEditPhoto( 12, 133 ); return false;" >Edit</a></div> 					<div style="float:right; margin-right:6px;" ><a style="color:red;" onclick="_wppaStop( 12 );if ( confirm( &quot;Are you sure you want to remove this photo?&quot; ) ) wppaAjaxRemovePhoto( 12, 133, true ); return false;">Delete</a></div><div style="clear:both"></div><p>Views: 6<br />\nThumbnail: <a href="/wp-content/uploads/wppa/thumbs/133.jpg" rel="nofollow">/wp-content/uploads/wppa/thumbs/133.jpg</a><br />\nScreenres: <a href="/wp-content/uploads/wppa/133.jpg" rel="nofollow">/wp-content/uploads/wppa/133.jpg</a><br />\nHires: <a href="/wp-content/uploads/wppa-source/album-75/coverphoto.jpg" rel="nofollow">/wp-content/uploads/wppa-source/album-75/coverphoto.jpg</a><br />\nPermalink: <a href="/wp-content/albums/Sub-van-tesje/coverphoto.jpg" rel="nofollow">/wp-content/albums/Sub-van-tesje/coverphoto.jpg</a><br />\nAugust 13, 2015 3:21 pm</p>\n\',\'133\',\'0|0\',\'0\',\'0\',\'/generic-2/?lang=en&wppa-album=75&wppa-cover=0&wppa-slide&wppa-occur=1&wppa-photo=63\',\'\',\'Zoom in\',\'\',\'0\',\'<div id="wppa-comform-wrap-12" style="display:none;" ><form id="wppa-commentform-12" class="wppa-comment-form" action="/generic-2/?lang=en&amp;wppa-album=75&wppa-occur=1&wppa-photo=133" method="post" onsubmit="return wppaValidateComment( 12 )" ><input type="hidden" id="wppa-nonce-12" name="wppa-nonce-12" value="be4355e748" /><input type="hidden" name="wppa-album" value="75" /><input type="hidden" name="wppa-returnurl" id="wppa-returnurl-12" value="/generic-2/?lang=en&amp;wppa-album=75&wppa-occur=1&wppa-photo=133" /><input type="hidden" name="wppa-occur" value="1" /><table id="wppacommenttable-12" style="margin:0;"><tbody><tr valign="top" style="display:none; "><td class="wppa-box-text wppa-td" style="width:30%; " >Your name:</td><td class="wppa-box-text wppa-td" style="width:70%; " ><input type="text" name="wppa-comname" id="wppa-comname-12" style="width:100%; " value="admin" /></td></tr><tr valign="top" style="display:none; "><td class="wppa-box-text wppa-td" style="width:30%; " >Your email:</td><td class="wppa-box-text wppa-td" style="width:70%; " ><input type="text" name="wppa-comemail" id="wppa-comemail-12" style="width:100%;" value="opajaap@opajaap.nl" /></td></tr><tr valign="top" style="vertical-align:top;"><td valign="top" class="wppa-box-text wppa-td" style="vertical-align:top; width:30%; " >Your comment:<br />admin<br />10 + 11 = <input type="text" id="wppa-captcha-12" name="wppa-captcha" style="width:20%;" />&nbsp;<input type="button" name="commentbtn" onclick="wppaAjaxComment( 12, 133 )" value="Send!" style="margin:0 4px 0 0;" /><img id="wppa-comment-spin-12" src="/wp-content/plugins/wp-photo-album-plus/images/wpspin.gif" style="display:none;" /></td><td valign="top" class="wppa-box-text wppa-td" style="vertical-align:top; width:70%; " ><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; ;-) &quot; )" title=";-)" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f609.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :| &quot; )" title="|" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f610.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :x &quot; )" title="x" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f621.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :twisted: &quot; )" title="twisted" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f608.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :smile: &quot; )" title="smile" ><img src="/wp-includes/images/smilies/simple-smile.png" alt=":smile:" class="wp-smiley" style="height: 1em; max-height: 1em;" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :shock: &quot; )" title="shock" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f62f.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :sad: &quot; )" title="sad" ><img src="/wp-includes/images/smilies/frownie.png" alt=":sad:" class="wp-smiley" style="height: 1em; max-height: 1em;" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :roll: &quot; )" title="roll" ><img src="/wp-includes/images/smilies/rolleyes.png" alt=":roll:" class="wp-smiley" style="height: 1em; max-height: 1em;" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :razz: &quot; )" title="razz" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f61b.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :oops: &quot; )" title="oops" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f633.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :o &quot; )" title="o" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f62e.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :mrgreen: &quot; )" title="mrgreen" ><img src="/wp-includes/images/smilies/mrgreen.png" alt=":mrgreen:" class="wp-smiley" style="height: 1em; max-height: 1em;" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :lol: &quot; )" title="lol" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f606.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :idea: &quot; )" title="idea" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f4a1.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :grin: &quot; )" title="grin" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f600.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :evil: &quot; )" title="evil" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f47f.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :cry: &quot; )" title="cry" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f625.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :cool: &quot; )" title="cool" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f60e.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :arrow: &quot; )" title="arrow" ><img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/27a1.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :???: &quot; )" title="???" ><img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f615.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :?: &quot; )" title="?" ><img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/2753.png" /></a><a onclick="wppaInsertAtCursor( document.getElementById( &quot;wppa-comment-12&quot; ), &quot; :!: &quot; )" title="!" ><img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/2757.png" /></a><textarea name="wppa-comment" id="wppa-comment-12" style="height:60px; width:100%; "></textarea></td></tr></tbody></table></form></div><div id="wppa-comfooter-wrap-12" style="display:block;" ><table id="wppacommentfooter-12" class="wppa-comment-form" style="margin:0;"><tbody><tr style="text-align:center;"><td style="text-align:center; cursor:pointer;" ><a onclick="wppaOpenComments( 12, -1 ); return false;" >Leave a comment</a></td></tr></tbody></table></div><div style="clear:both"></div>\',\'<div id="iptccontent-12" >No IPTC data</div>\',\'<div id="exifcontent-12" ><a class="-wppa-exif-table-12" onclick="wppaStopShow( 12 );jQuery( &#039;.wppa-exif-table-12&#039; ).css( &#039;display&#039;, &#039;&#039; );jQuery( &#039;.-wppa-exif-table-12&#039; ).css( &#039;display&#039;, &#039;none&#039; );" style="cursor:pointer;display:inline;" >Show EXIF data</a><a class="wppa-exif-table-12" onclick="jQuery( &#039;.wppa-exif-table-12&#039; ).css( &#039;display&#039;, &#039;none&#039; );jQuery( &#039;.-wppa-exif-table-12&#039; ).css( &#039;display&#039;, &#039;&#039; )" style="cursor:pointer;display:none;" >Hide EXIF data</a><div style="clear:both;" ></div><table class="wppa-exif-table-12 wppa-detail" style="display:none; border:0 none; margin:0;" ><tbody><tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none;" ><td class="wppa-exif-label wppa-box-text wppa-td" style="" >Orientation:</td><td class="wppa-exif-value wppa-box-text wppa-td" style="" >1</td></tr><tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none;" ><td class="wppa-exif-label wppa-box-text wppa-td" style="" >Software:</td><td class="wppa-exif-value wppa-box-text wppa-td" style="" >Microsoft Windows Photo Viewer 6.1.7600.16385</td></tr><tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none;" ><td class="wppa-exif-label wppa-box-text wppa-td" style="" >DateTime:</td><td class="wppa-exif-value wppa-box-text wppa-td" style="" >2014:05:30 10:45:43</td></tr><tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none;" ><td class="wppa-exif-label wppa-box-text wppa-td" style="" >Exif_IFD_Pointer:</td><td class="wppa-exif-value wppa-box-text wppa-td" style="" >2200</td></tr></tbody></table></div>\',\'&lt;input type=&quot;button&quot; title=&quot;Download&quot; style=&quot;cursor:pointer; margin-bottom:0px; max-width:500px;&quot; class=&quot;wppa-download-button&quot; onclick=&quot;wppaAjaxMakeOrigName( 12, 133 );&quot; value=&quot;Download: coverphoto (admin)&quot; /&gt;&lt;br /&gt;Views: 6&lt;br /&gt; Thumbnail: &lt;a href=&quot;/wp-content/uploads/wppa/thumbs/133.jpg&quot; rel=&quot;nofollow&quot;&gt;/wp-content/uploads/wppa/thumbs/133.jpg&lt;/a&gt;&lt;br /&gt; Screenres: &lt;a href=&quot;/wp-content/uploads/wppa/133.jpg&quot; rel=&quot;nofollow&quot;&gt;/wp-content/uploads/wppa/133.jpg&lt;/a&gt;&lt;br /&gt; Hires: &lt;a href=&quot;/wp-content/uploads/wppa-source/album-75/coverphoto.jpg&quot; rel=&quot;nofollow&quot;&gt;/wp-content/uploads/wppa-source/album-75/coverphoto.jpg&lt;/a&gt;&lt;br /&gt; Permalink: &lt;a href=&quot;/wp-content/albums/Sub-van-tesje/coverphoto.jpg&quot; rel=&quot;nofollow&quot;&gt;/wp-content/albums/Sub-van-tesje/coverphoto.jpg&lt;/a&gt;&lt;br /&gt; August 13, 2015 3:21 pm&lt;br /&gt;&lt;div style=&quot;float:left; padding:2px;&quot; &gt;&lt;a title=&quot;Tweet coverphoto on Twitter&quot; href=&quot;https://twitter.com/intent/tweet?text=See+this+image+on+WPPA%2B+Beta+test: http%3A%2F%2Fbeta.opajaap.nl%2Fgeneric-2%2F%3Flang%3Den%26wppa-album%3D75%26wppa-photo%3D133%26wppa-cover%3D0%26wppa-occur%3D1%26wppa-single%3D1 coverphoto: Views%3A+6+%0D%0AThumbnail%3A+++%0D%0AScreenres%3A+++%0D%0AHires%3A+++%0D%0APermalink%3A+++%0D%0A...&quot; target=&quot;_blank&quot; &gt;&lt;img src=&quot;/wp-content/plugins/wp-photo-album-plus/images/twitter.png&quot; style=&quot;height:32px;&quot; alt=&quot;Share on Twitter&quot; /&gt;&lt;/a&gt;&lt;/div&gt;&lt;div style=&quot;float:left; padding:2px;&quot; &gt;&lt;a title=&quot;Share coverphoto on Pinterest&quot; href=&quot;http://pinterest.com/pin/create/button/?url=http%3A%2F%2Fbeta.opajaap.nl%2Fgeneric-2%2F%3Flang%3Den%26wppa-album%3D75%26wppa-photo%3D133%26wppa-cover%3D0%26wppa-occur%3D1%26wppa-single%3D1&amp;media=http%3A%2F%2Fbeta.opajaap.nl%2Fwp-content%2Fuploads%2Fwppa%2F133.jpg%3Fver%3D233&amp;description=See+this+image+on+WPPA%2B+Beta+test: Views%3A+6+%0D%0AThumbnail%3A+++%0D%0AScreenres%3A+++%0D%0AHires%3A+++%0D%0APermalink%3A+++%0D%0AAugust+13%2C+2015+3%3A21+pm&quot; target=&quot;_blank&quot; &gt;&lt;img src=&quot;/wp-content/plugins/wp-photo-album-plus/images/pinterest.png&quot; style=&quot;height:32px;&quot; alt=&quot;Share on Pinterest&quot; /&gt;&lt;/a&gt;&lt;/div&gt;&lt;div class=&quot;fb-share-button&quot; style=&quot;float:left; max-width:62px; max-height:64px; overflow:show;&quot; data-width=&quot;200&quot; data-href=&quot;/generic-2/?lang=en&amp;wppa-album=75&amp;wppa-photo=133&amp;wppa-cover=0&amp;wppa-occur=1&amp;wppa-single=1&quot; data-type=&quot;button&quot; &gt;&lt;/div&gt;[script&gt;wppaFbInit();[/script&gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt;\',\'/generic-2/wppaspec/oc1/lnen/cv0/ab75/pt133\',\'<div style="float:left; padding:2px;" ><a title="Tweet coverphoto on Twitter" href="https://twitter.com/intent/tweet?text=See+this+image+on+WPPA%2B+Beta+test: http%3A%2F%2Fbeta.opajaap.nl%2Fgeneric-2%2F%3Flang%3Den%26wppa-album%3D75%26wppa-photo%3D133%26wppa-cover%3D0%26wppa-occur%3D1%26wppa-single%3D1 coverphoto: Views%3A+6+%0D%0AThumbnail%3A+++%0D%0AScreenres%3A+++%0D%0AHires%3A+++%0D%0APermalink%3A+++%0D%0A..." target="_blank" ><img src="/wp-content/plugins/wp-photo-album-plus/images/twitter.png" style="height:32px;" alt="Share on Twitter" /></a></div><div style="float:left; padding:2px;" ><a title="Share coverphoto on Pinterest" href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Fbeta.opajaap.nl%2Fgeneric-2%2F%3Flang%3Den%26wppa-album%3D75%26wppa-photo%3D133%26wppa-cover%3D0%26wppa-occur%3D1%26wppa-single%3D1&media=http%3A%2F%2Fbeta.opajaap.nl%2Fwp-content%2Fuploads%2Fwppa%2F133.jpg%3Fver%3D233&description=See+this+image+on+WPPA%2B+Beta+test: Views%3A+6+%0D%0AThumbnail%3A+++%0D%0AScreenres%3A+++%0D%0AHires%3A+++%0D%0APermalink%3A+++%0D%0AAugust+13%2C+2015+3%3A21+pm" target="_blank" ><img src="/wp-content/plugins/wp-photo-album-plus/images/pinterest.png" style="height:32px;" alt="Share on Pinterest" /></a></div><div class="fb-share-button" style="float:left; " data-width="200" data-href="/generic-2/?lang=en&wppa-album=75&wppa-photo=133&wppa-cover=0&wppa-occur=1&wppa-single=1" data-type="button" ></div>[script>wppaFbInit();[/script><div style="clear:both"></div>\',\'\',\'/wp-content/uploads/wppa-source/album-75/coverphoto.jpg\',\'\',\'\' );
//		/* ]] */</script>';
//echo $str;
//echo '<br/>';
//echo wpautop($str);

// Trimmed down version:
//$str = '
//<script type="text/javascript">
//alert( \'<div style="float:right; margin-right:6px;" >Edit</div><div style="float:right; margin-right:6px;" >Delete</div>\' );
//</script>';
//echo $str;
//echo '<br/>';
//echo wpautop($str);


// To demonstrate wpautop treates html comments wrongly:

//	$str = '<div>My div1</div><!-- comment --><div>My div2</div>';
//	echo $str.'<br/>'.wpautop($str);
//	echo '<!-- End Test area ---><br />';
	// End test area

	// Verify some urls; set to default if empty
	if ( ! $wppa_opt['wppa_start_symbol_url'] ) {
		wppa_update_option( 'wppa_start_symbol_url', wppa_get_imgdir( 'start.png', 'abs' ) );
	}
	if ( ! $wppa_opt['wppa_pause_symbol_url'] ) {
		wppa_update_option( 'wppa_pause_symbol_url', wppa_get_imgdir( 'pause.png', 'abs' ) );
	}
	if ( ! $wppa_opt['wppa_stop_symbol_url'] ) {
		wppa_update_option( 'wppa_stop_symbol_url', wppa_get_imgdir( 'stop.png', 'abs' ) );
	}

	// Initialize
	wppa_initialize_runtime( true );
	$options_error = false;

	// If watermark all is going to be run, make sure the current user has no private overrule settings
	delete_option( 'wppa_watermark_file_'.wppa_get_user() );
	delete_option( 'wppa_watermark_pos_'.wppa_get_user() );

	// Things that wppa-admin-scripts.js needs to know
	echo('<script type="text/javascript">'."\n");
	echo('/* <![CDATA[ */'."\n");
		echo("\t".'wppaImageDirectory = "'.wppa_get_imgdir().'";'."\n");
		echo("\t".'wppaAjaxUrl = "'.admin_url('admin-ajax.php').'";'."\n");
	echo("/* ]]> */\n");
	echo("</script>\n");

	$key = '';
	// Someone hit a submit button or the like?
	if ( isset($_REQUEST['wppa_settings_submit']) ) {	// Yep!

		check_admin_referer(  'wppa-nonce', 'wppa-nonce' );
		$key = $_REQUEST['wppa-key'];
		$sub = $_REQUEST['wppa-sub'];

		// Switch on action key
		switch ( $key ) {

			// Must be here
			case 'wppa_moveup':
				if ( wppa_switch('wppa_split_namedesc') ) {
					$sequence = wppa_opt( 'slide_order_split' );
					$indices = explode(',', $sequence);
					$temp = $indices[$sub];
					$indices[$sub] = $indices[$sub - '1'];
					$indices[$sub - '1'] = $temp;
					wppa_update_option('wppa_slide_order_split', implode(',', $indices));
				}
				else {
					$sequence = wppa_opt( 'slide_order' );
					$indices = explode(',', $sequence);
					$temp = $indices[$sub];
					$indices[$sub] = $indices[$sub - '1'];
					$indices[$sub - '1'] = $temp;
					wppa_update_option('wppa_slide_order', implode(',', $indices));
				}
				break;
			// Should better be here
			case 'wppa_setup':
				wppa_setup(true); // Message on success or fail is in the routine
				break;
			// Must be here
			case 'wppa_backup':
				wppa_backup_settings();	// Message on success or fail is in the routine
				break;
			// Must be here
			case 'wppa_load_skin':
				$fname = wppa_opt( 'skinfile' );

				if ($fname == 'restore') {
					if (wppa_restore_settings(WPPA_DEPOT_PATH.'/settings.bak', 'backup')) {
						wppa_ok_message(__('Saved settings restored', 'wp-photo-album-plus'));
					}
					else {
						wppa_error_message(__('Unable to restore saved settings', 'wp-photo-album-plus'));
						$options_error = true;
					}
				}
				elseif ($fname == 'default' || $fname == '') {
					if (wppa_set_defaults(true)) {
						wppa_ok_message(__('Reset to default settings', 'wp-photo-album-plus'));
					}
					else {
						wppa_error_message(__('Unable to set defaults', 'wp-photo-album-plus'));
						$options_error = true;
					}
				}
				elseif (wppa_restore_settings($fname, 'skin')) {
					wppa_ok_message(sprintf(__('Skinfile %s loaded', 'wp-photo-album-plus'), basename($fname)));
				}
				else {
					// Error printed by wppa_restore_settings()
				}
				break;
			// Must be here
			case 'wppa_watermark_upload':
				if ( isset($_FILES['file_1']) && $_FILES['file_1']['error'] != 4 ) { // Expected a fileupload for a watermark
					$file = $_FILES['file_1'];
					if ( $file['error'] ) {
						wppa_error_message(sprintf(__('Upload error %s', 'wp-photo-album-plus'), $file['error']));
					}
					else {
						$imgsize = getimagesize($file['tmp_name']);
						if ( !is_array($imgsize) || !isset($imgsize[2]) || $imgsize[2] != 3 ) {
							wppa_error_message(sprintf(__('Uploaded file %s is not a .png file', 'wp-photo-album-plus'), $file['name']).' (Type='.$file['type'].').');
						}
						else {
							copy($file['tmp_name'], WPPA_UPLOAD_PATH . '/watermarks/' . basename($file['name']));
							wppa_alert(sprintf(__('Upload of %s done', 'wp-photo-album-plus'), basename($file['name'])));
						}
					}
				}
				else {
					wppa_error_message(__('No file selected or error on upload', 'wp-photo-album-plus'));
				}
				break;

			case 'wppa_watermark_font_upload':
				if ( isset($_FILES['file_2']) && $_FILES['file_2']['error'] != 4 ) { // Expected a fileupload for a watermark font file
					$file = $_FILES['file_2'];
					if ( $file['error'] ) {
						wppa_error_message(sprintf(__('Upload error %s', 'wp-photo-album-plus'), $file['error']));
					}
					else {
						if ( substr($file['name'], -4) != '.ttf' ) {
							wppa_error_message(sprintf(__('Uploaded file %s is not a .ttf file', 'wp-photo-album-plus'), $file['name']).' (Type='.$file['type'].').');
						}
						else {
							copy($file['tmp_name'], WPPA_UPLOAD_PATH . '/fonts/' . basename($file['name']));
							wppa_alert(sprintf(__('Upload of %s done', 'wp-photo-album-plus'), basename($file['name'])));
						}
					}
				}
				else {
					wppa_error_message(__('No file selected or error on upload', 'wp-photo-album-plus'));
				}
				break;

			case 'wppa_audiostub_upload':
				if ( isset($_FILES['file_3']) && $_FILES['file_3']['error'] != 4 ) { // Expected a fileupload
					$file = $_FILES['file_3'];
					if ( $file['error'] ) {
						wppa_error_message(sprintf(__('Upload error %s', 'wp-photo-album-plus'), $file['error']));
					}
					else {
						$imgsize = getimagesize($file['tmp_name']);
						if ( ! is_array( $imgsize ) || ! isset( $imgsize[2] ) || $imgsize[2] < 1 || $imgsize[2] > 3 ) {
							wppa_error_message(sprintf(__('Uploaded file %s is not a valid image file', 'wp-photo-album-plus'), $file['name']).' (Type='.$file['type'].').');
						}
						else {
							switch ( $imgsize[2] ) {
								case '1':
									$ext = '.gif';
									break;
								case '2':
									$ext = '.jpg';
									break;
								case '3':
									$ext = '.png';
									break;
							}
							copy( $file['tmp_name'], WPPA_UPLOAD_PATH . '/audiostub' . $ext );
							wppa_update_option( 'wppa_audiostub', 'audiostub'. $ext );
							// Thumbx, thumby, phtox and photoy must be cleared for the new stub
							$wpdb->query( "UPDATE `" . WPPA_PHOTOS ."` SET `thumbx` = 0, `thumby` = 0, `photox` = 0, `photoy` = 0 WHERE `ext` = 'xxx'" );
							wppa_alert( sprintf( __( 'Upload of %s done', 'wp-photo-album-plus'), basename( $file['name'] ) ) );
						}
					}
				}
				else {
					wppa_error_message(__('No file selected or error on upload', 'wp-photo-album-plus'));
				}
				break;

			case 'wppa_delete_all_from_cloudinary':
				$bret = wppa_delete_all_from_cloudinary();
				if ( $bret ) {
					wppa_ok_message('Done! wppa_delete_all_from_cloudinary');
				}
				else {
					sleep(5);
					wppa_ok_message('Not yet Done! wppa_delete_all_from_cloudinary' .
									'<br />Trying to continue...');
					echo
							'<script type="text/javascript">' .
								'document.location=' .
									'document.location+"&' .
									'wppa_settings_submit=Doit&' .
									'wppa-nonce=' . $_REQUEST['wppa-nonce'] . '&' .
									'wppa-key=' . $key . '&' .
									'_wp_http_referer=' . $_REQUEST['_wp_http_referer'] . '"' .
							'</script>';
				}
				break;

			case 'wppa_delete_derived_from_cloudinary':
				$bret = wppa_delete_derived_from_cloudinary();
				if ( $bret ) {
					wppa_ok_message('Done! wppa_delete_derived_from_cloudinary');
				}
				else {
					sleep(5);
					wppa_ok_message('Not yet Done! wppa_delete_derived_from_cloudinary' .
									'<br />Trying to continue...');
					echo
							'<script type="text/javascript">' .
								'document.location=' .
									'document.location+"&' .
									'wppa_settings_submit=Doit&' .
									'wppa-nonce=' . $_REQUEST['wppa-nonce'] . '&' .
									'wppa-key=' . $key . '&' .
									'_wp_http_referer=' . $_REQUEST['_wp_http_referer'] . '"' .
							'</script>';
				}
				break;

			default: wppa_error_message('Unimplemnted action key: '.$key);
		}

		// Make sure we are uptodate
		wppa_initialize_runtime(true);

	} // wppa-settings-submit

	// Fix invalid ratings
	$iret = $wpdb->query( "DELETE FROM `".WPPA_RATING."` WHERE `value` = 0" );
	if ( $iret ) wppa_update_message( sprintf( __( '%s invalid ratings removed. Please run Table VIII-A5: Rerate to fix the averages.' , 'wp-photo-album-plus'), $iret ) );

	// Fix invalid source path
	wppa_fix_source_path();

	// Check database
	wppa_check_database(true);

	// Cleanup obsolete settings
	$iret = $wpdb->query( "DELETE FROM `".$wpdb->prefix.'options'."` WHERE `option_name` LIKE 'wppa_last_album_used-%'" );
	if ( $iret > '10' ) wppa_update_message( sprintf( __( '%s obsolete settings removed.', 'wp-photo-album-plus'), $iret ) );

?>
	<div class="wrap">
		<?php wppa_admin_spinner() ?>
		<?php $iconurl = WPPA_URL.'/images/settings32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
			<br />
		</div>
		<h2><?php _e('WP Photo Album Plus Settings', 'wp-photo-album-plus'); ?> <span style="color:blue;"><?php _e('Auto Save', 'wp-photo-album-plus') ?></span></h2>
		<?php _e('Database revision:', 'wp-photo-album-plus'); ?> <?php echo(get_option('wppa_revision', '100')) ?>. <?php _e('WP Charset:', 'wp-photo-album-plus'); ?> <?php echo(get_bloginfo('charset')); ?>. <?php echo 'Current PHP version: ' . phpversion() ?>. <?php echo 'WPPA+ API Version: '.$wppa_api_version ?>.
		<br /><?php if ( is_multisite() ) {
			if ( WPPA_MULTISITE_GLOBAL ) {
				_e('Multisite in singlesite mode.', 'wp-photo-album-plus');
			}
			else {
				_e('Multisite enabled.', 'wp-photo-album-plus');
				echo ' ';
				_e('Blogid =', 'wp-photo-album-plus');
				echo ' '.$blog_id;
			}
		}

		// Blacklist
		$blacklist_plugins = array(
			'wp-fluid-images/plugin.php',
			'performance-optimization-order-styles-and-javascript/order-styles-js.php',
			'wp-ultra-simple-paypal-shopping-cart/wp_ultra_simple_shopping_cart.php',
			'cachify/cachify.php',
			'wp-deferred-javascripts/wp-deferred-javascripts.php',
			'frndzk-photo-lightbox-gallery/frndzk_photo_gallery.php',
			);
		$plugins = get_option('active_plugins');
		$matches = array_intersect($blacklist_plugins, $plugins);
		foreach ( $matches as $bad ) {
			wppa_error_message(__('Please de-activate plugin <i style="font-size:14px;">', 'wp-photo-album-plus').substr($bad, 0, strpos($bad, '/')).__('. </i>This plugin will cause wppa+ to function not properly.', 'wp-photo-album-plus'));
		}

		// Graylist
		$graylist_plugins = array(
			'shortcodes-ultimate/shortcodes-ultimate.php',
			'tablepress/tablepress.php'
			);
		$matches = array_intersect($graylist_plugins, $plugins);
		foreach ( $matches as $bad ) {
			wppa_warning_message(__('Please note that plugin <i style="font-size:14px;">', 'wp-photo-album-plus').substr($bad, 0, strpos($bad, '/')).__('</i> can cause wppa+ to function not properly if it is misconfigured.', 'wp-photo-album-plus'));
		}

		// Check for trivial requirements
		if ( ! function_exists('imagecreatefromjpeg') ) {
			wppa_error_message(__('There is a serious misconfiguration in your servers PHP config. Function imagecreatefromjpeg() does not exist. You will encounter problems when uploading photos and not be able to generate thumbnail images. Ask your hosting provider to add GD support with a minimal version 1.8.', 'wp-photo-album-plus'));
		}

		// Check for pending actions
//		if ( wppa_switch( 'indexed_search' ) ) {
			if ( get_option( 'wppa_remake_index_albums_status' ) 	&& get_option( 'wppa_remake_index_albums_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Rebuilding the Album index needs completion. See Table VIII' , 'wp-photo-album-plus') );
			if ( get_option( 'wppa_remake_index_photos_status' ) 	&& get_option( 'wppa_remake_index_photos_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Rebuilding the Photo index needs completion. See Table VIII' , 'wp-photo-album-plus') );
//		}
		if ( get_option( 'wppa_remove_empty_albums_status'	) 		&& get_option( 'wppa_remove_empty_albums_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Remove empty albums needs completion. See Table VIII', 'wp-photo-album-plus') );
		if ( get_option( 'wppa_apply_new_photodesc_all_status' ) 	&& get_option( 'wppa_apply_new_photodesc_all_user', wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Applying new photo description needs completion. See Table VIII', 'wp-photo-album-plus') );
		if ( get_option( 'wppa_append_to_photodesc_status' ) 		&& get_option( 'wppa_append_to_photodesc_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Appending to photo description needs completion. See Table VIII' , 'wp-photo-album-plus') );
		if ( get_option( 'wppa_remove_from_photodesc_status' ) 		&& get_option( 'wppa_remove_from_photodesc_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Removing from photo description needs completion. See Table VIII' , 'wp-photo-album-plus') );
		if ( get_option( 'wppa_remove_file_extensions_status' ) 	&& get_option( 'wppa_remove_file_extensions_user', 	wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Removing file extensions needs completion. See Table VIII' , 'wp-photo-album-plus') );
		if ( get_option( 'wppa_regen_thumbs_status' ) 				&& get_option( 'wppa_regen_thumbs_user', 			wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Regenerating the Thumbnails needs completion. See Table VIII' , 'wp-photo-album-plus') );
		if ( get_option( 'wppa_rerate_status' ) 					&& get_option( 'wppa_rerate_user', 					wppa_get_user() ) == wppa_get_user() ) wppa_warning_message( __( 'Rerating needs completion. See Table VIII' , 'wp-photo-album-plus') );

		// Check for inconsistencies
		if ( ( wppa_opt( 'thumbtype' ) == 'default' ) && (
			wppa_opt( 'tf_width' ) < wppa_opt( 'thumbsize' ) ||
			wppa_opt( 'tf_width_alt') < wppa_opt( 'thumbsize_alt' ) ||
			wppa_opt( 'tf_height' ) < wppa_opt( 'thumbsize' ) ||
			wppa_opt( 'tf_height_alt') < wppa_opt( 'thumbsize_alt' ) ) ) {
				wppa_warning_message( __( 'A thumbframe width or height should not be smaller than a thumbnail size. Please correct the corresponding setting(s) in Table I-C' , 'wp-photo-album-plus') );
			}

?>
		<!--<br /><a href="javascript:window.print();"><?php //_e('Print settings') ?></a><br />-->
		<a style="cursor:pointer;" id="wppa-legon" onclick="jQuery('#wppa-legenda').css('display', ''); jQuery('#wppa-legon').css('display', 'none'); return false;" ><?php _e('Show legenda', 'wp-photo-album-plus') ?></a>
		<div id="wppa-legenda" class="updated" style="line-height:20px; display:none" >
			<div style="float:left"><?php _e('Legenda:', 'wp-photo-album-plus') ?></div><br />
			<?php echo wppa_doit_button(__('Button', 'wp-photo-album-plus')) ?><div style="float:left">&nbsp;:&nbsp;<?php _e('action that causes page reload.', 'wp-photo-album-plus') ?></div>
			<br />
			<input type="button" onclick="if ( confirm('<?php _e('Are you sure?', 'wp-photo-album-plus') ?>') ) return true; else return false;" class="button-secundary" style="float:left; border-radius:3px; font-size: 12px; height: 18px; margin: 0 4px; padding: 0px;" value="<?php _e('Button', 'wp-photo-album-plus') ?>" />
			<div style="float:left">&nbsp;:&nbsp;<?php _e('action that does not cause page reload.', 'wp-photo-album-plus') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>star.png" title="<?php _e('Setting unmodified', 'wp-photo-album-plus') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Setting unmodified', 'wp-photo-album-plus') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>clock.png" title="<?php _e('Update in progress', 'wp-photo-album-plus') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Update in progress', 'wp-photo-album-plus') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>tick.png" title="<?php _e('Setting updated', 'wp-photo-album-plus') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Setting updated', 'wp-photo-album-plus') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>cross.png" title="<?php _e('Update failed', 'wp-photo-album-plus') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Update failed', 'wp-photo-album-plus') ?></div>
			<br />
			&nbsp;<a style="cursor:pointer;" onclick="jQuery('#wppa-legenda').css('display', 'none'); jQuery('#wppa-legon').css('display', ''); return false;" ><?php _e('Hide this', 'wp-photo-album-plus') ?></a>
		</div>
<?php
		// Quick open selections
		$wppa_tags = array(
							'-' 		=> '',
							'system' 	=> __('System', 'wp-photo-album-plus'),
							'access' 	=> __('Access', 'wp-photo-album-plus'),
							'album' 	=> __('Albums', 'wp-photo-album-plus'),
							'audio' 	=> __('Audio', 'wp-photo-album-plus'),
							'comment' 	=> __('Comments', 'wp-photo-album-plus'),
							'count' 	=> __('Counts', 'wp-photo-album-plus'),
							'cover' 	=> __('Covers', 'wp-photo-album-plus'),
							'layout' 	=> __('Layout', 'wp-photo-album-plus'),
							'lightbox' 	=> __('Lightbox', 'wp-photo-album-plus'),
							'link' 		=> __('Links', 'wp-photo-album-plus'),
							'meta' 		=> __('Metadata', 'wp-photo-album-plus'),
							'navi' 		=> __('Navigation', 'wp-photo-album-plus'),
							'page' 		=> __('Page', 'wp-photo-album-plus'),
							'rating' 	=> __('Rating', 'wp-photo-album-plus'),
							'search' 	=> __('Search', 'wp-photo-album-plus'),
							'size' 		=> __('Sizes', 'wp-photo-album-plus'),
							'slide' 	=> __('Slideshows', 'wp-photo-album-plus'),
							'sm' 		=> __('Social Media', 'wp-photo-album-plus'),
							'thumb' 	=> __('Thumbnails', 'wp-photo-album-plus'),
							'upload' 	=> __('Uploads', 'wp-photo-album-plus'),
							'widget' 	=> __('Widgets', 'wp-photo-album-plus'),
							'water' 	=> __('Watermark', 'wp-photo-album-plus'),
							'video' 	=> __('Video', 'wp-photo-album-plus')
							);

		asort( $wppa_tags );

?>
		<p>
			<?php _e('Click on the banner of a (sub)table to open/close it, or', 'wp-photo-album-plus') ?>
			<br />
			<?php _e('Show settings related to:', 'wp-photo-album-plus') ?>
			<select id="wppa-quick-selbox-1" onchange="wppaQuickSel()">
				<?php foreach( array_keys($wppa_tags) as $key ) { ?>
					<option value="<?php echo $key ?>"><?php echo $wppa_tags[$key] ?></option>
				<?php } ?>
			</select>
			<?php _e('and ( optionally ) to:', 'wp-photo-album-plus') ?>
			<select id="wppa-quick-selbox-2" onchange="wppaQuickSel()">
				<?php foreach( array_keys($wppa_tags) as $key ) { ?>
					<option value="<?php echo $key ?>"><?php echo $wppa_tags[$key] ?></option>
				<?php } ?>
			</select>
		</p>

		<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_options')) ?>" method="post">

			<?php wp_nonce_field('wppa-nonce', 'wppa-nonce'); ?>
			<input type="hidden" name="wppa-key" id="wppa-key" value="" />
			<input type="hidden" name="wppa-sub" id="wppa-sub" value="" />
			<?php if ( get_option('wppa_i_done') == 'done' ) { ?>
			<a class="-wppa-quick" onclick="jQuery('.wppa-quick').css('display','inline');jQuery('.-wppa-quick').css('display','none')" ><?php _e('Quick setup', 'wp-photo-album-plus') ?></a>
			<?php } else { ?>
			<input type="button" class="-wppa-quick" onclick="jQuery('.wppa-quick').css('display','inline');jQuery('.-wppa-quick').css('display','none')" value="<?php _e('Do a quick initial setup', 'wp-photo-album-plus') ?>" />
			<input type="button" style="display:none;" class="wppa-quick" onclick="jQuery('.-wppa-quick').css('display','inline');jQuery('.wppa-quick').css('display','none')" value="<?php _e('Close quick setup', 'wp-photo-album-plus') ?>" />
			<?php } ?>

			<div class="wppa-quick" style="display:none;" >
			<?php // Table 0: Quick Setup ?>
			<?php wppa_settings_box_header(
				'0',
				__('Table O:', 'wp-photo-album-plus').' '.__('Quick Setup:', 'wp-photo-album-plus').' '.
				__('This table enables you to quickly do an inital setup.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_0" style=" margin:0; padding:0; " class="inside" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_1">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_0">
							<?php
							$no_default = true;

							$wppa_table = '0';

							$clas = '';
							$tags = '';
						wppa_setting_subheader( '', '1', __('To quickly setup WPPA+ please answer the following questions. You can alway change any setting later. <span style="color:#700">Click on me!</span>', 'wp-photo-album-plus'));
							{
							$name = __('Is your theme <i>responsive</i>?', 'wp-photo-album-plus');
							$desc = __('Responsive themes have a layout that varies with the size of the browser window.', 'wp-photo-album-plus');
							$help = esc_js(__('WPPA+ needs to know this to automaticly adept the width of the display to the available width on the page.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_responsive';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do you want to downsize photos during upload?', 'wp-photo-album-plus');
							$desc = __('Downsizing photos make them load faster to the visitor, without loosing display quality', 'wp-photo-album-plus');
							$help = esc_js(__('If you answer yes, the photos will be downsized to max 1024 x 768 pixels. You can change this later, if you like', 'wp-photo-album-plus'));
							$slug = 'wppa_i_downsize';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do you want to save the original photofiles?', 'wp-photo-album-plus');
							$desc = __('This will require considerable disk space on the server.', 'wp-photo-album-plus');
							$help = esc_js(__('If you answer yes, you will be able to remove watermarks you applied with wppa+ in a later stage, redo downsizing to a larger size afterwards, and supply fullsize images for download.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_source';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('May visitors upload photos?', 'wp-photo-album-plus');
							$desc = __('It is safe to do so, but i will have to do some settings to keep it safe!', 'wp-photo-album-plus');
							$help = esc_js(__('If you answer yes, i will assume you want to enable logged in users to upload photos at the front-end of the website and allow them to edit their photos name and descriptions.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The photos will be hold for moderation, the admin will get notified by email.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Each user will get his own album to upload to. These settings can be changed later.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_userupload';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do you want the rating system active?', 'wp-photo-album-plus');
							$desc = __('Enable the rating system and show the votes in the slideshow.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details of the rating system later', 'wp-photo-album-plus'));
							$slug = 'wppa_i_rating';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do you want the comment system active?', 'wp-photo-album-plus');
							$desc = __('Enable the comment system and show the comments in the slideshow.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details of the comment system later', 'wp-photo-album-plus'));
							$slug = 'wppa_i_comment';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do you want the social media share buttons displayed?', 'wp-photo-album-plus');
							$desc = __('Display the social media buttons in the slideshow', 'wp-photo-album-plus');;
							$help = esc_js(__('These buttons share the specific photo rather than the page where it is displayed on', 'wp-photo-album-plus'));
							$slug = 'wppa_i_share';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to use IPTC data?', 'wp-photo-album-plus');
							$desc = __('IPTC data is information you may have added in a photo manipulation program.', 'wp-photo-album-plus');
							$help = esc_js(__('The information can be displayed in slideshows and in photo descriptions.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_iptc';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to use EXIF data?', 'wp-photo-album-plus');
							$desc = __('EXIF data is information from the camera like model no, focal distance and aperture used.', 'wp-photo-album-plus');
							$help = esc_js(__('The information can be displayed in slideshows and in photo descriptions.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_exif';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to use GPX data?', 'wp-photo-album-plus');
							$desc = __('Some cameras and mobile devices save the geographic location where the photo is taken.', 'wp-photo-album-plus');
							$help = esc_js(__('A Google map can be displayed in slideshows.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_gpx';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to use Fotomoto?', 'wp-photo-album-plus');
							$desc = __('<a href="http://www.fotomoto.com/" target="_blank" >Fotomoto</a> is an on-line print service.', 'wp-photo-album-plus');
							$help = esc_js(__('If you answer Yes, you will have to open an account on Fotomoto.', 'wp-photo-album-plus'));
							$slug = 'wppa_i_fotomoto';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to add videofiles?', 'wp-photo-album-plus');
							$desc = __('You can mix videos and photos in any album.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details later', 'wp-photo-album-plus'));
							$slug = 'wppa_i_video';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to add audiofiles?', 'wp-photo-album-plus');
							$desc = __('You can add audio to photos in any album.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details later', 'wp-photo-album-plus'));
							$slug = 'wppa_i_audio';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Are you going to upload 3D stereo photos?', 'wp-photo-album-plus');
							$desc = __('You can add l-r and r-l stereo photo pairs.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details later', 'wp-photo-album-plus'));
							$slug = 'wppa_i_stereo';
							$opts = array('', 'yes', 'no');
							$vals = array('', 'yes', 'no');
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Done?', 'wp-photo-album-plus');
							$desc = __('If you are ready answering these questions, select <b>yes</b>', 'wp-photo-album-plus');
							$help = esc_js(__('You can change any setting later, and be more specific and add a lot of settings. For now it is enough, go create albums and upload photos!', 'wp-photo-album-plus'));
							$slug = 'wppa_i_done';
							$opts = array('', 'yes');
							$vals = array('', 'yes');
							$closetext = esc_js(__('Thank you!. The most important settings are done now. You can refine your settings, the behaviour and appearance of WPPA+ in the Tables below.', 'wp-photo-album-plus'));
							$postaction = 'alert(\''.$closetext.'\');setTimeout(\'document.location.reload(true)\', 1000)';
							$html = wppa_select($slug, $opts, $vals, '', '', false, $postaction);
							wppa_setting($slug, '99', $name, $desc, $html, $help, $clas, $tags);

							$no_default = false;
							}
							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_1">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<?php // Table 1: Sizes ?>
			<?php wppa_settings_box_header(
				'1',
				__('Table I:', 'wp-photo-album-plus').' '.__('Sizes:', 'wp-photo-album-plus').' '.
				__('This table describes all the sizes and size options (except fontsizes) for the generation and display of the WPPA+ elements.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_1" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_1">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_1">
							<?php
							$wppa_table = 'I';

						wppa_setting_subheader( 'A', '1', __( 'WPPA+ global system related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Column Width', 'wp-photo-album-plus');
							$desc = __('The width of the main column in your theme\'s display area.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the width of the main column in your theme\'s display area.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You should set this value correctly to make sure the fullsize images are properly aligned horizontally.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('You may enter \'auto\' for use in themes that have a floating content column.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The use of \'auto\' is required for responsive themes.', 'wp-photo-album-plus'));
							$slug = 'wppa_colwidth';
							$onchange = 'wppaCheckFullHalign()';
							$html = wppa_input($slug, '40px', '', __('pixels wide', 'wp-photo-album-plus'), $onchange);
							$clas = '';
							$tags = 'size,system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Initial Width', 'wp-photo-album-plus');
							$desc = __('The most often displayed colun width in responsive theme', 'wp-photo-album-plus');
							$help = esc_js(__('Change this value only if your responsive theme shows initially a wrong column width.', 'wp-photo-album-plus'));
							$slug = 'wppa_initial_colwidth';
							$html = wppa_input($slug, '40px', '', __('pixels wide', 'wp-photo-album-plus'));
							$clas = 'wppa_init_resp_width';
							$tags = 'size,system';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Resize on Upload', 'wp-photo-album-plus');
							$desc = __('Indicate if the photos should be resized during upload.', 'wp-photo-album-plus');
							$help = esc_js(__('If you check this item, the size of the photos will be reduced to the dimension specified in the next item during the upload/import process.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The photos will never be stretched during upload if they are smaller.', 'wp-photo-album-plus'));
							$slug = 'wppa_resize_on_upload';
							$onchange = 'wppaCheckResize()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'size,upload';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Resize to', 'wp-photo-album-plus');
							$desc = __('Resize photos to fit within a given area.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the screensize for the unscaled photos.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The use of a non-default value is particularly usefull when you make use of lightbox functionality.', 'wp-photo-album-plus'));
							$slug = 'wppa_resize_to';
							$px = __('pixels', 'wp-photo-album-plus');
							$options = array(__('Fit within rectangle as set in Table I-B1,2', 'wp-photo-album-plus'), '640 x 480 '.$px, '800 x 600 '.$px, '1024 x 768 '.$px, '1200 x 900 '.$px, '1280 x 960 '.$px, '1366 x 768 '.$px, '1920 x 1080 '.$px);
							$values = array( '0', '640x480', '800x600', '1024x768', '1200x900', '1280x960', '1366x768', '1920x1080');
							$html = wppa_select($slug, $options, $values);
							$clas = 're_up';
							$tags = 'size,upload';
							wppa_setting('', '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photocount threshold', 'wp-photo-album-plus');
							$desc = __('Number of photos in an album must exceed.', 'wp-photo-album-plus');
							$help = esc_js(__('Photos do not show up in the album unless there are more than this number of photos in the album. This allows you to have cover photos on an album that contains only sub albums without seeing them in the list of sub albums. Usually set to 0 (always show) or 1 (for one cover photo).', 'wp-photo-album-plus'));
							$slug = 'wppa_min_thumbs';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Border thickness', 'wp-photo-album-plus');
							$desc = __('Thickness of wppa+ box borders.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the thickness for the border of the WPPA+ boxes. A number of 0 means: no border.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('WPPA+ boxes are: the navigation bars and the filmstrip.', 'wp-photo-album-plus'));
							$slug = 'wppa_bwidth';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Border radius', 'wp-photo-album-plus');
							$desc = __('Radius of wppa+ box borders.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the corner radius for the border of the WPPA+ boxes. A number of 0 means: no rounded corners.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('WPPA+ boxes are: the navigation bars and the filmstrip.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Note that rounded corners are only supported by modern browsers.', 'wp-photo-album-plus'));
							$slug = 'wppa_bradius';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Box spacing', 'wp-photo-album-plus');
							$desc = __('Distance between wppa+ boxes.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_box_spacing';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Related count', 'wp-photo-album-plus');
							$desc = __('The default maximum number of related photos to find.', 'wp-photo-album-plus');
							$help = esc_js(__('When using shortcodes like [wppa type="album" album="#related,desc,23"][/wppa], the maximum number is 23. Omitting the number gives the maximum of this setting.', 'wp-photo-album-plus'));
							$slug = 'wppa_related_count';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max Pagelinks', 'wp-photo-album-plus');
							$desc = __('The maximum number of pagelinks to be displayed.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_pagelinks_max';
							$html = wppa_input($slug, '40px', '', __('pages', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max file name length', 'wp-photo-album-plus');
							$desc = __('The max length of a photo file name excluding the extension.', 'wp-photo-album-plus');
							$help = esc_js(__('A setting of 0 means: unlimited.', 'wp-photo-album-plus'));
							$slug = 'wppa_max_filename_length';
							$html = wppa_input($slug, '40px', '', __('chars', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,system';
							wppa_setting($slug, '10.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max photo name length', 'wp-photo-album-plus');
							$desc = __('The max length of a photo name.', 'wp-photo-album-plus');
							$help = esc_js(__('A setting of 0 means: unlimited.', 'wp-photo-album-plus'));
							$slug = 'wppa_max_photoname_length';
							$html = wppa_input($slug, '40px', '', __('chars', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,system';
							wppa_setting($slug, '10.2', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'B', '1', __( 'Slideshow related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Maximum Width', 'wp-photo-album-plus');
							$desc = __('The maximum width photos will be displayed in slideshows.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the largest size in pixels as how you want your photos to be displayed.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This is usually the same as the Column Width (Table I-A1), but it may differ.', 'wp-photo-album-plus'));
							$slug = 'wppa_fullsize';
							$onchange = 'wppaCheckFullHalign()';
							$html = wppa_input($slug, '40px', '', __('pixels wide', 'wp-photo-album-plus'), $onchange);
							$clas = '';
							$tags = 'size,slide';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Maximum Height', 'wp-photo-album-plus');
							$desc = __('The maximum height photos will be displayed in slideshows.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the largest size in pixels as how you want your photos to be displayed.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This setting defines the height of the space reserved for photos in slideshows.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you change the width of a display by the %%size= command, this value changes proportionally to match the aspect ratio as defined by this and the previous setting.', 'wp-photo-album-plus'));
							$slug = 'wppa_maxheight';
							$html = wppa_input($slug, '40px', '', __('pixels high', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,slide';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Stretch to fit', 'wp-photo-album-plus');
							$desc = __('Stretch photos that are too small.', 'wp-photo-album-plus');
							$help = esc_js(__('Images will be stretched to the Maximum Size at display time if they are smaller. Leaving unchecked is recommended. It is better to upload photos that fit well the sizes you use!', 'wp-photo-album-plus'));
							$slug = 'wppa_enlarge';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'size,system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow borderwidth', 'wp-photo-album-plus');
							$desc = __('The width of the border around slideshow images.', 'wp-photo-album-plus');
							$help = esc_js(__('The border is made by the image background being larger than the image itsself (padding).', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Additionally there may be a one pixel outline of a different color. See Table III-A2.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The number you enter here is exclusive the one pixel outline.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you leave this entry empty, there will be no outline either.', 'wp-photo-album-plus'));
							$slug = 'wppa_fullimage_border_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,slide,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Numbar Max', 'wp-photo-album-plus');
							$desc = __('Maximum numbers to display.', 'wp-photo-album-plus');
							$help = esc_js(__('In order to attemt to fit on one line, the numbers will be replaced by dots - except the current - when there are more than this number of photos in a slideshow.', 'wp-photo-album-plus'));
							$slug = 'wppa_numbar_max';
							$html = wppa_input($slug, '40px', '', __('numbers', 'wp-photo-album-plus'));
							$clas = 'wppa_numbar';
							$tags = 'count,slide,navi';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Share button size', 'wp-photo-album-plus');
							$desc = __('The size of the social media icons in the Share box', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_size';
							$opts = array('16 x 16', '20 x 20', '32 x 32');
							$vals = array('16', '20', '32');
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_share';
							$tags = 'size,sm,slide';
							wppa_setting($slug, '6', $name, $desc, $html.__('pixels', 'wp-photo-album-plus'), $help, $clas, $tags);

							$name = __('Mini Treshold', 'wp-photo-album-plus');
							$desc = __('Show mini text at slideshow smaller then.', 'wp-photo-album-plus');
							$help = esc_js(__('Display Next and Prev. as opposed to Next photo and Previous photo when the cotainer is smaller than this size.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Special use in responsive themes.', 'wp-photo-album-plus'));
							$slug = 'wppa_mini_treshold';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,slide,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow pagesize', 'wp-photo-album-plus');
							$desc = __('The maximum number of slides in a certain view. 0 means no pagination', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_slideshow_pagesize';
							$html = wppa_input($slug, '40px', '', __('slides', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,page,slide';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Filmstrip Thumbnail Size', 'wp-photo-album-plus');
							$desc = __('The size of the filmstrip images.', 'wp-photo-album-plus');
							$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the thumbnail size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$slug = 'wppa_film_thumbsize';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,slide,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideonly max', 'wp-photo-album-plus');
							$desc = __('The max number of lides in a slideonly display', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_slideonly_max';
							$html = wppa_input($slug, '40px', '', __('slides', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,slide';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'C', '1', __( 'Thumbnail photos related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Thumbnail Size', 'wp-photo-album-plus');
							$desc = __('The size of the thumbnail images.', 'wp-photo-album-plus');
							$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the thumbnail size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbsize';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'tt_normal tt_masonry';
							$tags = 'size,thumb';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail Size Alt', 'wp-photo-album-plus');
							$desc = __('The alternative size of the thumbnail images.', 'wp-photo-album-plus');
							$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the thumbnail size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbsize_alt';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'tt_normal tt_masonry';
							$tags = 'size,thumb';
							wppa_setting($slug, '1a', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail Aspect', 'wp-photo-album-plus');
							$desc = __('Aspect ration of thumbnail image', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_thumb_aspect';
							$options = array(
								__('--- same as fullsize ---', 'wp-photo-album-plus'),
								__('--- square clipped ---', 'wp-photo-album-plus'),
								__('4:5 landscape clipped', 'wp-photo-album-plus'),
								__('3:4 landscape clipped', 'wp-photo-album-plus'),
								__('2:3 landscape clipped', 'wp-photo-album-plus'),
								__('9:16 landscape clipped', 'wp-photo-album-plus'),
								__('1:2 landscape clipped', 'wp-photo-album-plus'),
								__('--- square padded ---', 'wp-photo-album-plus'),
								__('4:5 landscape padded', 'wp-photo-album-plus'),
								__('3:4 landscape padded', 'wp-photo-album-plus'),
								__('2:3 landscape padded', 'wp-photo-album-plus'),
								__('9:16 landscape padded', 'wp-photo-album-plus'),
								__('1:2 landscape padded', 'wp-photo-album-plus')
								);
							$values = array(
								'0:0:none',
								'1:1:clip',
								'4:5:clip',
								'3:4:clip',
								'2:3:clip',
								'9:16:clip',
								'1:2:clip',
								'1:1:padd',
								'4:5:padd',
								'3:4:padd',
								'2:3:padd',
								'9:16:padd',
								'1:2:padd'
								);
							$html = wppa_select($slug, $options, $values);
							$clas = 'tt_normal tt_masonry';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbframe width', 'wp-photo-album-plus');
							$desc = __('The width of the thumbnail frame.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the width of the thumbnail frame.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wp-photo-album-plus'));
							$slug = 'wppa_tf_width';
							$html = wppa_input($slug, '40px', '', __('pixels wide', 'wp-photo-album-plus'));
							$clas = 'tt_normal';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbframe width Alt', 'wp-photo-album-plus');
							$desc = __('The width of the alternative thumbnail frame.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the width of the thumbnail frame.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wp-photo-album-plus'));
							$slug = 'wppa_tf_width_alt';
							$html = wppa_input($slug, '40px', '', __('pixels wide', 'wp-photo-album-plus'));
							$clas = 'tt_normal';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '3a', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbframe height', 'wp-photo-album-plus');
							$desc = __('The height of the thumbnail frame.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the height of the thumbnail frame.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wp-photo-album-plus'));
							$slug = 'wppa_tf_height';
							$html = wppa_input($slug, '40px', '', __('pixels high', 'wp-photo-album-plus'));
							$clas = 'tt_normal';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbframe height Alt', 'wp-photo-album-plus');
							$desc = __('The height of the alternative thumbnail frame.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the height of the thumbnail frame.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wp-photo-album-plus'));
							$slug = 'wppa_tf_height_alt';
							$html = wppa_input($slug, '40px', '', __('pixels high', 'wp-photo-album-plus'));
							$clas = 'tt_normal';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '4a', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail spacing', 'wp-photo-album-plus');
							$desc = __('The spacing between adjacent thumbnail frames.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the minimal spacing between the adjacent thumbnail frames', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wp-photo-album-plus'));
							$slug = 'wppa_tn_margin';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'tt_normal tt_masonry';
							$tags = 'size,thumb,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto spacing', 'wp-photo-album-plus');
							$desc = __('Space the thumbnail frames automatic.', 'wp-photo-album-plus');
							$help = esc_js(__('If you check this box, the thumbnail images will be evenly distributed over the available width.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('In this case, the thumbnail spacing value (setting I-9) will be regarded as a minimum value.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_auto';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'size,layout,thumb';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page size', 'wp-photo-album-plus');
							$desc = __('Max number of thumbnails per page.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of thumbnail images per page. A value of 0 indicates no pagination.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_page_size';
							$html = wppa_input($slug, '40px', '', __('thumbnails', 'wp-photo-album-plus'));
							$clas = 'tt_always';
							$tags = 'count,thumb,page';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup size', 'wp-photo-album-plus');
							$desc = __('The size of the thumbnail popup images.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size of the popup images. This size should be larger than the thumbnail size.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This size should also be at least the cover image size.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the popup size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Although this setting has only visual effect if "Thumb popup" (Table IV-C8) is checked,', 'wp-photo-album-plus'));
							$help .= ' '.esc_js(__('the value must be right as it is the physical size of the thumbnail and coverphoto images.', 'wp-photo-album-plus'));
							$slug = 'wppa_popupsize';
							$clas = 'tt_normal tt_masonry';
							$tags = 'size,thumb';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use thumbs if fit', 'wp-photo-album-plus');
							$desc = __('Use the thumbnail image files if they are large enough.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting speeds up page loading for small photos.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Do NOT use this when your thumbnails have a forced aspect ratio (when Table I-C2 is set to anything different from --- same as fullsize ---)', 'wp-photo-album-plus'));
							$slug = 'wppa_use_thumbs_if_fit';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'thumb,system';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);
							}
							wppa_setting_subheader( 'D', '1', __( 'Album cover related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Max Cover width', 'wp-photo-album-plus');
							$desc = __('Maximum width for a album cover display.', 'wp-photo-album-plus');
							$help = esc_js(__('Display covers in 2 or more columns if the display area is wider than the given width.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This also applies for \'thumbnails as covers\', and will NOT apply to single items.', 'wp-photo-album-plus'));
							$slug = 'wppa_max_cover_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,layout,size';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Min Cover height', 'wp-photo-album-plus');
							$desc = __('Minimal height of an album cover.', 'wp-photo-album-plus');
							$help = esc_js(__('If you use this setting to make the albums the same height and you are not satisfied about the lay-out, try increasing the value in the next setting', 'wp-photo-album-plus'));
							$slug = 'wppa_cover_minheight';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,layout,size';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Min Text frame height', 'wp-photo-album-plus');
							$desc = __('The minimal cover text frame height incl header.', 'wp-photo-album-plus');
							$help = esc_js(__('The height starting with the album title up to and including the view- and the slideshow- links.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting enables you to give the album covers the same height while the title does not need to fit on one line.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This is the recommended setting to line-up your covers!', 'wp-photo-album-plus'));
							$slug = 'wppa_head_and_text_frame_height';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,size,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Min Description height', 'wp-photo-album-plus');
							$desc = __('The minimal height of the album description text frame.', 'wp-photo-album-plus');
							$help = esc_js(__('The minimal height of the description field in an album cover display.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting enables you to give the album covers the same height provided that the cover images are equally sized and the titles fit on one line.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('To force the coverphotos have equal heights, tick the box in Table I-D7.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You may need this setting if changing the previous setting is not sufficient to line-up the covers.', 'wp-photo-album-plus'));
							$slug = 'wppa_text_frame_height';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,size,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Coverphoto size', 'wp-photo-album-plus');
							$desc = __('The size of the coverphoto.', 'wp-photo-album-plus');
							$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the coverphoto size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$slug = 'wppa_smallsize';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,thumb,size';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Coverphoto size multi', 'wp-photo-album-plus');
							$desc = __('The size of coverphotos if more than one.', 'wp-photo-album-plus');
							$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Changing the coverphoto size may result in all thumbnails being regenerated. this may take a while.', 'wp-photo-album-plus'));
							$slug = 'wppa_smallsize_multi';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,thumb,size';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Size is height', 'wp-photo-album-plus');
							$desc = __('The size of the coverphoto is the height of it.', 'wp-photo-album-plus');
							$help = esc_js(__('If set: the previous setting is the height, if unset: the largest of width and height.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This setting applies for coverphoto position top or bottom only (Table IV-D3).', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This makes it easyer to make the covers of equal height.', 'wp-photo-album-plus'));
							$slug = 'wppa_coversize_is_height';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,thumb,size';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page size', 'wp-photo-album-plus');
							$desc = __('Max number of covers per page.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of album covers per page. A value of 0 indicates no pagination.', 'wp-photo-album-plus'));
							$slug = 'wppa_album_page_size';
							$html = wppa_input($slug, '40px', '', __('covers', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'cover,album,count';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);
							}
							wppa_setting_subheader( 'E', '1', __( 'Rating and comment related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Rating size', 'wp-photo-album-plus');
							$desc = __('Select the number of voting stars.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_rating_max';
							$options = array(__('Standard: 5 stars', 'wp-photo-album-plus'), __('Extended: 10 stars', 'wp-photo-album-plus'), __('One button vote', 'wp-photo-album-plus'));
							$values = array('5', '10', '1');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_rating_';
							$tags = 'count,rating,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Display precision', 'wp-photo-album-plus');
							$desc = __('Select the desired rating display precision.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_rating_prec';
							$options = array('1 '.__('decimal places', 'wp-photo-album-plus'), '2 '.__('decimal places', 'wp-photo-album-plus'), '3 '.__('decimal places', 'wp-photo-album-plus'), '4 '.__('decimal places', 'wp-photo-album-plus'));
							$values = array('1', '2', '3', '4');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Avatar size', 'wp-photo-album-plus');
							$desc = __('Size of Avatar images.', 'wp-photo-album-plus');
							$help = esc_js(__('The size of the square avatar; must be > 0 and < 256', 'wp-photo-album-plus'));
							$slug = 'wppa_gravatar_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'comment,size,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating space', 'wp-photo-album-plus');
							$desc = __('Space between avg and my rating stars', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ratspacing';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'rating,layout,size';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);
							}
							wppa_setting_subheader( 'F', '1', __( 'Widget related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Widget width', 'wp-photo-album-plus');
							$desc = __('The useable width within widgets.', 'wp-photo-album-plus');
							$help = esc_js(__('Widget width for photo of the day, general purpose (default), slideshow (default) and upload widgets.', 'wp-photo-album-plus'));
							$slug = 'wppa_widget_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('TopTen count', 'wp-photo-album-plus');
							$desc = __('Number of photos in TopTen widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of rated photos in the TopTen widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_topten_count';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = 'wppa_rating';
							$tags = 'count,widget';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('TopTen size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in TopTen widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the TopTen widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_topten_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa_rating';
							$tags = 'size,widget';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment count', 'wp-photo-album-plus');
							$desc = __('Number of entries in Comment widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of entries in the Comment widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_comten_count';
							$html = wppa_input($slug, '40px', '', __('entries', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,widget';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in Comment widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the Comment widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_comten_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail count', 'wp-photo-album-plus');
							$desc = __('Number of photos in Thumbnail widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of rated photos in the Thumbnail widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbnail_widget_count';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,widget';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail widget size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in Thumbnail widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the Thumbnail widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbnail_widget_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('LasTen count', 'wp-photo-album-plus');
							$desc = __('Number of photos in Last Ten widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of photos in the LasTen widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_lasten_count';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,widget';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('LasTen size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in Last Ten widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the LasTen widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_lasten_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Album widget count', 'wp-photo-album-plus');
							$desc = __('Number of albums in Album widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of thumbnail photos of albums in the Album widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_album_widget_count';
							$html = wppa_input($slug, '40px', '', __('albums', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,widget';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Album widget size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in Album widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the Album widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_album_widget_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('FeaTen count', 'wp-photo-album-plus');
							$desc = __('Number of photos in Featured Ten widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum number of photos in the FeaTen widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_featen_count';
							$html = wppa_input($slug, '40px', '', __('photos', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'count,widget';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('FeaTen size', 'wp-photo-album-plus');
							$desc = __('Size of thumbnails in Featured Ten widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the size for the mini photos in the FeaTen widget.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wp-photo-album-plus'));
							$slug = 'wppa_featen_size';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,widget';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tagcloud min size', 'wp-photo-album-plus');
							$desc = __('Minimal fontsize in tagclouds', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_tagcloud_min';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'layout,size,widget';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tagcloud max size', 'wp-photo-album-plus');
							$desc = __('Maximal fontsize in tagclouds', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_tagcloud_max';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'layout,size,widget';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);
							}
							wppa_setting_subheader( 'G', '1', __( 'Lightbox related size settings. These settings have effect only when Table IX-J3 is set to wppa' , 'wp-photo-album-plus') );
							{
							$name = __('Number of text lines', 'wp-photo-album-plus');
							$desc = __('Number of lines on the lightbox description area, exclusive the n/m line.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter a number in the range from 0 to 24 or auto', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_txt_lines';
							$html = wppa_input($slug, '40px', '', __('lines', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,lightbox,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Magnifier cursor size', 'wp-photo-album-plus');
							$desc = __('Select the size of the magnifier cursor.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_magnifier';
							$options = array(__('small','wppa', 'wp-photo-album-plus'), __('medium', 'wp-photo-album-plus'), __('large', 'wp-photo-album-plus'), __('--- none ---', 'wp-photo-album-plus'));
							$values  = array('magnifier-small.png', 'magnifier-medium.png', 'magnifier-large.png', '');
							$onchange = 'jQuery(\'#wppa-cursor\').attr(\'alt\', \'Pointer\');document.getElementById(\'wppa-cursor\').src=wppaImageDirectory+document.getElementById(\'wppa_magnifier\').value';
							$html = wppa_select($slug, $options, $values, $onchange).'&nbsp;&nbsp;<img id="wppa-cursor" src="'.wppa_get_imgdir().wppa_opt( $slug ).'" />';
							$clas = '';
							$tags = 'lightbox,size,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);
							echo '<script>'.$onchange.'</script>';

							$name = __('Border width', 'wp-photo-album-plus');
							$desc = __('Border width for lightbox display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_border_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,lightbox,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Border radius', 'wp-photo-album-plus');
							$desc = __('Border radius for lightbox display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_border_radius';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'size,lightbox,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'H', '1', __( 'Video related size settings' , 'wp-photo-album-plus') );
							{
							$name = __('Default width', 'wp-photo-album-plus');
							$desc = __('The width of most videos', 'wp-photo-album-plus');
							$help = esc_js('This setting can be overruled for individual videos on the photo admin pages.');
							$slug = 'wppa_video_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa-video';
							$tags = 'size,video';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default height', 'wp-photo-album-plus');
							$desc = __('The height of most videos', 'wp-photo-album-plus');
							$help = esc_js('This setting can be overruled for individual videos on the photo admin pages.');
							$slug = 'wppa_video_height';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa-video';
							$tags = 'size,video';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);
							}

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_1">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 2: Visibility ?>
			<?php wppa_settings_box_header(
				'2',
				__('Table II:', 'wp-photo-album-plus').' '.__('Visibility:', 'wp-photo-album-plus').' '.
				__('This table describes the visibility of certain wppa+ elements.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_2" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_2">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_2">
							<?php
							$wppa_table = 'II';

						wppa_setting_subheader( 'A', '1', __( 'Breadcrumb related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Breadcrumb on posts', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed', 'wp-photo-album-plus'));
							$slug = 'wppa_show_bread_posts';
							$onchange = 'wppaCheckBreadcrumb()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'navi,page';
							wppa_setting($slug, '1a', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on pages', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed', 'wp-photo-album-plus'));
							$slug = 'wppa_show_bread_pages';
							$onchange = 'wppaCheckBreadcrumb()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'navi,page';
							wppa_setting($slug, '1b', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on search results', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on the search results page.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the search results.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_search';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page,search';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on topten displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on topten displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the topten displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_topten';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page';
							wppa_setting($slug, '3.0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on last ten displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on last ten displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the last ten displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_lasten';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page';
							wppa_setting($slug, '3.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on comment ten displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on comment ten displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the comment ten displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_comten';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page,comment';
							wppa_setting($slug, '3.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on tag result displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on tag result displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the tag result displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_tag';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page';
							wppa_setting($slug, '3.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on featured ten displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on featured ten displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the featured ten displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_featen';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page';
							wppa_setting($slug, '3.4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Breadcrumb on related photos displays', 'wp-photo-album-plus');
							$desc = __('Show breadcrumb navigation bars on related photos displays.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the related photos displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_on_related';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,page';
							wppa_setting($slug, '3.5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Home', 'wp-photo-album-plus');
							$desc = __('Show "Home" in breadcrumb.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether the breadcrumb navigation should start with a "Home"-link', 'wp-photo-album-plus'));
							$slug = 'wppa_show_home';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,layout';
							wppa_setting($slug, '4.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Home text', 'wp-photo-album-plus');
							$desc = __('The text to use as "Home"', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_home_text';
							$html = wppa_input($slug, '100px;');
							$clas = 'wppa_bc';
							$tags = 'navi,layout';
							wppa_setting($slug, '4.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page', 'wp-photo-album-plus');
							$desc = __('Show the page(s) in breadcrumb.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate whether the breadcrumb navigation should show the page(hierarchy)', 'wp-photo-album-plus'));
							$slug = 'wppa_show_page';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_bc';
							$tags = 'navi,layout';
							wppa_setting($slug, '4.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Separator', 'wp-photo-album-plus');
							$desc = __('Breadcrumb separator symbol.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the desired breadcrumb separator element.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('A text string may contain valid html.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('An image will be scaled automatically if you set the navigation font size.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_separator';
							$options = array('&amp;raquo', '&amp;rsaquo', '&amp;gt', '&amp;bull', __('Text (html):', 'wp-photo-album-plus'), __('Image (url):', 'wp-photo-album-plus'));
							$values = array('raquo', 'rsaquo', 'gt', 'bull', 'txt', 'url');
							$onchange = 'wppaCheckBreadcrumb()';
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = 'wppa_bc';
							$tags = 'navi,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Html', 'wp-photo-album-plus');
							$desc = __('Breadcrumb separator text.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the HTML code that produces the separator symbol you want.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It may be as simple as \'-\' (without the quotes) or as complex as a tag like <div>..</div>.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_txt';
							$html = wppa_input($slug, '90%', '300px');
							$clas = $slug;
							$tags = 'navi,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Image Url', 'wp-photo-album-plus');
							$desc = __('Full url to separator image.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the full url to the image you want to use for the separator symbol.', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_url';
							$html = wppa_input($slug, '90%', '300px');
							$clas = $slug;
							$tags = 'navi,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Pagelink position', 'wp-photo-album-plus');
							$desc = __('The location for the pagelinks bar.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_pagelink_pos';
							$options = array(__('Top', 'wp-photo-album-plus'), __('Bottom', 'wp-photo-album-plus'), __('Both', 'wp-photo-album-plus'));
							$values = array('top', 'bottom', 'both');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'navi,layout';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumblink on slideshow', 'wp-photo-album-plus');
							$desc = __('Show a thumb link on slideshow bc.', 'wp-photo-album-plus');
							$help = esc_js(__('Show a link to thumbnail display on an breadcrumb above a slideshow', 'wp-photo-album-plus'));
							$slug = 'wppa_bc_slide_thumblink';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'navi,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'B', '1', __( 'Slideshow related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Start/stop', 'wp-photo-album-plus');
							$desc = __('Show the Start/Stop slideshow bar.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the start/stop slideshow navigation bar above the full-size images and slideshow', 'wp-photo-album-plus'));
							$slug = 'wppa_show_startstop_navigation';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Browse bar', 'wp-photo-album-plus');
							$desc = __('Show Browse photos bar.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the preveous/next navigation bar under the full-size images and slideshow', 'wp-photo-album-plus'));
							$slug = 'wppa_show_browse_navigation';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Filmstrip', 'wp-photo-album-plus');
							$desc = __('Show Filmstrip navigation bar.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the filmstrip navigation bar under the full_size images and slideshow', 'wp-photo-album-plus'));
							$slug = 'wppa_filmstrip';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,navi,thumb';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Film seam', 'wp-photo-album-plus');
							$desc = __('Show seam between end and start of film.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the wrap-around point in the filmstrip', 'wp-photo-album-plus'));
							$slug = 'wppa_film_show_glue';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,navi,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo name', 'wp-photo-album-plus');
							$desc = __('Display photo name.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the name of the photo under the slideshow image.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_full_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Add (Owner)', 'wp-photo-album-plus');
							$desc = __('Add the uploaders display name in parenthesis to the name.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_full_owner';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '5.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo desc', 'wp-photo-album-plus');
							$desc = __('Display Photo description.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the description of the photo under the slideshow image.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_full_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Hide when empty', 'wp-photo-album-plus');
							$desc = __('Hide the descriptionbox when empty.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_hide_when_empty';
							$html = wppa_checkbox($slug);
							$clas = 'hide_empty';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '6.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating system', 'wp-photo-album-plus');
							$desc = __('Enable the rating system.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the photo rating system will be enabled.', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_on';
							$onchange = 'wppaCheckRating()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,rating';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comments system', 'wp-photo-album-plus');
							$desc = __('Enable the comments system.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the comments box under the fullsize images and let users enter their comments on individual photos.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_comments';
							$onchange = 'wppaCheckComments()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,comment';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment Avatar default', 'wp-photo-album-plus');
							$desc = __('Show Avatars with the comments if not --- none ---', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_gravatar';
							$onchange = 'wppaCheckGravatar()';
							$options = array(	__('--- none ---', 'wp-photo-album-plus'),
												__('mystery man', 'wp-photo-album-plus'),
												__('identicon', 'wp-photo-album-plus'),
												__('monsterid', 'wp-photo-album-plus'),
												__('wavatar', 'wp-photo-album-plus'),
												__('retro', 'wp-photo-album-plus'),
												__('--- url ---', 'wp-photo-album-plus')
											);
							$values = array(	'none',
												'mm',
												'identicon',
												'monsterid',
												'wavatar',
												'retro',
												'url'
											);
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = 'wppa_comment_';
							$tags = 'slide,comment,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment Avatar url', 'wp-photo-album-plus');
							$desc = __('Comment Avatar default url.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_gravatar_url';
							$html = wppa_input($slug, '90%', '300px');
							$clas = 'wppa_grav';
							$tags = 'slide,comment,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Big Browse Buttons', 'wp-photo-album-plus');
							$desc = __('Enable invisible browsing buttons.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the fullsize image is covered by two invisible areas that act as browse buttons.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Make sure the Full height (Table I-B2) is properly configured to prevent these areas to overlap unwanted space.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_bbb';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Ugly Browse Buttons', 'wp-photo-album-plus');
							$desc = __('Enable the ugly browsing buttons.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the fullsize image is covered by two browse buttons.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_ubb';
//							$slug2 = 'wppa_ubb_color';
							$html = wppa_checkbox($slug);
//							$opts = array( __('Black', 'wp-photo-album-plus'), __('Light gray', 'wp-photo-album-plus') );
//							$vals = array( '', 'c');
//							$html2 = wppa_select($slug2, $opts, $vals);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '13.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Start/stop icons', 'wp-photo-album-plus');
							$desc = __('Show start and stop icons at the center of the slide', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_start_stop_icons';
					//		$slug2 = 'wppa_start_stop_icons_type';
							$html = wppa_checkbox($slug);
					//		$opts = array( __('Black square', 'wp-photo-album-plus'), __('Blue square', 'wp-photo-album-plus'), __('Black round', 'wp-photo-album-plus') );
					//		$vals = array( '.jpg', 'b.jpg', 'r.png' );
					//		$html2 = wppa_select($slug2, $opts, $vals);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '13.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show custom box', 'wp-photo-album-plus');
							$desc = __('Display the custom box in the slideshow', 'wp-photo-album-plus');
							$help = esc_js(__('You can fill the custom box with any html you like. It will not be checked, so it is your own responsability to close tags properly.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The position of the box can be defined in Table IX-E.', 'wp-photo-album-plus'));
							$slug = 'wppa_custom_on';
							$onchange = 'wppaCheckCustom()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Custom content', 'wp-photo-album-plus');
							$desc = __('The content (html) of the custom box.', 'wp-photo-album-plus');
							$help = esc_js(__('You can fill the custom box with any html you like. It will not be checked, so it is your own responsability to close tags properly.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The position of the box can be defined in Table IX-E.', 'wp-photo-album-plus'));
							$slug = 'wppa_custom_content';
							$html = wppa_textarea($slug, $name);
							$clas = 'wppa_custom_';
							$tags = 'slide,layout';
							wppa_setting(false, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow/Number bar', 'wp-photo-album-plus');
							$desc = __('Display the Slideshow / Number bar.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: display the number boxes on slideshow', 'wp-photo-album-plus'));
							$slug = 'wppa_show_slideshownumbar';
							$onchange = 'wppaCheckNumbar()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,navi';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('IPTC system', 'wp-photo-album-plus');
							$desc = __('Enable the iptc system.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the iptc box under the fullsize images.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_iptc';
							$onchange = '';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('IPTC open', 'wp-photo-album-plus');
							$desc = __('Display the iptc box initially opened.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the iptc box under the fullsize images initially open.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_iptc_open';
							$onchange = '';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '17.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('EXIF system', 'wp-photo-album-plus');
							$desc = __('Enable the exif system.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the exif box under the fullsize images.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_exif';
							$onchange = '';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);

							$name = __('EXIF open', 'wp-photo-album-plus');
							$desc = __('Display the exif box initially opened.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the exif box under the fullsize images initially open.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_exif_open';
							$onchange = '';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '18.1', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'C', '1', __( 'Social media share box related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Show Share Box', 'wp-photo-album-plus');
							$desc = __('Display the share social media buttons box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_on';
							$onchange = 'wppaCheckShares()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Hide when running', 'wp-photo-album-plus');
							$desc = __('Hide the SM box when slideshow runs.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_hide_when_running';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Share Box Widget', 'wp-photo-album-plus');
							$desc = __('Display the share social media buttons box in widgets.', 'wp-photo-album-plus');
							$help = __('This setting applies to normal slideshows in widgets, not to the slideshowwidget as that is a slideonly display.', 'wp-photo-album-plus');
							$slug = 'wppa_share_on_widget';
							$onchange = 'wppaCheckShares()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'widget,sm,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Share Buttons Thumbs', 'wp-photo-album-plus');
							$desc = __('Display the share social media buttons under thumbnails.', 'wp-photo-album-plus');
							$help = '';// __('This setting applies to normal slideshows in widgets, not to the slideshowwidget as that is a slideonly display.');
							$slug = 'wppa_share_on_thumbs';
							$onchange = 'wppaCheckShares()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'thumb,sm,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Share Buttons Lightbox', 'wp-photo-album-plus');
							$desc = __('Display the share social media buttons on lightbox displays.', 'wp-photo-album-plus');
							$help = '';// __('This setting applies to normal slideshows in widgets, not to the slideshowwidget as that is a slideonly display.');
							$slug = 'wppa_share_on_lightbox';
							$onchange = 'wppaCheckShares()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'lightbox,sm,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Share Buttons Mphoto', 'wp-photo-album-plus');
							$desc = __('Display the share social media buttons on mphoto displays.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_on_mphoto';
							$onchange = 'wppaCheckShares()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'sm,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show QR Code', 'wp-photo-album-plus');
							$desc = __('Display the QR code in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_qr';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Twitter button', 'wp-photo-album-plus');
							$desc = __('Display the Twitter button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_twitter';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Google+ button', 'wp-photo-album-plus');
							$desc = __('Display the Google+ button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_google';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Pinterest button', 'wp-photo-album-plus');
							$desc = __('Display the Pintrest button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_pinterest';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show LinkedIn button', 'wp-photo-album-plus');
							$desc = __('Display the LinkedIn button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_linkedin';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Facebook share button', 'wp-photo-album-plus');
							$desc = __('Display the Facebook button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_share_facebook';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '17.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Facebook like button', 'wp-photo-album-plus');
							$desc = __('Display the Facebook button in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_facebook_like';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '17.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Display type', 'wp-photo-album-plus');
							$desc = __('Select the Facebook button display type.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_fb_display';
							$opts = array( __('Standard', 'wp-photo-album-plus'), __('Button', 'wp-photo-album-plus'), __('Button with counter', 'wp-photo-album-plus'), __('Box with counter', 'wp-photo-album-plus') );
							$vals = array( 'standard', 'button', 'button_count', 'box_count' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '17.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Facebook comment box', 'wp-photo-album-plus');
							$desc = __('Display the Facebook comment dialog box in the share box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_facebook_comments';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'slide,sm,layout';
							wppa_setting($slug, '17.4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Facebook User Id', 'wp-photo-album-plus');
							$desc = __('Enter your facebook user id to be able to moderate comments and sends', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_facebook_admin_id';
							$html = wppa_input($slug, '200px');
							$clas = 'wppa_share';
							$tags = 'system,sm';
							wppa_setting($slug, '17.7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Facebook App Id', 'wp-photo-album-plus');
							$desc = __('Enter your facebook app id to be able to moderate comments and sends', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_facebook_app_id';
							$html = wppa_input($slug, '200px');
							$clas = 'wppa_share';
							$tags = 'system,sm';
							wppa_setting($slug, '17.8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Facebook js SDK', 'wp-photo-album-plus');
							$desc = __('Load Facebook js SDK', 'wp-photo-album-plus');
							$help = esc_js(__('Uncheck this box only when there is a conflict with an other plugin that also loads the Facebook js SDK.', 'wp-photo-album-plus'));
							$slug = 'wppa_load_facebook_sdk';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'system,sm';
							wppa_setting($slug, '17.9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Share single image', 'wp-photo-album-plus');
							$desc = __('Share a link to a single image, not the slideshow.', 'wp-photo-album-plus');
							$help = esc_js(__('The sharelink points to a page with a single image rather than to the page with the photo in the slideshow.', 'wp-photo-album-plus'));
							$slug = 'wppa_share_single_image';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_share';
							$tags = 'system,sm';
							wppa_setting($slug, '99', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'D', '1', __( 'Thumbnail display related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Thumbnail name', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail name.', 'wp-photo-album-plus');
							$help = esc_js(__('Display photo name under thumbnail images.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_name';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Add (Owner)', 'wp-photo-album-plus');
							$desc = __('Add the uploaders display name in parenthesis to the name.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_thumb_text_owner';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail desc', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail description.', 'wp-photo-album-plus');
							$help = esc_js(__('Display description of the photo under thumbnail images.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_desc';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail rating', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail Rating.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the rating of the photo under the thumbnail image.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_rating';
							$html = '<span class="wppa_rating">'.wppa_checkbox($slug).'</span>';
							$clas = 'wppa_rating_ tt_normal';
							$tags = 'thumb,layout,rating';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail comcount', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail Comment count.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the number of comments to the photo under the thumbnail image.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_comcount';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout,comment';
							wppa_setting($slug, '4.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail viewcount', 'wp-photo-album-plus');
							$desc = __('Display the number of views.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the number of views under the thumbnail image.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_viewcount';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout,meta';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail virt album', 'wp-photo-album-plus');
							$desc = __('Display the real album name on virtual album display.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the album name of the photo in parenthesis under the thumbnail on virtual album displays like search results etc.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_text_virt_album';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout,meta';
							wppa_setting($slug, '5.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail video', 'wp-photo-album-plus');
							$desc = __('Show video controls on thumbnail displays.', 'wp-photo-album-plus');
							$help = __('Works on default thumbnail type only. You can play the video only when the link is set to no link at all.', 'wp-photo-album-plus');
							$slug = 'wppa_thumb_video';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout,video';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail audio', 'wp-photo-album-plus');
							$desc = __('Show audio controls on thumbnail displays.', 'wp-photo-album-plus');
							$help = __('Works on default thumbnail type only.', 'wp-photo-album-plus');
							$slug = 'wppa_thumb_audio';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout,audio';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup name', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail name on popup.', 'wp-photo-album-plus');
							$help = esc_js(__('Display photo name under thumbnail images on the popup.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_name';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,layout,meta';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup (owner)', 'wp-photo-album-plus');
							$desc = __('Display owner on popup.', 'wp-photo-album-plus');
							$help = esc_js(__('Display photo owner under thumbnail images on the popup.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_owner';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup desc', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail description on popup.', 'wp-photo-album-plus');
							$help = esc_js(__('Display description of the photo under thumbnail images on the popup.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_desc';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup desc no links', 'wp-photo-album-plus');
							$desc = __('Strip html anchor tags from descriptions on popups', 'wp-photo-album-plus');
							$help = esc_js(__('Use this option to prevent the display of links that cannot be activated.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_desc_strip';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,meta,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup rating', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail Rating on popup.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the rating of the photo under the thumbnail image on the popup.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_rating';
							$html = '<span class="wppa_rating">'.wppa_checkbox($slug).'</span>';
							$clas = 'wppa_rating_ tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,rating,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Popup comcount', 'wp-photo-album-plus');
							$desc = __('Display Thumbnail Comment count on popup.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the number of comments of the photo under the thumbnail image on the popup.', 'wp-photo-album-plus'));
							$slug = 'wppa_popup_text_ncomments';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal tt_masonry wppa_popup';
							$tags = 'thumb,comment,layout';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show rating count', 'wp-photo-album-plus');
							$desc = __('Display the number of votes along with average ratings.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the number of votes is displayed along with average rating displays on thumbnail and popup displays.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_rating_count';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_ tt_normal tt_masonry';
							$tags = 'thumb,rating,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show name on thumb area', 'wp-photo-album-plus');
							$desc = __('Select if and where to display the album name on the thumbnail display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_albname_on_thumbarea';
							$options = array(__('None', 'wp-photo-album-plus'), __('At the top', 'wp-photo-album-plus'), __('At the bottom', 'wp-photo-album-plus'));
							$values = array('none', 'top', 'bottom');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'album,meta,layout';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show desc on thumb area', 'wp-photo-album-plus');
							$desc = __('Select if and where to display the album description on the thumbnail display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_albdesc_on_thumbarea';
							$options = array(__('None', 'wp-photo-album-plus'), __('At the top', 'wp-photo-album-plus'), __('At the bottom', 'wp-photo-album-plus'));
							$values = array('none', 'top', 'bottom');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'album,meta,layout';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Edit/Delete links', 'wp-photo-album-plus');
							$desc = __('Show these links under default thumbnails for owner and admin.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_edit_thumb';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'thumb';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show empty thumbnail area', 'wp-photo-album-plus');
							$desc = __('Display thumbnail areas with upload link only for empty albums.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_empty_thumblist';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'thumb';
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'E', '1', __( 'Album cover related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Covertext', 'wp-photo-album-plus');
							$desc = __('Show the text on the album cover.', 'wp-photo-album-plus');
							$help = esc_js(__('Display the album decription on the album cover', 'wp-photo-album-plus'));
							$slug = 'wppa_show_cover_text';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,meta,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow', 'wp-photo-album-plus');
							$desc = __('Enable the slideshow.', 'wp-photo-album-plus');
							$help = esc_js(__('If you do not want slideshows: uncheck this box. Browsing full size images will remain possible.', 'wp-photo-album-plus'));
							$slug = 'wppa_enable_slideshow';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,navi,slide,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow/Browse', 'wp-photo-album-plus');
							$desc = __('Display the Slideshow / Browse photos link on album covers', 'wp-photo-album-plus');
							$help = esc_js(__('This setting causes the Slideshow link to be displayed on the album cover.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If slideshows are disabled in item 2 in this table, you will see a browse link to fullsize images.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you do not want the browse link either, uncheck this item.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_slideshowbrowselink';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,navi,slide,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('View ...', 'wp-photo-album-plus');
							$desc = __('Display the View xx albums and yy photos link on album covers', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_viewlink';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,navi,album,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Treecount', 'wp-photo-album-plus');
							$desc = __('Disaplay the total number of (sub)albums and photos in subalbums', 'wp-photo-album-plus');
							$help = esc_js(__('Displays the total number of sub albums and photos in the entire album tree in parenthesis if the numbers differ from the direct content of the album.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_treecount';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show categories', 'wp-photo-album-plus');
							$desc = __('Display the album categories on the covers.', 'wp-photo-album-plus');
							$slug = 'wppa_show_cats';
							$help = '';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,meta,album,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Skip empty albums', 'wp-photo-album-plus');
							$desc = __('Do not show empty albums, except for admin and owner.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_skip_empty_albums';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'cover,album,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'F', '1', __( 'Widget related visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Big Browse Buttons in widget', 'wp-photo-album-plus');
							$desc = __('Enable invisible browsing buttons in widget slideshows.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the fullsize image is covered by two invisible areas that act as browse buttons.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Make sure the Full height (Table I-B2) is properly configured to prevent these areas to overlap unwanted space.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_bbb_widget';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'widget,slide,layout,navi';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Ugly Browse Buttons in widget', 'wp-photo-album-plus');
							$desc = __('Enable ugly browsing buttons in widget slideshows.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the fullsize image is covered by browse buttons.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Make sure the Full height (Table I-B2) is properly configured to prevent these areas to overlap unwanted space.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_ubb_widget';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'widget,slide,layout,navi';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Album widget tooltip', 'wp-photo-album-plus');
							$desc = __('Show the album description on hoovering thumbnail in album widget', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_albwidget_tooltip';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'widget,album,layout,meta';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'G', '1', __( 'Lightbox related settings. These settings have effect only when Table IX-J3 is set to wppa' , 'wp-photo-album-plus') );
							{
/*
							$name = __('Overlay Close label text', 'wp-photo-album-plus');
							$desc = __('The text label for the cross exit symbol.', 'wp-photo-album-plus');
							$help = __('This text may be multilingual according to the qTranslate short tags specs.', 'wp-photo-album-plus');
							$slug = 'wppa_ovl_close_txt';
							$html = wppa_input($slug, '200px');
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);
*/

							$name = __('Overlay theme color', 'wp-photo-album-plus');
							$desc = __('The color of the image border and text background.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_theme';
							$options = array(__('Black', 'wp-photo-album-plus'), __('White', 'wp-photo-album-plus'));
							$values = array('black', 'white');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay background color', 'wp-photo-album-plus');
							$desc = __('The color of the outer background.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_bgcolor';
							$options = array(__('Black', 'wp-photo-album-plus'), __('White', 'wp-photo-album-plus'));
							$values = array('black', 'white');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '2.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay slide name', 'wp-photo-album-plus');
							$desc = __('Show name if from slide.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos name on a lightbox display when initiated from a slide.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting also applies to film thumbnails if Table VI-11 is set to lightbox overlay.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_slide_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,slide,meta,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay slide desc', 'wp-photo-album-plus');
							$desc = __('Show description if from slide.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from a slide.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting also applies to film thumbnails if Table VI-11 is set to lightbox overlay.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_slide_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,slide,meta,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay thumb name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from thumb.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from a standard thumbnail or a widget thumbnail.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting applies to standard thumbnails, thumbnail-, comment-, topten- and lasten-widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_thumb_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,thumb,meta,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay thumb desc', 'wp-photo-album-plus');
							$desc = __('Show description if from thumb.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from a standard thumbnail or a widget thumbnail.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting applies to standard thumbnails, thumbnail-, comment-, topten- and lasten-widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_thumb_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,thumb,meta,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay potd name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from photo of the day.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from the photo of the day.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_potd_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,widget,meta,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay potd desc', 'wp-photo-album-plus');
							$desc = __('Show description if from from photo of the day.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from the photo of the day.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_potd_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,widget,meta,layout';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay sphoto name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from a single photo.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from a single photo.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_sphoto_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay sphoto desc', 'wp-photo-album-plus');
							$desc = __('Show description if from from a single photo.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from a single photo.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_sphoto_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay mphoto name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from a single media style photo.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from a single media style photo.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_mphoto_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay mphoto desc', 'wp-photo-album-plus');
							$desc = __('Show description if from from a media style photo.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from a single media style photo.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_mphoto_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay albumwidget name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from the album widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from the album widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_alw_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,widget,layout';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay albumwidget desc', 'wp-photo-album-plus');
							$desc = __('Show description if from from the album widget.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from the album widget.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_alw_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,widget,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay coverphoto name', 'wp-photo-album-plus');
							$desc = __('Show the photos name if from the album cover.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the name on a lightbox display when initiated from the album coverphoto.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_cover_name';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,cover,album,layout';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay coverphoto desc', 'wp-photo-album-plus');
							$desc = __('Show description if from from the album cover.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the photos description on a lightbox display when initiated from the album coverphoto.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_cover_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,cover,album,layout';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay add owner', 'wp-photo-album-plus');
							$desc = __('Add the owner to the photo name on lightbox displays.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting is independant of the show name switches and is a global setting.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_add_owner';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay show start/stop', 'wp-photo-album-plus');
							$desc = __('Show Start and Stop for running slideshow on lightbox.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_show_startstop';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay show legenda', 'wp-photo-album-plus');
							$desc = __('Show "Press f for fullsize" etc. on lightbox.', 'wp-photo-album-plus');
							$help = esc_js(__('Independant of this setting, it will not show up on mobile devices.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_show_legenda';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '19', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show fullscreen icons', 'wp-photo-album-plus');
							$desc = __('Shows fullscreen and back to normal icon buttons on upper right corner', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_fs_icons';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '20', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay show counter', 'wp-photo-album-plus');
							$desc = __('Show the x/y counter below the image.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_show_counter';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '90', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Zoom in', 'wp-photo-album-plus');
							$desc = __('Display tooltip "Zoom in" along with the magnifier cursor.', 'wp-photo-album-plus');
							$help = esc_js(__('If you select ---none--- in Table I-G2 for magnifier size, the tooltop contains the photo name.', 'wp-photo-album-plus') );
							$slug = 'wppa_show_zoomin';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,meta,layout';
							wppa_setting($slug, '91', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'H', '1', __( 'Frontend upload configuration settings' , 'wp-photo-album-plus') );
							{
							$name = __('User upload Photos', 'wp-photo-album-plus');
							$desc = __('Enable frontend upload.', 'wp-photo-album-plus');
							$help = esc_js(__('If you check this item, frontend upload will be enabled according to the rules set in the following items of this table.', 'wp-photo-album-plus'));
							$slug = 'wppa_user_upload_on';
							$onchange = 'wppaFollow(\'wppa_user_upload_on\',\'wppa_feup\');';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'access,upload';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User upload Photos login', 'wp-photo-album-plus');
							$desc = __('Frontend upload requires the user is logged in.', 'wp-photo-album-plus');
							$help = esc_js(__('If you uncheck this box, make sure you check the item Owners only in Table VII-D1.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Also: set the owner to ---public--- of the albums that are allowed to be uploaded to.', 'wp-photo-album-plus'));
							$slug = 'wppa_user_upload_login';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'access,upload';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User upload Ajax', 'wp-photo-album-plus');
							$desc = __('Shows the upload progression bar.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ajax_upload';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'system,upload';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show Copyright', 'wp-photo-album-plus');
							$desc = __('Show a copyright warning on frontend upload locations.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_copyright_on';
							$onchange = 'wppaFollow(\'wppa_copyright_on\',\'wppa_up_wm\')';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_feup';
							$tags = 'upload,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Copyright notice', 'wp-photo-album-plus');
							$desc = __('The message to be displayed.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_copyright_notice';
							$html = wppa_textarea($slug, $name);
							$clas = 'wppa_feup wppa_up_wm';
							$tags = 'upload,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User Watermark', 'wp-photo-album-plus');
							$desc = __('Uploading users may select watermark settings', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, anyone who can upload and/or import photos can overrule the default watermark settings.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_user';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_watermark wppa_feup';
							$tags = 'water,upload';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User name', 'wp-photo-album-plus');
							$desc = __('Uploading users may overrule the default name.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the default photo name as defined in Table IX-D13 may be overruled by the user.', 'wp-photo-album-plus'));
							$slug = 'wppa_name_user';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'upload';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Apply Newphoto desc user', 'wp-photo-album-plus');
							$desc = __('Give each new frontend uploaded photo a standard description.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, each new photo will get the description (template) as specified in Table IX-D5.', 'wp-photo-album-plus'));
							$slug = 'wppa_apply_newphoto_desc_user';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'upload';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User desc', 'wp-photo-album-plus');
							$desc = __('Uploading users may overrule the default description.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_desc_user';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'upload';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User upload custom', 'wp-photo-album-plus');
							$desc = __('Frontend upload can fill in custom data fields.', 'wp-photo-album-plus');
							$help = esc_js('Custom datafields can be defined in Table II-J10');
							$slug = 'wppa_fe_custom_fields';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup custfields';
							$tags = 'upload';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User upload tags', 'wp-photo-album-plus');
							$desc = __('Frontend upload can add tags.', 'wp-photo-album-plus');
							$help = esc_js(__('You can configure the details of tag addition in Table IX-D18.x', 'wp-photo-album-plus'));
							$slug = 'wppa_fe_upload_tags';
							$onchange = 'wppaFollow(\'wppa_fe_upload_tags\', \'wppa_up_tags\');';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_feup';
							$tags = 'upload';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tag selection box', 'wp-photo-album-plus').' 1';
							$desc = __('Front-end upload tags selecion box.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_up_tagselbox_on_1';
							$slug2 = 'wppa_up_tagselbox_multi_1';
							$html = '<span style="float:left" >'.__('On:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug1).'<span style="float:left" >'.__('Multi:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug2);
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.1ab', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Caption box', 'wp-photo-album-plus').' 1';
							$desc = __('The title of the tag selection box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tagselbox_title_1';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.1c', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tags box', 'wp-photo-album-plus').' 1';
							$desc = __('The tags in the selection box.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the tags you want to appear in the selection box. Empty means: all existing tags', 'wp-photo-album-plus'));
							$slug = 'wppa_up_tagselbox_content_1';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.1d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tag selection box', 'wp-photo-album-plus').' 2';
							$desc = __('Front-end upload tags selecion box.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_up_tagselbox_on_2';
							$slug2 = 'wppa_up_tagselbox_multi_2';
							$html = '<span style="float:left" >'.__('On:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug1).'<span style="float:left" >'.__('Multi:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug2);
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.2ab', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Caption box', 'wp-photo-album-plus').' 2';
							$desc = __('The title of the tag selection box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tagselbox_title_2';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.2c', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tags box', 'wp-photo-album-plus').' 2';
							$desc = __('The tags in the selection box.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the tags you want to appear in the selection box. Empty means: all existing tags', 'wp-photo-album-plus'));
							$slug = 'wppa_up_tagselbox_content_2';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.2d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tag selection box', 'wp-photo-album-plus').' 3';
							$desc = __('Front-end upload tags selecion box.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_up_tagselbox_on_3';
							$slug2 = 'wppa_up_tagselbox_multi_3';
							$html = '<span style="float:left" >'.__('On:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug1).'<span style="float:left" >'.__('Multi:', 'wp-photo-album-plus').'</span>'.wppa_checkbox($slug2);
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.3ab', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Caption box', 'wp-photo-album-plus').' 3';
							$desc = __('The title of the tag selection box.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tagselbox_title_3';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.3c', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tags box', 'wp-photo-album-plus').' 3';
							$desc = __('The tags in the selection box.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the tags you want to appear in the selection box. Empty means: all existing tags', 'wp-photo-album-plus'));
							$slug = 'wppa_up_tagselbox_content_3';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '11.3d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('New tags', 'wp-photo-album-plus');
							$desc = __('Input field for any user defined tags.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tag_input_on';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('New tags caption', 'wp-photo-album-plus');
							$desc = __('The caption above the tags input field.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tag_input_title';
							$html = wppa_edit( $slug, get_option( $slug ), '300px' );
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Preview tags', 'wp-photo-album-plus');
							$desc = __('Show a preview of all tags that will be added to the photo info.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_up_tag_preview';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup wppa_up_tags';
							$tags = 'upload';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Camera connect', 'wp-photo-album-plus');
							$desc = __('Connect frontend upload to camara on mobile devices with camera', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_camera_connect';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_feup';
							$tags = 'upload';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'J', '1', __( 'Miscellaneous visibility settings' , 'wp-photo-album-plus') );
							{
							$name = __('Frontend ending label', 'wp-photo-album-plus');
							$desc = __('Frontend upload / create / edit dialog closing label text.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_close_text';
							$opts = array( __('Abort', 'wp-photo-album-plus'), __('Cancel', 'wp-photo-album-plus'), __('Close', 'wp-photo-album-plus'), __('Exit', 'wp-photo-album-plus'), __('Quit', 'wp-photo-album-plus') );
							$vals = array( 'Abort', 'Cancel', 'Close', 'Exit', 'Quit' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);


							$name = __('Widget thumbs fontsize', 'wp-photo-album-plus');
							$desc = __('Font size for thumbnail subtext in widgets.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_fontsize_widget_thumb';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'thumb,widget,size,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Arrow color', 'wp-photo-album-plus');
							$desc = __('Left/right browsing arrow color.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the color of the filmstrip navigation arrows.', 'wp-photo-album-plus'));
							$slug = 'wppa_arrow_color';
							$html = wppa_input($slug, '70px', '', '');
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Owner on new line', 'wp-photo-album-plus');
							$desc = __('Place the (owner) text on a new line.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_owner_on_new_line';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'layout,meta';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Custom datafields', 'wp-photo-album-plus');
							$desc = __('Define up to 10 custom data fields for photos.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_custom_fields';
							$onch = 'wppaCheckCheck(\'wppa_custom_fields\', \'custfields\' )';
							$html = wppa_checkbox($slug, $onch);
							$clas = '';
							$tags = 'meta';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

								for ( $i = '0'; $i < '10'; $i++ ) {
									$name = sprintf(__('Name and visibility %s', 'wp-photo-album-plus'), $i);
									$desc = sprintf(__('The caption for field %s and visibility at frontend.', 'wp-photo-album-plus'), $i);
									$help = esc_js(sprintf(__('If you check the box, the value of this field is displayable in photo descriptions at the frontend with keyword w#c%s', 'wp-photo-album-plus'), $i));
									$slug1 = 'wppa_custom_caption_'.$i;
									$html1 = wppa_input($slug1, '300px');
									$slug2 = 'wppa_custom_visible_'.$i;
									$html2 = wppa_checkbox($slug2);
									$clas = 'custfields';
									$tags = 'meta';
									wppa_setting(array($slug1,$slug2), '10.'.$i.'a,b', $name, $desc, $html1.$html2, $help, $clas, $tags);
								}
							}
						wppa_setting_subheader( 'K', '1', __( 'Navigation symbols for slideshows and lighbox' , 'wp-photo-album-plus') );
							{
							$name = __('Start symbol url', 'wp-photo-album-plus');
							$desc = __('Supply the url of an image for the start symbol', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_start_symbol_url';
							$html = wppa_input($slug, '350px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Pause symbol url', 'wp-photo-album-plus');
							$desc = __('Supply the url of an image for the pause symbol', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_pause_symbol_url';
							$html = wppa_input($slug, '350px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Stop symbol url', 'wp-photo-album-plus');
							$desc = __('Supply the url of an image for the stop symbol', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_stop_symbol_url';
							$html = wppa_input($slug, '350px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Left (prev) symbol url', 'wp-photo-album-plus');
							$desc = __('Supply the url of an image for the left symbol, if not default', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_left_symbol_url';
							$html = wppa_input($slug, '350px', '', '', '', wppa_get_imgdir( 'prev-'.wppa_opt('ovl_theme').'.gif', true ));
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Right (next) symbol url', 'wp-photo-album-plus');
							$desc = __('Supply the url of an image for the right symbol, if not default', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_left_symbol_url';
							$html = wppa_input($slug, '350px', '', '', '', wppa_get_imgdir( 'next-'.wppa_opt('ovl_theme').'.gif', true ));
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Start/pause symbol size', 'wp-photo-album-plus');
							$desc = __('The size of the start/pause symbols.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_start_pause_symbol_size';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Start/pause symbol border radius', 'wp-photo-album-plus');
							$desc = __('The border radius if the symbol is round', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_start_pause_symbol_bradius';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Stop symbol size', 'wp-photo-album-plus');
							$desc = __('The size of the stop symbols.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_stop_symbol_size';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Stop symbol border radius', 'wp-photo-album-plus');
							$desc = __('The border radius if the symbol is round', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_stop_symbol_bradius';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Left/right symbol size', 'wp-photo-album-plus');
							$desc = __('The size of the stop symbols.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_left_right_symbol_size';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Left/right symbol border radius', 'wp-photo-album-plus');
							$desc = __('The border radius', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_left_right_symbol_bradius';
							$html = wppa_input($slug, '30px');
							$clas = '';
							$tags = 'slide,lightbox,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							}

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_2">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 3: Backgrounds ?>
			<?php wppa_settings_box_header(
				'3',
				__('Table III:', 'wp-photo-album-plus').' '.__('Backgrounds:', 'wp-photo-album-plus').' '.
				__('This table describes the backgrounds of wppa+ elements.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_3" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_3">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Background color', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Sample', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Border color', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Sample', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_3">
							<?php
							$wppa_table = 'III';

						wppa_setting_subheader( 'A', '4', __('Slideshow elements backgrounds' , 'wp-photo-album-plus') );
							{
							$name = __('Nav', 'wp-photo-album-plus');
							$desc = __('Navigation bars.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for navigation backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_nav';
							$slug2 = 'wppa_bcolor_nav';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('SlideImg', 'wp-photo-album-plus');
							$desc = __('Fullsize Slideshow Photos.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for fullsize photo backgrounds and borders.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The colors may be equal or "transparent"', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('For more information about slideshow image borders see the help on Table I-B4', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_fullimg';
							$slug2 = 'wppa_bcolor_fullimg';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Numbar', 'wp-photo-album-plus');
							$desc = __('Number bar box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for numbar box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_numbar';
							$slug2 = 'wppa_bcolor_numbar';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = 'wppa_numbar';
							$tags = 'slide,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Numbar active', 'wp-photo-album-plus');
							$desc = __('Number bar active box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for numbar active box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_numbar_active';
							$slug2 = 'wppa_bcolor_numbar_active';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = 'wppa_numbar';
							$tags = 'slide,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Name/desc', 'wp-photo-album-plus');
							$desc = __('Name and Description bars.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for name and description box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_namedesc';
							$slug2 = 'wppa_bcolor_namedesc';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comments', 'wp-photo-album-plus');
							$desc = __('Comment input and display areas.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for comment box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_com';
							$slug2 = 'wppa_bcolor_com';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$clas = 'wppa_comment_';
							$tags = 'slide,comment,layout';
							$html = array($html1, $html2);
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Custom', 'wp-photo-album-plus');
							$desc = __('Custom box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for custom box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_cus';
							$slug2 = 'wppa_bcolor_cus';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('IPTC', 'wp-photo-album-plus');
							$desc = __('IPTC display box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for iptc box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_iptc';
							$slug2 = 'wppa_bcolor_iptc';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('EXIF', 'wp-photo-album-plus');
							$desc = __('EXIF display box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for exif box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_exif';
							$slug2 = 'wppa_bcolor_exif';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,meta,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Share', 'wp-photo-album-plus');
							$desc = __('Share box display background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for share box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_share';
							$slug2 = 'wppa_bcolor_share';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'B', '4', __('Other backgrounds' , 'wp-photo-album-plus') );
							{
							$name = __('Even', 'wp-photo-album-plus');
							$desc = __('Even background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for even numbered backgrounds and borders of album covers and thumbnail displays \'As covers\'.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_even';
							$slug2 = 'wppa_bcolor_even';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,album,cover,thumb';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Odd', 'wp-photo-album-plus');
							$desc = __('Odd background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for odd numbered backgrounds and borders of album covers and thumbnail displays \'As covers\'.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_alt';
							$slug2 = 'wppa_bcolor_alt';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,album,cover,thumb';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail padding', 'wp-photo-album-plus');
							$desc = __('Thumbnail padding color if thumbnail aspect is a padded setting.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS color hexadecimal like #000000 for black or #ffffff for white for the padded thumbnails.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_thumbnail';
							$slug2 = '';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = '</td><td>';//wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,thumb';
							wppa_setting($slug, '3.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Img', 'wp-photo-album-plus');
							$desc = __('Cover Photos and popups.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for Cover photo and popup backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_img';
							$slug2 = 'wppa_bcolor_img';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,cover,album';
							wppa_setting($slug, '3.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload', 'wp-photo-album-plus');
							$desc = __('Upload box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for upload box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_upload';
							$slug2 = 'wppa_bcolor_upload';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,upload';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Multitag', 'wp-photo-album-plus');
							$desc = __('Multitag box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for multitag box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_multitag';
							$slug2 = 'wppa_bcolor_multitag';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,search';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tagcloud', 'wp-photo-album-plus');
							$desc = __('Tagcloud box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for tagcloud box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_tagcloud';
							$slug2 = 'wppa_bcolor_tagcloud';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,search';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Superview', 'wp-photo-album-plus');
							$desc = __('Superview box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for superview box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_superview';
							$slug2 = 'wppa_bcolor_superview';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,search';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Search', 'wp-photo-album-plus');
							$desc = __('Search box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for search box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_search';
							$slug2 = 'wppa_bcolor_search';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,search';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('BestOf', 'wp-photo-album-plus');
							$desc = __('BestOf box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for bestof box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_bestof';
							$slug2 = 'wppa_bcolor_bestof';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout,search';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Calendar', 'wp-photo-album-plus');
							$desc = __('Calendar box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for calendar box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_calendar';
							$slug2 = 'wppa_bcolor_calendar';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Stereo', 'wp-photo-album-plus');
							$desc = __('Stereo mode selection box background.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter valid CSS colors for stereo mode selection box backgrounds and borders.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_bgcolor_stereo';
							$slug2 = 'wppa_bcolor_stereo';
							$slug = array($slug1, $slug2);
							$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
							$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
							$html = array($html1, $html2);
							$clas = '';
							$tags = 'layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							}
							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_3">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Background color', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Sample', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Border color', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Sample', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 4: Behaviour ?>
			<?php wppa_settings_box_header(
				'4',
				__('Table IV:', 'wp-photo-album-plus').' '.__('Behaviour:', 'wp-photo-album-plus').' '.
				__('This table describes the dynamic behaviour of certain wppa+ elements.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_4" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_4">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_4">
							<?php
							$wppa_table = 'IV';

						wppa_setting_subheader( 'A', '1', __( 'System related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Use Ajax', 'wp-photo-album-plus');
							$desc = __('Use Ajax as much as is possible and implemented.', 'wp-photo-album-plus');
							$help = esc_js(__('If this box is ticked, page content updates from within wppa+ displays will be Ajax based as much as possible.', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_ajax';
							$onchange = 'wppaCheckAjax()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Ajax NON Admin', 'wp-photo-album-plus');
							$desc = __('Frontend ajax use no admin files.', 'wp-photo-album-plus');
							$help = esc_js(__('If you want to password protect wp-admin, check this box.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('In rare cases changing page content does not work when this box is checked. Verify the functionality!', 'wp-photo-album-plus'));
							$slug = 'wppa_ajax_non_admin';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo names in urls', 'wp-photo-album-plus');
							$desc = __('Display photo names in urls.', 'wp-photo-album-plus');
							$help = esc_js(__('Urls to wppa+ displays will contain photonames in stead of numbers.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It is your responsability to avoid duplicate names of photos in the same album.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_photo_names_in_urls';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,system,meta';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Album names in urls', 'wp-photo-album-plus');
							$desc = __('Display album names in urls.', 'wp-photo-album-plus');
							$help = esc_js(__('Urls to wppa+ displays will contain albumnames in stead of numbers.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It is your responsability to avoid duplicate names of albums in the system.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_album_names_in_urls';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,system,meta';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use short query args', 'wp-photo-album-plus');
							$desc = __('Use &album=... &photo=...', 'wp-photo-album-plus');
							$help = esc_js(__('Urls to wppa+ displays will contain &album=... &photo=... in stead of &wppa-album=... &wppa-photo=...', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Use this setting only when there are no conflicts with other plugins that may interprete arguments like &album= etc.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_short_qargs';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,system';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable pretty links', 'wp-photo-album-plus');
							$desc = __('Enable the generation and understanding of pretty links.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, links to social media and the qr code will have "/token1/token2/" etc in stead of "&arg1=..&arg2=.." etc.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('These types of links will be interpreted and cause a redirection on entering.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It is recommended to check this box. It shortens links dramatically and simplifies qr codes.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('However, you may encounter conflicts with themes and/or other plugins, so test it troughly!', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Table IV-A2 (Photo names in urls) must be UNchecked for this setting to work!', 'wp-photo-album-plus'));
							$slug = 'wppa_use_pretty_links';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,system';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable encrypted links', 'wp-photo-album-plus');
							$desc = __('Encrypt album and photo ids in links.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_use_encrypted_links';
							$onch = 'alert(\''.__('The page will be reloaded.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
							$html = wppa_checkbox($slug, $onch);
							$clas = '';
							$tags = 'link,system';
							wppa_setting($slug, '6.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Refuse unencrypted', 'wp-photo-album-plus');
							$desc = __('When encrypted is enabled, refuse unencrypted urls.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_refuse_unencrypted';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,system';
							wppa_setting($slug, '6.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Update addressline', 'wp-photo-album-plus');
							$desc = __('Update the addressline after an ajax action or next slide.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, refreshing the page will show the current content and the browsers back and forth arrows will browse the history on the page.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If unchecked, refreshing the page will re-display the content of the original page.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This will only work on browsers that support history.pushState() and therefor NOT in IE', 'wp-photo-album-plus'));
							$warning = esc_js(__('Switching this off will affect the browsers behaviour.', 'wp-photo-album-plus'));
							$slug = 'wppa_update_addressline';
							$html = wppa_checkbox_warn_off($slug, '', '', $warning);
							$clas = '';
							$tags = 'link,system';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Render shortcode always', 'wp-photo-album-plus');
							$desc = __('This will skip the check on proper initialisation.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting is required for certain themes like Gantry to prevent the display of wppa placeholders like [WPPA+ Photo display].', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If this check is needed, you can use shortcodes like [wppa ...] only, not scripts like %%wppa%%.', 'wp-photo-album-plus'));
							$slug = 'wppa_render_shortcode_always';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Track viewcounts', 'wp-photo-album-plus');
							$desc = __('Register number of views of albums and photos.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_track_viewcounts';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto page', 'wp-photo-album-plus');
							$desc = __('Create a wp page for every fullsize image.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_auto_page';
							$onchange = 'wppaCheckAutoPage()';
							$warn = esc_js(__('Please reload this page after changing!', 'wp-photo-album-plus'));
							$html = wppa_checkbox_warn($slug, $onchange, '', $warn);
							$clas = '';
							$tags = 'page,system';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto page display', 'wp-photo-album-plus');
							$desc = __('The type of display on the autopage pages.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_auto_page_type';
							$opts = array(__('Single photo', 'wp-photo-album-plus'), __('Media type photo', 'wp-photo-album-plus'), __('In the style of a slideshow', 'wp-photo-album-plus') );
							$vals = array('photo', 'mphoto', 'slphoto');
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'autopage';
							$tags = 'page,system,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto page links', 'wp-photo-album-plus');
							$desc = __('The location for the pagelinks.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_auto_page_links';
							$opts = array(__('none', 'wp-photo-album-plus'), __('At the top', 'wp-photo-album-plus'), __('At the bottom', 'wp-photo-album-plus'), __('At top and bottom', 'wp-photo-album-plus'));
							$vals = array('none', 'top', 'bottom', 'both');
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'autopage';
							$tags = 'page,system,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Defer javascript', 'wp-photo-album-plus');
							$desc = __('Put javascript near the end of the page.', 'wp-photo-album-plus');
							$help = esc_js(__('If checkd: May fix layout problems and broken slideshows. May speed up or slow down page appearing.', 'wp-photo-album-plus'));
							$slug = 'wppa_defer_javascript';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Inline styles', 'wp-photo-album-plus');
							$desc = __('Set style specifications inline.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: May fix layout problems, but slows down page appearing.', 'wp-photo-album-plus'));
							$slug = 'wppa_inline_css';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Custom style', 'wp-photo-album-plus');
							$desc = __('Enter custom style specs here.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_custom_style';
							$html = wppa_textarea($slug, $name);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use customized style file', 'wp-photo-album-plus');
							$desc = __('This feature is highly discouraged.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_use_custom_style_file';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use customized theme file', 'wp-photo-album-plus');
							$desc = __('This feature is highly discouraged.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_use_custom_theme_file';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable photo html access', 'wp-photo-album-plus');
							$desc = __('Creates .htaccess files in .../uploads/wppa/ and .../uploads/wppa/thumbs/', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_cre_uploads_htaccess';
							$opts = array(	__('create \'all access\' .htaccess files', 'wp-photo-album-plus'),
											__('remove .htaccess files', 'wp-photo-album-plus'),
											__('create \'no hotlinking\' .htaccess files', 'wp-photo-album-plus'),
											__('do not change existing .htaccess file(s)', 'wp-photo-album-plus'),
											);
							$vals = array(	'grant',
											'remove',
											'nohot',
											'custom',
											);
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'system,access';
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Lazy or HTML comp', 'wp-photo-album-plus');
							$desc = __('Tick this box when you use lazy load or html compression.', 'wp-photo-album-plus');
							$help = esc_js(__('If the filmstrip images do not show up and you have a lazy load or html optimizing plugin active: Check this box', 'wp-photo-album-plus'));
							$slug = 'wppa_lazy_or_htmlcomp';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '19', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbs first', 'wp-photo-album-plus');
							$desc = __('When displaying album content: thumbnails before subalbums.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_thumbs_first';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,layout';
							wppa_setting($slug, '20', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Login links', 'wp-photo-album-plus');
							$desc = __('You must login to... links to login page.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_login_links';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '21', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable Video', 'wp-photo-album-plus');
							$desc = __('Enables video support.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_enable_video';
							$onchange = 'wppaCheckCheck( \''.$slug.'\', \'wppa-video\' )';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '22', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable Audio', 'wp-photo-album-plus');
							$desc = __('Enables audio support.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_enable_audio';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,audio';
							wppa_setting($slug, '23', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable 3D Stereo', 'wp-photo-album-plus');
							$desc = __('Enables 3D stereo photo support.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_enable_stereo';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,audio';
							wppa_setting($slug, '24', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Relative urls', 'wp-photo-album-plus');
							$desc = __('Use relative urls only.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_relative_urls';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '25', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Capitalize tags and cats', 'wp-photo-album-plus');
							$desc = __('Format tags and cats to start with one capital character', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_capitalize_tags';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '26', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable Admins Choice', 'wp-photo-album-plus');
							$desc = __('Enable the creation of zipfiles with selected photos.', 'wp-photo-album-plus');
							$help = esc_js(__('Activate the Admins Choice widget to make the zipfiles downloadable.', 'wp-photo-album-plus'));
							$slug = 'wppa_enable_admins_choice';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '27', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'B', '1', __( 'Slideshow related settings' , 'wp-photo-album-plus') );
							{
							$name = __('V align', 'wp-photo-album-plus');
							$desc = __('Vertical alignment of slideshow images.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the vertical alignment of slideshow images.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you select --- none ---, the photos will not be centered horizontally either.', 'wp-photo-album-plus'));
							$slug = 'wppa_fullvalign';
							$options = array(__('--- none ---', 'wp-photo-album-plus'), __('top', 'wp-photo-album-plus'), __('center', 'wp-photo-album-plus'), __('bottom', 'wp-photo-album-plus'), __('fit', 'wp-photo-album-plus'));
							$values = array('default', 'top', 'center', 'bottom', 'fit');
							$onchange = 'wppaCheckFullHalign()';
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('H align', 'wp-photo-album-plus');
							$desc = __('Horizontal alignment of slideshow images.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the horizontal alignment of slideshow images. If you specify --- none --- , no horizontal alignment will take place.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This setting is only usefull when the Column Width differs from the Maximum Width.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('(Settings I-A1 and I-B1)', 'wp-photo-album-plus'));
							$slug = 'wppa_fullhalign';
							$options = array(__('--- none ---', 'wp-photo-album-plus'), __('left', 'wp-photo-album-plus'), __('center', 'wp-photo-album-plus'), __('right', 'wp-photo-album-plus'));
							$values = array('default', 'left', 'center', 'right');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_ha';
							$tags = 'slide,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Start', 'wp-photo-album-plus');
							$desc = __('Start slideshow running.', 'wp-photo-album-plus');
							$help = esc_js(__('If you select "running", the slideshow will start running immediately, if you select "still at first photo", the first photo will be displayed in browse mode.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you select "still at first norated", the first photo that the visitor did not gave a rating will be displayed in browse mode.', 'wp-photo-album-plus'));
							$slug = 'wppa_start_slide';
							$options = array(	__('running', 'wp-photo-album-plus'),
												__('still at first photo', 'wp-photo-album-plus'),
												__('still at first norated', 'wp-photo-album-plus')
											);
							$values = array(	'run',
												'still',
												'norate'
											);
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_ss';
							$tags = 'slide';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Start slideonly', 'wp-photo-album-plus');
							$desc = __('Start slideonly slideshow running.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_start_slideonly';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide';
							wppa_setting($slug, '3.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Video autostart', 'wp-photo-album-plus');
							$desc = __('Autoplay videos in slideshows.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_start_slide_video';
							$onchange = 'wppaCheckSlideVideoControls()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa-video';
							$tags = 'slide,video';
							wppa_setting($slug, '3.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Audio autostart', 'wp-photo-album-plus');
							$desc = __('Autoplay audios in slideshows.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_start_slide_audio';
							$html = wppa_checkbox($slug);
							$clas = 'wppa-audio';
							$tags = 'slide,audio';
							wppa_setting($slug, '3.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Animation type', 'wp-photo-album-plus');
							$desc = __('The way successive slides appear.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the way the old slide is to be replaced by the new one in the slideshow/browse fullsize display.', 'wp-photo-album-plus'));
							$slug = 'wppa_animation_type';
							$options = array(	__('Fade out and in simultaneous', 'wp-photo-album-plus'),
												__('Fade in after fade out', 'wp-photo-album-plus'),
												__('Shift adjacent', 'wp-photo-album-plus'),
												__('Stack on', 'wp-photo-album-plus'),
												__('Stack off', 'wp-photo-album-plus'),
												__('Turn over', 'wp-photo-album-plus')
											);
							$values = array(	'fadeover',
												'fadeafter',
												'swipe',
												'stackon',
												'stackoff',
												'turnover'
										);
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'slide';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Timeout', 'wp-photo-album-plus');
							$desc = __('Slideshow timeout.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the time a single slide will be visible when the slideshow is started.', 'wp-photo-album-plus'));
							$slug = 'wppa_slideshow_timeout';
							$options = array( '1 s.', '1.5 s.', '2.5 s.', '4 s.', '6 s.', '8 s.', '10 s.', '12 s.', '15 s.' );
							$values = array('1000', '1500', '2500', '4000', '6000', '8000', '10000', '12000', '15000' );
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_ss';
							$tags = 'slide';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Speed', 'wp-photo-album-plus');
							$desc = __('Slideshow animation speed.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the animation speed to be used in slideshows.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This is the time it takes a photo to fade in or out.', 'wp-photo-album-plus'));
							$slug = 'wppa_animation_speed';
							$options = array(__('--- off ---', 'wp-photo-album-plus'), __('very fast (200 ms.)', 'wp-photo-album-plus'), __('fast (400 ms.)', 'wp-photo-album-plus'), __('normal (800 ms.)', 'wp-photo-album-plus'),  __('slow (1.2 s.)', 'wp-photo-album-plus'), __('very slow (2 s.)', 'wp-photo-album-plus'), __('extremely slow (4 s.)', 'wp-photo-album-plus'));
							$values = array('10', '200', '400', '800', '1200', '2000', '4000');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_ss';
							$tags = 'slide';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slide hover pause', 'wp-photo-album-plus');
							$desc = __('Running Slideshow suspends during mouse hover.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_slide_pause';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow wrap around', 'wp-photo-album-plus');
							$desc = __('The slideshow wraps around the start and end', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_slide_wrap';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Full desc align', 'wp-photo-album-plus');
							$desc = __('The alignment of the descriptions under fullsize images and slideshows.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_fulldesc_align';
							$options = array(__('Left', 'wp-photo-album-plus'), __('Center', 'wp-photo-album-plus'), __('Right', 'wp-photo-album-plus'));
							$values = array('left', 'center', 'right');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'slide,layout,meta';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remove redundant space', 'wp-photo-album-plus');
							$desc = __('Removes unwanted &lt;p> and &lt;br> tags in fullsize descriptions.', 'wp-photo-album-plus');
							$help = __('This setting has only effect when Table IX-A7 (foreign shortcodes) is checked.', 'wp-photo-album-plus');
							$slug = 'wppa_clean_pbr';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,layout,meta';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Run wpautop on description', 'wp-photo-album-plus');
							$desc = __('Adds &lt;p> and &lt;br> tags in fullsize descriptions.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_run_wpautop_on_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,layout,meta';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto open comments', 'wp-photo-album-plus');
							$desc = __('Automatic opens comments box when slideshow does not run.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_auto_open_comments';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,comment,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Film hover goto', 'wp-photo-album-plus');
							$desc = __('Go to slide when hovering filmstrip thumbnail.', 'wp-photo-album-plus');
							$help = __('Do not use this setting when slides have different aspect ratios!', 'wp-photo-album-plus');
							$slug = 'wppa_film_hover_goto';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,layout';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slide swipe', 'wp-photo-album-plus');
							$desc = __('Enable touch events swipe left-right on slides on touch screens.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_slide_swipe';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,system';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slide page Ajax', 'wp-photo-album-plus');
							$desc = __('Pagelinks slideshow use Ajax', 'wp-photo-album-plus');
							$help = __('On some systems you need to disable ajax here.', 'wp-photo-album-plus');
							$slug = 'wppa_slideshow_page_allow_ajax';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'slide,system';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'C', '1', __( 'Thumbnail related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Photo order', 'wp-photo-album-plus');
							$desc = __('Photo ordering sequence method.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the way the photos should be ordered. This is the default setting. You can overrule the default sorting order on a per album basis.', 'wp-photo-album-plus'));
							$slug = 'wppa_list_photos_by';
							$options = array(	__('--- none ---', 'wp-photo-album-plus'),
												__('Order #', 'wp-photo-album-plus'),
												__('Name', 'wp-photo-album-plus'),
												__('Random', 'wp-photo-album-plus'),
												__('Rating mean value', 'wp-photo-album-plus'),
												__('Number of votes', 'wp-photo-album-plus'),
												__('Timestamp', 'wp-photo-album-plus'),
												__('EXIF Date', 'wp-photo-album-plus'),
												__('Order # desc', 'wp-photo-album-plus'),
												__('Name desc', 'wp-photo-album-plus'),
												__('Rating mean value desc', 'wp-photo-album-plus'),
												__('Number of votes desc', 'wp-photo-album-plus'),
												__('Timestamp desc', 'wp-photo-album-plus'),
												__('EXIF Date desc', 'wp-photo-album-plus')
												);
							$values = array(	'0',
												'1',
												'2',
												'3',
												'4',
												'6',
												'5',
												'7',
												'-1',
												'-2',
												'-4',
												'-6',
												'-5',
												'-7'
												);
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'thumb,system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnail type', 'wp-photo-album-plus');
							$desc = __('The way the thumbnail images are displayed.', 'wp-photo-album-plus');
							$help = esc_js(__('You may select an altenative display method for thumbnails. Note that some of the thumbnail settings do not apply to all available display methods.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbtype';
							$options = array(__('--- default ---', 'wp-photo-album-plus'), __('like album covers', 'wp-photo-album-plus'), __('like album covers mcr', 'wp-photo-album-plus'), __('masonry style columns', 'wp-photo-album-plus'),  __('masonry style rows', 'wp-photo-album-plus'));
							$values = array('default', 'ascovers', 'ascovers-mcr', 'masonry-v', 'masonry-h' );
							$onchange = 'wppaCheckThumbType()';
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = '';
							$tags = 'thumb,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Placement', 'wp-photo-album-plus');
							$desc = __('Thumbnail image left or right.', 'wp-photo-album-plus');
							$help = esc_js(__('Indicate the placement position of the thumbnailphoto you wish.', 'wp-photo-album-plus'));
							$slug = 'wppa_thumbphoto_left';
							$options = array(__('Left', 'wp-photo-album-plus'), __('Right', 'wp-photo-album-plus'));
							$values = array('yes', 'no');
							$html = wppa_select($slug, $options, $values);
							$clas = 'tt_ascovers';
							$tags = 'thumb,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Vertical alignment', 'wp-photo-album-plus');
							$desc = __('Vertical alignment of thumbnails.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the vertical alignment of thumbnail images. Use this setting when albums contain both portrait and landscape photos.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It is NOT recommended to use the value --- default ---; it will affect the horizontal alignment also and is meant to be used with custom css.', 'wp-photo-album-plus'));
							$slug = 'wppa_valign';
							$options = array( __('--- default ---', 'wp-photo-album-plus'), __('top', 'wp-photo-album-plus'), __('center', 'wp-photo-album-plus'), __('bottom', 'wp-photo-album-plus'));
							$values = array('default', 'top', 'center', 'bottom');
							$html = wppa_select($slug, $options, $values);
							$clas = 'tt_normal';
							$tags = 'thumb,layout';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumb mouseover', 'wp-photo-album-plus');
							$desc = __('Apply thumbnail mouseover effect.', 'wp-photo-album-plus');
							$help = esc_js(__('Check this box to use mouseover effect on thumbnail images.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_thumb_opacity';
							$onchange = 'wppaCheckUseThumbOpacity()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'tt_normal tt_masonry';
							$tags = 'thumb';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumb opacity', 'wp-photo-album-plus');
							$desc = __('Initial opacity value.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wp-photo-album-plus'));
							$slug = 'wppa_thumb_opacity';
							$html = '<span class="thumb_opacity_html">'.wppa_input($slug, '50px', '', __('%', 'wp-photo-album-plus')).'</span>';
							$clas = 'tt_normal tt_masonry';
							$tags = 'thumb';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumb popup', 'wp-photo-album-plus');
							$desc = __('Use popup effect on thumbnail images.', 'wp-photo-album-plus');
							$help = esc_js(__('Thumbnails pop-up to a larger image when hovered.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_thumb_popup';
							$onchange = 'wppaCheckPopup()';
							$html = wppa_checkbox($slug, $onchange) . wppa_htmlerr('popup-lightbox');
							$clas = 'tt_normal tt_masonry';
							$tags = 'thumb';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Align subtext', 'wp-photo-album-plus');
							$desc = __('Set thumbnail subtext on equal height.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_align_thumbtext';
							$html = wppa_checkbox($slug);
							$clas = 'tt_normal';
							$tags = 'thumb,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'D', '1', __( 'Album and covers related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Album order', 'wp-photo-album-plus');
							$desc = __('Album ordering sequence method.', 'wp-photo-album-plus');
							$help = esc_js(__('Specify the way the albums should be ordered.', 'wp-photo-album-plus'));
							$slug = 'wppa_list_albums_by';
							$options = array(	__('--- none ---', 'wp-photo-album-plus'),
												__('Order #', 'wp-photo-album-plus'),
												__('Name', 'wp-photo-album-plus'),
												__('Random', 'wp-photo-album-plus'),
												__('Timestamp', 'wp-photo-album-plus'),
												__('Order # desc', 'wp-photo-album-plus'),
												__('Name desc', 'wp-photo-album-plus'),
												__('Timestamp desc', 'wp-photo-album-plus'),
												);
							$values = array(	'0',
												'1',
												'2',
												'3',
												'5',
												'-1',
												'-2',
												'-5'
												);
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'album,system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default coverphoto selection', 'wp-photo-album-plus');
							$desc = __('Default select cover photo method.', 'wp-photo-album-plus');
							$help = esc_js(__('This is the initial value on album creation only. It can be overruled on the edit album page.', 'wp-photo-album-plus'));
							$opts = array(__('Random from album', 'wp-photo-album-plus'), __('Random featured from album', 'wp-photo-album-plus'), __('Most recently added to album', 'wp-photo-album-plus'), __('Random from album or any sub album', 'wp-photo-album-plus') );
							$vals = array('0', '-1', '-2', '-3');
							$slug = 'wppa_main_photo';
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'album,cover';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Placement', 'wp-photo-album-plus');
							$desc = __('Cover image position.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the position that you want to be used for the default album cover selected in Table IV-D6.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('For covertype Image Factory: left will be treated as top and right will be treted as bottom.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('For covertype Long Descriptions: top will be treated as left and bottom will be treted as right.', 'wp-photo-album-plus'));
							$slug = 'wppa_coverphoto_pos';
							$options = array(__('Left', 'wp-photo-album-plus'), __('Right', 'wp-photo-album-plus'), __('Top', 'wp-photo-album-plus'), __('Bottom', 'wp-photo-album-plus'));
							$values = array('left', 'right', 'top', 'bottom');
							$onchange = 'wppaCheckCoverType()';
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = '';
							$tags = 'album,cover,layout';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Cover mouseover', 'wp-photo-album-plus');
							$desc = __('Apply coverphoto mouseover effect.', 'wp-photo-album-plus');
							$help = esc_js(__('Check this box to use mouseover effect on cover images.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_cover_opacity';
							$onchange = 'wppaCheckUseCoverOpacity()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'cover,thumb';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Cover opacity', 'wp-photo-album-plus');
							$desc = __('Initial opacity value.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wp-photo-album-plus'));
							$slug = 'wppa_cover_opacity';
							$html = '<span class="cover_opacity_html">'.wppa_input($slug, '50px', '', __('%', 'wp-photo-album-plus')).'</span>';
							$clas = '';
							$tags = 'cover,thumb';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Cover type', 'wp-photo-album-plus');
							$desc = __('Select the default cover type.', 'wp-photo-album-plus');
							$help = esc_js(__('Types with the addition mcr are suitable for Multi Column in a Responsive theme', 'wp-photo-album-plus'));;
							$slug = 'wppa_cover_type';
							$options = array(	__('Standard', 'wp-photo-album-plus'),
												__('Long Descriptions', 'wp-photo-album-plus'),
												__('Image Factory', 'wp-photo-album-plus'),
												__('Standard mcr', 'wp-photo-album-plus'),
												__('Long Descriptions mcr', 'wp-photo-album-plus'),
												__('Image Factory mcr', 'wp-photo-album-plus')
											);
							$values = array(	'default',
												'longdesc',
												'imagefactory',
												'default-mcr',
												'longdesc-mcr',
												'imagefactory-mcr'
											);
							$onchange = 'wppaCheckCoverType()';
							$html = wppa_select($slug, $options, $values, $onchange);
							$clas = '';
							$tags = 'cover,layout';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Number of coverphotos', 'wp-photo-album-plus');
							$desc = __('The umber of coverphotos. Must be > 1 and < 25.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_imgfact_count';
							$html = wppa_input($slug, '50px', '', __('photos', 'wp-photo-album-plus'));
							$clas = 'wppa_imgfact_';
							$tags = 'cover,layout';
							wppa_setting($slug, '6.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Cats include subs', 'wp-photo-album-plus');
							$desc = __('Child albums are included in Category based shortcodes.', 'wp-photo-album-plus');
							$help = esc_js(__('When you use album="#cat,...", in a shortcode, the child albums will be included.', 'wp-photo-album-plus'));
							$slug = 'wppa_cats_inherit';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'album,cover,meta';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'E', '1', __( 'Rating related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Rating login', 'wp-photo-album-plus');
							$desc = __('Users must login to rate photos.', 'wp-photo-album-plus');
							$help = esc_js(__('If users want to vote for a photo (rating 1..5 stars) the must login first. The avarage rating will always be displayed as long as the rating system is enabled.', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_login';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating,access';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating change', 'wp-photo-album-plus');
							$desc = __('Users may change their ratings.', 'wp-photo-album-plus');
							$help = esc_js(__('Users may change their ratings.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_change';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating multi', 'wp-photo-album-plus');
							$desc = __('Users may give multiple votes.', 'wp-photo-album-plus');
							$help = esc_js(__('Users may give multiple votes. (This has no effect when users may change their votes.)', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_multi';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rate own photos', 'wp-photo-album-plus');
							$desc = __('It is allowed to rate photos by the uploader himself.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_allow_owner_votes';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '3.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating requires comment', 'wp-photo-album-plus');
							$desc = __('Users must clarify their vote in a comment.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_vote_needs_comment';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '3.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Dislike value', 'wp-photo-album-plus');
							$desc = __('This value counts dislike rating.', 'wp-photo-album-plus');
							$help = esc_js(__('This value will be used for a dislike rating on calculation of avarage ratings.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_dislike_value';
							$html = wppa_input($slug, '50px', '', __('points', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Next after vote', 'wp-photo-album-plus');
							$desc = __('Goto next slide after voting', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the visitor goes straight to the slide following the slide he voted. This will speed up mass voting.', 'wp-photo-album-plus'));
							$slug = 'wppa_next_on_callback';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Star off opacity', 'wp-photo-album-plus');
							$desc = __('Rating star off state opacity value.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_star_opacity';
							$html = wppa_input($slug, '50px', '', __('%', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Notify inappropriate', 'wp-photo-album-plus');
							$desc = __('Notify admin every x times.', 'wp-photo-album-plus');
							$help = esc_js(__('If this number is positive, there will be a thumb down icon in the rating bar.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Cicking the icon indicates a user wants to report that an image is inappropiate.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Admin will be notified by email after every x reports.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('A value of 0 disables this feature.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_dislike_mail_every';
							$html = wppa_input($slug, '40px', '', __('reports', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Pending after', 'wp-photo-album-plus');
							$desc = __('Set status to pending after xx dislike votes.', 'wp-photo-album-plus');
							$help = esc_js(__('A value of 0 disables this feature.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_dislike_set_pending';
							$html = wppa_input($slug, '40px', '', __('reports', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Delete after', 'wp-photo-album-plus');
							$desc = __('Deete photo after xx dislike votes.', 'wp-photo-album-plus');
							$help = esc_js(__('A value of 0 disables this feature.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_dislike_delete';
							$html = wppa_input($slug, '40px', '', __('reports', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show dislike count', 'wp-photo-album-plus');
							$desc = __('Show the number of dislikes in the rating bar.', 'wp-photo-album-plus');
							$help = esc_js(__('Displayes the total number of dislike votes for the current photo.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_dislike_show_count';
							$html = wppa_checkbox($slug, $onchange);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rating display type', 'wp-photo-album-plus');
							$desc = __('Specify the type of the rating display.', 'wp-photo-album-plus');
							$help = esc_js(__('If "One button vote" is selected in Table I-E1, this setting has no meaning', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_display_type';
							$options = array(__('Graphic', 'wp-photo-album-plus'), __('Numeric', 'wp-photo-album-plus'));
							$values = array('graphic', 'numeric');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show average rating', 'wp-photo-album-plus');
							$desc = __('Display the avarage rating and/or vote count on the rating bar', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the average rating as well as the current users rating is displayed in max 5 or 10 stars.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If unchecked, only the current users rating is displayed (if any).', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If "One button vote" is selected in Table I-E1, this box checked will display the vote count.', 'wp-photo-album-plus'));
							$slug = 'wppa_show_avg_rating';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Single vote button text', 'wp-photo-album-plus');
							$desc = __('The text on the voting button.', 'wp-photo-album-plus');
							$help = __('This text may contain qTranslate compatible language tags.', 'wp-photo-album-plus');
							$slug = 'wppa_vote_button_text';
							$html = wppa_input($slug, '100');
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Single vote button text voted', 'wp-photo-album-plus');
							$desc = __('The text on the voting button when voted.', 'wp-photo-album-plus');
							$help = __('This text may contain qTranslate compatible language tags.', 'wp-photo-album-plus');
							$slug = 'wppa_voted_button_text';
							$html = wppa_input($slug, '100');
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Single vote button thumbnail', 'wp-photo-album-plus');
							$desc = __('Display single vote button below thumbnails.', 'wp-photo-album-plus');
							$help = esc_js(__('This works only in single vote mode: Table I-E1 set to "one button vote"', 'wp-photo-album-plus'));
							$slug = 'wppa_vote_thumb';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Medal bronze when', 'wp-photo-album-plus');
							$desc = __('Photo gets medal bronze when number of top-scores ( 5 or 10 ).', 'wp-photo-album-plus');
							$help = esc_js(__('When the photo has this number of topscores ( 5 or 10 stars ), it will get a bronze medal. A value of 0 indicates that you do not want this feature.', 'wp-photo-album-plus'));
							$slug = 'wppa_medal_bronze_when';
							$html = wppa_input($slug, '50px', '', __('Topscores', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '16.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Medal silver when', 'wp-photo-album-plus');
							$desc = __('Photo gets medal silver when number of top-scores ( 5 or 10 ).', 'wp-photo-album-plus');
							$help = esc_js(__('When the photo has this number of topscores ( 5 or 10 stars ), it will get a silver medal. A value of 0 indicates that you do not want this feature.', 'wp-photo-album-plus'));
							$slug = 'wppa_medal_silver_when';
							$html = wppa_input($slug, '50px', '', __('Topscores', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '16.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Medal gold when', 'wp-photo-album-plus');
							$desc = __('Photo gets medal bronze when number of top-scores ( 5 or 10 ).', 'wp-photo-album-plus');
							$help = esc_js(__('When the photo has this number of topscores ( 5 or 10 stars ), it will get a bronze medal. A value of 0 indicates that you do not want this feature.', 'wp-photo-album-plus'));
							$slug = 'wppa_medal_gold_when';
							$html = wppa_input($slug, '50px', '', __('Topscores', 'wp-photo-album-plus'));
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '16.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Medal tag color', 'wp-photo-album-plus');
							$desc = __('The color of the tag on the medal.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_medal_color';
							$opts = array( __('Red', 'wp-photo-album-plus'), __('Green', 'wp-photo-album-plus'), __('Blue', 'wp-photo-album-plus') );
							$vals = array( '1', '2', '3' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '16.4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Medal position', 'wp-photo-album-plus');
							$desc = __('The position of the medal on the image.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_medal_position';
							$opts = array( __('Top left', 'wp-photo-album-plus'), __('Top right', 'wp-photo-album-plus'), __('Bottom left', 'wp-photo-album-plus'), __('Bottom right', 'wp-photo-album-plus') );
							$vals = array( 'topleft', 'topright', 'botleft', 'botright' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_rating_';
							$tags = 'rating,layout';
							wppa_setting($slug, '16.5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Top criterium', 'wp-photo-album-plus');
							$desc = __('The top sort item used for topten results from shortcodes.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_topten_sortby';
							$opts = array( __('Mean raiting', 'wp-photo-album-plus'), __('Rating count', 'wp-photo-album-plus'), __('Viewcount', 'wp-photo-album-plus') );
							$vals = array( 'mean_rating', 'rating_count', 'views' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'rating,layout';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'F', '1', __( 'Comments related settings' , 'wp-photo-album-plus'), 'wppa_comment_' );
							{
							$name = __('Commenting login', 'wp-photo-album-plus');
							$desc = __('Users must be logged in to comment on photos.', 'wp-photo-album-plus');
							$help = esc_js(__('Check this box if you want users to be logged in to be able to enter comments on individual photos.', 'wp-photo-album-plus'));
							$slug = 'wppa_comment_login';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,access';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comments view login', 'wp-photo-album-plus');
							$desc = __('Users must be logged in to see comments on photos.', 'wp-photo-album-plus');
							$help = esc_js(__('Check this box if you want users to be logged in to be able to see existing comments on individual photos.', 'wp-photo-album-plus'));
							$slug = 'wppa_comment_view_login';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,access';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Last comment first', 'wp-photo-album-plus');
							$desc = __('Display the newest comment on top.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: Display the newest comment on top.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If unchecked, the comments are listed in the ordere they were entered.', 'wp-photo-album-plus'));
							$slug = 'wppa_comments_desc';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,layout';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment moderation', 'wp-photo-album-plus');
							$desc = __('Comments from what users need approval.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the desired users of which the comments need approval.', 'wp-photo-album-plus'));
							$slug = 'wppa_comment_moderation';
							$options = array(__('All users', 'wp-photo-album-plus'), __('Logged out users', 'wp-photo-album-plus'), __('No users', 'wp-photo-album-plus'));
							$values = array('all', 'logout', 'none');
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_comment_';
							$tags = 'comment,access';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment email required', 'wp-photo-album-plus');
							$desc = __('Commenting users must enter their email addresses.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_email_required';
							$opts = array( 	__('None', 'wp-photo-album-plus'),
											__('Optional', 'wp-photo-album-plus'),
											__('Required', 'wp-photo-album-plus'),
											);
							$vals = array( 	'none',
											'optional',
											'required',
											);
							$html = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_comment_';
							$tags = 'comment';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment notify', 'wp-photo-album-plus');
							$desc = __('Select who must receive an e-mail notification of a new comment.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_notify';
							$options = array(	__('--- None ---', 'wp-photo-album-plus'),
												__('--- Admin ---', 'wp-photo-album-plus'),
												__('--- Album owner ---', 'wp-photo-album-plus'),
												__('--- Admin & Owner ---', 'wp-photo-album-plus'),
												__('--- Uploader ---', 'wp-photo-album-plus'),
												__('--- Up & admin ---', 'wp-photo-album-plus'),
												__('--- Up & Owner ---', 'wp-photo-album-plus')
												);
							$values = array(	'none',
												'admin',
												'owner',
												'both',
												'upload',
												'upadmin',
												'upowner'
												);
							$usercount = wppa_get_user_count();
							if ( $usercount <= wppa_opt('wppa_max_users') ) {
								$users = wppa_get_users();
								foreach ( $users as $usr ) {
									$options[] = $usr['display_name'];
									$values[]  = $usr['ID'];
								}
							}
							$html = wppa_select($slug, $options, $values);
							$clas = 'wppa_comment_';
							$tags = 'comment';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment notify previous', 'wp-photo-album-plus');
							$desc = __('Notify users who has commented this photo earlier.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_com_notify_previous';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment';
							wppa_setting($slug, '5.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment ntfy added', 'wp-photo-album-plus');
							$desc = __('Show "Comment added" after successfull adding a comment.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_notify_added';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('ComTen alt display', 'wp-photo-album-plus');
							$desc = __('Display comments at comten thumbnails.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comten_alt_display';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,layout';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comten Thumbnail width', 'wp-photo-album-plus');
							$desc = __('The width of the thumbnail in the alt comment display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comten_alt_thumbsize';
							$html = wppa_input($slug, '50px', '', __('Pixels', 'wp-photo-album-plus'));
							$clas = 'wppa_comment_';
							$tags = 'comment,layout';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show smiley picker', 'wp-photo-album-plus');
							$desc = __('Display a clickable row of smileys.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_smiley_picker';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show commenter email', 'wp-photo-album-plus');
							$desc = __('Show the commenter\'s email in the notify emails.', 'wp-photo-album-plus');
							$help = esc_js(__('Shows the email address of the commenter in all notify emails.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If switched off, admin will still receive the senders email in the notification mail', 'wp-photo-album-plus'));
							$slug = 'wppa_mail_upl_email';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_comment_';
							$tags = 'comment,layout';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'G', '1', __( 'Lightbox related settings. These settings have effect only when Table IX-J3 is set to wppa' , 'wp-photo-album-plus') );
							{
							$name = __('Overlay opacity', 'wp-photo-album-plus');
							$desc = __('The opacity of the lightbox overlay background.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_opacity';
							$html = wppa_input($slug, '50px', '', __('%', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Click on background', 'wp-photo-album-plus');
							$desc = __('Select the action to be taken on click on background.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_onclick';
							$options = array(__('Nothing', 'wp-photo-album-plus'), __('Exit (close)', 'wp-photo-album-plus'), __('Browse (left/right)', 'wp-photo-album-plus'));
							$values = array('none', 'close', 'browse');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay animation speed', 'wp-photo-album-plus');
							$desc = __('The fade-in time of the lightbox images', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_anim';
							$options = array(__('--- off ---', 'wp-photo-album-plus'), __('very fast (100 ms.)', 'wp-photo-album-plus'), __('fast (200 ms.)', 'wp-photo-album-plus'), __('normal (300 ms.)', 'wp-photo-album-plus'),  __('slow (500 ms.)', 'wp-photo-album-plus'), __('very slow (1 s.)', 'wp-photo-album-plus'), __('extremely slow (2 s.)', 'wp-photo-album-plus'));
							$values = array('0', '100', '200', '300', '500', '1000', '2000');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '3.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay slideshow speed', 'wp-photo-album-plus');
							$desc = __('The time the lightbox images stay', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_slide';
							$options = array(__('fast (3 s.)', 'wp-photo-album-plus'), __('normal (5 s.)', 'wp-photo-album-plus'),  __('slow (8 s.)', 'wp-photo-album-plus'), __('very slow (13 s.)', 'wp-photo-album-plus'), __('extremely slow (20 s.)', 'wp-photo-album-plus'));
							$values = array('3000', '5000', '8000', '13000', '20000');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '3.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Overlay at top in Chrome', 'wp-photo-album-plus');
							$desc = __('Place the overlay (lightbox) image at the top of the page in Chrome browsers.', 'wp-photo-album-plus');
							$help = esc_js(__('This is required for certain mobile devices.', 'wp-photo-album-plus'));
							$slug = 'wppa_ovl_chrome_at_top';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('WPPA+ Lightbox global', 'wp-photo-album-plus');
							$desc = __('Use the wppa+ lightbox also for non-wppa images.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_lightbox_global';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('WPPA+ Lightbox global is a set', 'wp-photo-album-plus');
							$desc = __('Treat the other images as a set.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, you can scroll through the images in the lightbox view. Requires item 5 to be checked.', 'wp-photo-album-plus'));
							$slug = 'wppa_lightbox_global_set';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '5.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use hires files', 'wp-photo-album-plus');
							$desc = __('Use the highest resolution available for lightbox.', 'wp-photo-album-plus');
							$help = esc_js(__('Ticking this box is recommended for lightbox fullscreen modes.', 'wp-photo-album-plus'));
							$slug = 'wppa_lb_hres';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Video autostart', 'wp-photo-album-plus');
							$desc = __('Videos on lightbox start automaticly.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_video_start';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,video';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Audio autostart', 'wp-photo-album-plus');
							$desc = __('Audio on lightbox start automaticly.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_audio_start';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'lightbox,audio';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Lightbox start mode', 'wp-photo-album-plus');
							$desc = __('The mode lightbox starts in.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ovl_mode_initial';
							$opts = array(	__('Normal', 'wp-photo-album-plus'),
											__('Padded', 'wp-photo-album-plus'),
											__('Sttetched', 'wp-photo-album-plus'),
											__('Clipped', 'wp-photo-album-plus'),
											__('Real size', 'wp-photo-album-plus'),
											);
							$vals = array( 	'normal',
											'padded',
											'stretched',
											'clipped',
											'realsize',
											);
							$html = wppa_select($slug,$opts,$vals);
							$clas = '';
							$tags = 'lightbox,layout';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							}
							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_4">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 5: Fonts ?>
			<?php wppa_settings_box_header(
				'5',
				__('Table V:', 'wp-photo-album-plus').' '.__('Fonts:', 'wp-photo-album-plus').' '.
				__('This table describes the Fonts used for the wppa+ elements.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_5" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_5">
							<tr>
								<td scope="col" ><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font family', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font size', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font color', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font weight', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_5">
							<?php
							$wppa_table = 'V';

							$wppa_subtable = 'Z';	// No subtables

							$options = array(__('normal', 'wp-photo-album-plus'), __('bold', 'wp-photo-album-plus'), __('bolder', 'wp-photo-album-plus'), __('lighter', 'wp-photo-album-plus'), '100', '200', '300', '400', '500', '600', '700', '800', '900');
							$values = array('normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900');

							$name = __('Album titles', 'wp-photo-album-plus');
							$desc = __('Font used for Album titles.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for album cover titles.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_title';
							$slug2 = 'wppa_fontsize_title';
							$slug3 = 'wppa_fontcolor_title';
							$slug4 = 'wppa_fontweight_title';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,album';
							wppa_setting($slug, '1a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow desc', 'wp-photo-album-plus');
							$desc = __('Font for slideshow photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for slideshow photo descriptions.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_fulldesc';
							$slug2 = 'wppa_fontsize_fulldesc';
							$slug3 = 'wppa_fontcolor_fulldesc';
							$slug4 = 'wppa_fontweight_fulldesc';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,slide';
							wppa_setting($slug, '2a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Slideshow name', 'wp-photo-album-plus');
							$desc = __('Font for slideshow photo names.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for slideshow photo names.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_fulltitle';
							$slug2 = 'wppa_fontsize_fulltitle';
							$slug3 = 'wppa_fontcolor_fulltitle';
							$slug4 = 'wppa_fontweight_fulltitle';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,slide,meta';
							wppa_setting($slug, '3a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Navigations', 'wp-photo-album-plus');
							$desc = __('Font for navigations.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for navigation items.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_nav';
							$slug2 = 'wppa_fontsize_nav';
							$slug3 = 'wppa_fontcolor_nav';
							$slug4 = 'wppa_fontweight_nav';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,navi';
							wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Thumbnails', 'wp-photo-album-plus');
							$desc = __('Font for text under thumbnails.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for text under thumbnail images.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_thumb';
							$slug2 = 'wppa_fontsize_thumb';
							$slug3 = 'wppa_fontcolor_thumb';
							$slug4 = 'wppa_fontweight_thumb';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,thumb';
							wppa_setting($slug, '5a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Other', 'wp-photo-album-plus');
							$desc = __('General font in wppa boxes.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for all other items.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_box';
							$slug2 = 'wppa_fontsize_box';
							$slug3 = 'wppa_fontcolor_box';
							$slug4 = 'wppa_fontweight_box';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout';
							wppa_setting($slug, '6a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Numbar', 'wp-photo-album-plus');
							$desc = __('Font in wppa number bars.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for numberbar navigation.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_numbar';
							$slug2 = 'wppa_fontsize_numbar';
							$slug3 = 'wppa_fontcolor_numbar';
							$slug4 = 'wppa_fontweight_numbar';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,slide';
							wppa_setting($slug, '7a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Numbar Active', 'wp-photo-album-plus');
							$desc = __('Font in wppa number bars, active item.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for numberbar navigation.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_numbar_active';
							$slug2 = 'wppa_fontsize_numbar_active';
							$slug3 = 'wppa_fontcolor_numbar_active';
							$slug4 = 'wppa_fontweight_numbar_active';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,slide';
							wppa_setting($slug, '8a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Lightbox', 'wp-photo-album-plus');
							$desc = __('Font in wppa lightbox overlays.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter font name, size, color and weight for wppa lightbox overlays.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_fontfamily_lightbox';
							$slug2 = 'wppa_fontsize_lightbox';
							$slug3 = 'wppa_fontcolor_lightbox';
							$slug4 = 'wppa_fontweight_lightbox';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = wppa_input($slug1, '90%', '200px', '');
							$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html3 = wppa_input($slug3, '70px', '', '');
							$html4 = wppa_select($slug4, $options, $values);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'layout,lightbox';
							wppa_setting($slug, '9a,b,c,d', $name, $desc, $html, $help, $clas, $tags);

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_5">
							<tr>
								<td scope="col" ><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font family', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font size', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font color', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Font weight', 'wp-photo-album-plus') ?></td>
								<td scope="col" ><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 6: Links ?>
			<?php wppa_settings_box_header(
				'6',
				__('Table VI:', 'wp-photo-album-plus').' '.__('Links:', 'wp-photo-album-plus').' '.
				__('This table defines the link types and pages.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_6" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_6">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Link type', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Link page', 'wp-photo-album-plus') ?></td>
								<td><?php _e('New tab', 'wp-photo-album-plus') ?></td>
								<th scope="col" title="<?php _e('Photo specific link overrules', 'wp-photo-album-plus') ?>" style="cursor: default"><?php _e('PSO', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_6">
							<?php
							$wppa_table = 'VI';
							$wppa_subtable = 'Z';
/*
							// Linktypes
							$options_linktype = array(
								__('no link at all.'),
								__('the plain photo (file).'),
								__('the full size photo in a slideshow.'),
								__('the fullsize photo on its own.'),
								__('the single photo in the style of a slideshow.'),
								__('the fs photo with download and print buttons.'),
								__('a plain page without a querystring.'),
								__('lightbox.')
							);
							$values_linktype = array(
								'none',
								'file',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$options_linktype_album = array(
								__('no link at all.'),
								__('the plain photo (file).'),
								__('the content of the album.'),
								__('the full size photo in a slideshow.'),
								__('the fullsize photo on its own.'),
								__('lightbox.')
							);
							$values_linktype_album = array('none', 'file', 'album', 'photo', 'single', 'lightbox');



*/

							// Linkpages
							$options_page = false;
							$options_page_post = false;
							$values_page = false;
							$values_page_post = false;
							// First
							$options_page_post[] = __('--- The same post or page ---', 'wp-photo-album-plus');
							$values_page_post[] = '0';
							$options_page[] = __('--- Please select a page ---', 'wp-photo-album-plus');
							$values_page[] = '0';
							// Pages if any
							$query = "SELECT ID, post_title, post_content, post_parent FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_title ASC";
							$pages = $wpdb->get_results ($query, ARRAY_A);
							if ($pages) {
								if ( wppa_switch('wppa_hier_pagesel') ) $pages = wppa_add_parents($pages);
								else {	// Just translate
									foreach ( array_keys($pages) as $index ) {
										$pages[$index]['post_title'] = __(stripslashes($pages[$index]['post_title']), 'wp-photo-album-plus');
									}
								}
								$pages = wppa_array_sort($pages, 'post_title');
								foreach ($pages as $page) {
									if (strpos($page['post_content'], '%%wppa%%') !== false || strpos($page['post_content'], '[wppa') !== false) {
										$options_page[] = __($page['post_title'], 'wp-photo-album-plus');
										$options_page_post[] = __($page['post_title'], 'wp-photo-album-plus');
										$values_page[] = $page['ID'];
										$values_page_post[] = $page['ID'];
									}
									else {
										$options_page[] = '|'.__($page['post_title'], 'wp-photo-album-plus').'|';
										$options_page_post[] = '|'.__($page['post_title'], 'wp-photo-album-plus').'|';
										$values_page[] = $page['ID'];
										$values_page_post[] = $page['ID'];
									}
								}
							}
							else {
								$options_page[] = __('--- No page to link to (yet) ---', 'wp-photo-album-plus');
								$values_page[] = '0';
							}

							$options_page_auto = $options_page;
							$options_page_auto[0] = __('--- Will be auto created ---', 'wp-photo-album-plus');

						wppa_setting_subheader('A', '4', __('Links from images in WPPA+ Widgets', 'wp-photo-album-plus'));
							{
							$name = __('PotdWidget', 'wp-photo-album-plus');
							$desc = __('Photo Of The Day widget link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the photo of the day points to.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you select \'defined on widget admin page\' you can manually enter a link and title on the Photo of the day Widget Admin page.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_widget_linktype';
							$slug2 = 'wppa_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_potd_blank';
							$slug4 = 'wppa_potdwidget_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckPotdLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('defined on widget admin page.', 'wp-photo-album-plus'),
								__('the content of the album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'custom',
								'album',
								'photo',
								'single',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_potdlp';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, '', $clas);
							$clas = 'wppa_potdlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$htmlerr = wppa_htmlerr('widget');
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,thumb';
							wppa_setting($slug, '1a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('SlideWidget', 'wp-photo-album-plus');
							$desc = __('Slideshow widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the slideshow photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_slideonly_widget_linktype';
							$slug2 = 'wppa_slideonly_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_sswidget_blank';
							$slug4 = 'wppa_sswidget_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckSlideOnlyLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('defined at widget activation.', 'wp-photo-album-plus'),
								__('the content of the album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'widget',
								'album',
								'photo',
								'single',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_solp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_solb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,slide';
							wppa_setting($slug, '2a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Album widget', 'wp-photo-album-plus');
							$desc = __('Album widget thumbnail link', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the album widget photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_album_widget_linktype';
							$slug2 = 'wppa_album_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_album_widget_blank';
						//	$slug4 = 'wppa_album_widget_overrule';	// useless
							$slug = array($slug1, $slug2, $slug3);
							$onchange = 'wppaCheckAlbumWidgetLink();';
							$opts = array(
								__('subalbums and thumbnails.', 'wp-photo-album-plus'),
								__('slideshow.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'content',
								'slide',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_awlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_awlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = ''; // wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,album';
							wppa_setting($slug, '3a,b,c', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('ThumbnailWidget', 'wp-photo-album-plus');
							$desc = __('Thumbnail widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the thumbnail photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_thumbnail_widget_linktype';
							$slug2 = 'wppa_thumbnail_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_thumbnail_widget_blank';
							$slug4 = 'wppa_thumbnail_widget_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckThumbnailWLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_tnlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_tnlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,thumb';
							wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('TopTenWidget', 'wp-photo-album-plus');
							$desc = __('TopTen widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the top ten photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_topten_widget_linktype';
							$slug2 = 'wppa_topten_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_topten_blank';
							$slug4 = 'wppa_topten_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckTopTenLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the virtual topten album.', 'wp-photo-album-plus'),
								__('the content of the thumbnails album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'thumbalbum',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_ttlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_ttlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = 'wppa_rating';
							$clas = '';
							$tags = 'widget,link,thumb,rating';
							wppa_setting($slug, '5a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('LasTenWidget', 'wp-photo-album-plus');
							$desc = __('Last Ten widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the last ten photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_lasten_widget_linktype';
							$slug2 = 'wppa_lasten_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_lasten_blank';
							$slug4 = 'wppa_lasten_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckLasTenLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the virtual lasten album.', 'wp-photo-album-plus'),
								__('the content of the thumbnails album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'thumbalbum',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_ltlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_ltlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,thumb';
							wppa_setting($slug, '6a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('CommentWidget', 'wp-photo-album-plus');
							$desc = __('Comment widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the comment widget photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_comment_widget_linktype';
							$slug2 = 'wppa_comment_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_comment_blank';
							$slug4 = 'wppa_comment_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckCommentLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the virtual comten album.', 'wp-photo-album-plus'),
								__('the content of the thumbnails album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'thumbalbum',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_cmlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_cmlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,thumb,comment';
							wppa_setting($slug, '7a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('FeaTenWidget', 'wp-photo-album-plus');
							$desc = __('FeaTen widget photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the featured ten photos point to.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_featen_widget_linktype';
							$slug2 = 'wppa_featen_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_featen_blank';
							$slug4 = 'wppa_featen_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckFeaTenLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the virtual featen album.', 'wp-photo-album-plus'),
								__('the content of the thumbnails album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'thumbalbum',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_ftlp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_ftlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'widget,link,thumb';
							wppa_setting($slug, '8a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader('B', '4', __('Links from other WPPA+ images', 'wp-photo-album-plus'));
							{
							$name = __('Cover Image', 'wp-photo-album-plus');
							$desc = __('The link from the cover image of an album.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link the coverphoto points to.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The link from the album title can be configured on the Edit Album page.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('This link will be used for the photo also if you select: same as title.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you specify New Tab on this line, all links from the cover will open a new tab,', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('except when Ajax is activated on Table IV-A1.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_coverimg_linktype';
							$slug2 = 'wppa_coverimg_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_coverimg_blank';
							$slug4 = 'wppa_coverimg_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckCoverImg()';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('same as title.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus'),
								__('a slideshow starting at the photo', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'same',
								'lightbox',
								'slideshowstartatimage'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = '';
							$html2 = '';
							$clas = 'wppa_covimgbl';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link,cover';
							wppa_setting($slug, '1a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Thumbnail', 'wp-photo-album-plus');
							$desc = __('Thumbnail link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link you want, or no link at all.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you select the fullsize photo on its own, it will be stretched to fit, regardless of that setting.', 'wp-photo-album-plus')); /* oneofone is treated as portrait only */
							$help .= '\n'.esc_js(__('Note that a page must have at least %%wppa%% or [wppa][/wppa] in its content to show up the photo(s).', 'wp-photo-album-plus'));
							$slug1 = 'wppa_thumb_linktype';
							$slug2 = 'wppa_thumb_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_thumb_blank';
							$slug4 = 'wppa_thumb_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckThumbLink()';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('the single photo in the style of a slideshow.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('a plain page without a querystring.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'photo',
								'single',
								'slphoto',
								'fullpopup',
								'plainpage',
								'lightbox'
							);
							if ( wppa_switch('wppa_auto_page') ) {
								$opts[] = __('Auto Page', 'wp-photo-album-plus');
								$vals[] = 'autopage';
							}
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_tlp';
							$html2 = wppa_select($slug2, $options_page_post, $values_page_post, '', $clas);
							$clas = 'wppa_tlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$htmlerr = wppa_htmlerr('popup-lightbox');
							$html = array($html1, $htmlerr.$html2, $html3, $html4);
							$clas = 'tt_always';
							$clas = '';
							$tags = 'link,thumb';
							wppa_setting($slug, '2a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Sphoto', 'wp-photo-album-plus');
							$desc = __('Single photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link you want, or no link at all.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you select the fullsize photo on its own, it will be stretched to fit, regardless of that setting.', 'wp-photo-album-plus')); /* oneofone is treated as portrait only */
							$help .= '\n'.esc_js(__('Note that a page must have at least %%wppa%% or [wppa][/wppa] in its content to show up the photo(s).', 'wp-photo-album-plus'));
							$slug1 = 'wppa_sphoto_linktype';
							$slug2 = 'wppa_sphoto_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_sphoto_blank';
							$slug4 = 'wppa_sphoto_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckSphotoLink(); wppaCheckLinkPageErr(\'sphoto\');';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'photo',
								'single',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_slp';
							$onchange = 'wppaCheckLinkPageErr(\'sphoto\');';
							$html2 = wppa_select($slug2, $options_page, $values_page, $onchange, $clas, true);
							$clas = 'wppa_slb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$htmlerr = wppa_htmlerr('sphoto');
							$html = array($html1, $htmlerr.$html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '3a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Mphoto', 'wp-photo-album-plus');
							$desc = __('Media-like photo link.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the type of link you want, or no link at all.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you select the fullsize photo on its own, it will be stretched to fit, regardless of that setting.', 'wp-photo-album-plus')); /* oneofone is treated as portrait only */
							$help .= '\n'.esc_js(__('Note that a page must have at least %%wppa%% or [wppa][/wppa] in its content to show up the photo(s).', 'wp-photo-album-plus'));
							$slug1 = 'wppa_mphoto_linktype';
							$slug2 = 'wppa_mphoto_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_mphoto_blank';
							$slug4 = 'wppa_mphoto_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckMphotoLink(); wppaCheckLinkPageErr(\'mphoto\');';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the content of the album.', 'wp-photo-album-plus'),
								__('the full size photo in a slideshow.', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'album',
								'photo',
								'single',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_mlp';
							$onchange = 'wppaCheckLinkPageErr(\'mphoto\');';
							$html2 = wppa_select($slug2, $options_page, $values_page, $onchange, $clas, true);
							$clas = 'wppa_mlb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$htmlerr = wppa_htmlerr('mphoto');
							$html = array($html1, $htmlerr.$html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Slideshow', 'wp-photo-album-plus');
							$desc = __('Slideshow fullsize link', 'wp-photo-album-plus');
							$help = esc_js(__('You can overrule lightbox but not big browse buttons with the photo specifc link.', 'wp-photo-album-plus'));
							$help .= '\n\n* '.esc_js(__('fullsize slideshow can only be set by the WPPA_SET shortcode.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_slideshow_linktype';
							$slug2 = 'wppa_slideshow_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_slideshow_blank';
							$slug4 = 'wppa_slideshow_overrule';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$onchange = 'wppaCheckSlidePhotoLink();';
							$opts = array(
								__('no link at all.', 'wp-photo-album-plus'),
								__('the plain photo (file).', 'wp-photo-album-plus'),
								__('the fullsize photo on its own.', 'wp-photo-album-plus'),
								__('lightbox.', 'wp-photo-album-plus'),
								__('lightbox single photos.', 'wp-photo-album-plus'),
								__('the fs photo with download and print buttons.', 'wp-photo-album-plus'),
								__('the thumbnails.', 'wp-photo-album-plus'),
								__('fullsize slideshow', 'wp-photo-album-plus') . '*|',
							);
							$vals = array(
								'none',
								'file',
								'single',
								'lightbox',
								'lightboxsingle',
								'fullpopup',
								'thumbs',
								'slide',
							);
							$onchange = 'wppaCheckSlidePhotoLink();wppaCheckSlideVideoControls()';
							$html1 = wppa_select($slug1, $opts, $vals, $onchange);
							$clas = 'wppa_sslp';
							$html2 = wppa_select($slug2, $options_page_post, $values_page_post, $onchange, $clas);
							$clas = 'wppa_sslb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link,slide';
							wppa_setting($slug, '5a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Film linktype', 'wp-photo-album-plus');
							$desc = __('Direct access goto image in:', 'wp-photo-album-plus');
							$help = esc_js(__('Select the action to be taken when the user clicks on a filmstrip image.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_film_linktype';
							$slug3 = 'wppa_film_blank';
							$slug4 = 'wppa_film_overrule';
							$opts = array(
								__('slideshow window', 'wp-photo-album-plus'),
								__('lightbox overlay', 'wp-photo-album-plus')
							);
							$vals = array(
								'slideshow',
								'lightbox'
							);
							$html1 = wppa_select($slug1, $opts, $vals);
							$html2 = '';
							$html3 = wppa_checkbox($slug3);
							$html4 = wppa_checkbox($slug4);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link,slide';
							wppa_setting($slug, '6a,,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader('C', '4', __('Other links', 'wp-photo-album-plus'));
							{
							$name = __('Download Link (aka Art Monkey link)', 'wp-photo-album-plus');
							$desc = __('Makes the photo name a download button.', 'wp-photo-album-plus');
							$help = esc_js(__('Link Photo name in slideshow to file or zip with photoname as filename.', 'wp-photo-album-plus'));
							$slug = 'wppa_art_monkey_link';
							$opts = array(
								__('--- none ---', 'wp-photo-album-plus'),
								__('image file', 'wp-photo-album-plus'),
								__('zipped image', 'wp-photo-album-plus')
							);
							$vals = array(
								'none',
								'file',
								'zip'
							);
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '1', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Art Monkey Source', 'wp-photo-album-plus');
							$desc = __('Use Source file for art monkey link if available.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_artmonkey_use_source';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '1.1', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Art Monkey Display', 'wp-photo-album-plus');
							$desc = __('Select button or link ( text ).', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_art_monkey_display';
							$opts = array(
								__('Button', 'wp-photo-album-plus'),
								__('Textlink', 'wp-photo-album-plus')
							);
							$vals = array(
								'button',
								'text'
							);
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'link,layout';
							wppa_setting($slug, '1.2', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Popup Download Link', 'wp-photo-album-plus');
							$desc = __('Configure the download link on fullsize popups.', 'wp-photo-album-plus');
							$help = esc_js(__('Link fullsize popup download button to either image or zip file.', 'wp-photo-album-plus'));
							$slug = 'wppa_art_monkey_popup_link';
							$opts = array(
								__('image file', 'wp-photo-album-plus'),
								__('zipped image', 'wp-photo-album-plus')
							);
							$vals = array(
								'file',
								'zip'
							);
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'link,layout';
							wppa_setting($slug, '1.3', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Download link on lightbox', 'wp-photo-album-plus');
							$desc = __('Art monkey link on lightbox photo names.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_art_monkey_on_lightbox';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,layout';
							wppa_setting($slug, '1.4', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Album download link', 'wp-photo-album-plus');
							$desc = __('Place an album download link on the album covers', 'wp-photo-album-plus');
							$help = esc_js(__('Creates a download zipfile containing the photos of the album', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_download_album';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link,layout,cover,album';
							wppa_setting($slug, '2', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Album download Source', 'wp-photo-album-plus');
							$desc = __('Use Source file for album download link if available.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_download_album_source';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '2.1', $name, $desc, $html.'</td><td></td><td></td><td>', $help, $clas, $tags);
							}
							{
							$name = __('Tagcloud Link', 'wp-photo-album-plus');
							$desc = __('Configure the link from the tags in the tag cloud.', 'wp-photo-album-plus');
							$help = esc_js(__('Link the tag words to ether the thumbnails or the slideshow.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_tagcloud_linktype';
							$slug2 = 'wppa_tagcloud_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_tagcloud_blank';
							$slug4 = 'wppa_tagcloud_linkpage_oc';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$opts1 = array(
								__('thumbnails', 'wp-photo-album-plus'),
								__('slideshow', 'wp-photo-album-plus')
							);
							$vals1 = array(
								'album',
								'slide'
							);
							$opts4 = array('1','2','3','4','5');
							$vals4 = array('1','2','3','4','5');
							$onchange = 'wppaCheckTagLink();';
							$html1 = wppa_select($slug1, $opts1, $vals1, $onchange);
							$clas = 'wppa_tglp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_tglb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = '<div style="font-size:9px;foat:left;" class="'.$clas.'" >'.__('Occur', 'wp-photo-album-plus').'</div>'.wppa_select($slug4, $opts4, $vals4, '', $clas);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '3a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Multitag Link', 'wp-photo-album-plus');
							$desc = __('Configure the link from the multitag selection.', 'wp-photo-album-plus');
							$help = esc_js(__('Link to ether the thumbnails or the slideshow.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_multitag_linktype';
							$slug2 = 'wppa_multitag_linkpage';
							wppa_verify_page($slug2);
							$slug3 = 'wppa_multitag_blank';
							$slug4 = 'wppa_multitag_linkpage_oc';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$opts1 = array(
								__('thumbnails', 'wp-photo-album-plus'),
								__('slideshow', 'wp-photo-album-plus')
							);
							$vals1 = array(
								'album',
								'slide'
							);
							$opts4 = array('1','2','3','4','5');
							$vals4 = array('1','2','3','4','5');
							$onchange = 'wppaCheckMTagLink();';
							$html1 = wppa_select($slug1, $opts1, $vals1, $onchange);
							$clas = 'wppa_tglp';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = 'wppa_tglb';
							$html3 = wppa_checkbox($slug3, '', $clas);
							$html4 = '<div style="font-size:9px;foat:left;" class="'.$clas.'" >'.__('Occur', 'wp-photo-album-plus').'</div>'.wppa_select($slug4, $opts4, $vals4, '', $clas);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Super View Landing', 'wp-photo-album-plus');
							$desc = __('The landing page for the Super View widget.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_super_view_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = __('Defined by the visitor', 'wp-photo-album-plus');
							$clas = '';
							$onchange = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Uploader Landing', 'wp-photo-album-plus');
							$desc = __('Select the landing page for the Uploader Widget', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_upldr_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = '';
							$clas = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Bestof Landing', 'wp-photo-album-plus');
							$desc = __('Select the landing page for the BestOf Widget / Box', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_bestof_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = '';
							$clas = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Album navigator Link', 'wp-photo-album-plus');
							$desc = __('Select link type and page for the Album navigator Widget', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_album_navigator_widget_linktype';
							$slug2 = 'wppa_album_navigator_widget_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$opts = array(
								__('thumbnails', 'wp-photo-album-plus'),
								__('slideshow', 'wp-photo-album-plus')
							);
							$vals = array(
								'thumbs',
								'slide'
							);
							$html1 = wppa_select($slug1, $opts, $vals);
							$clas = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('Supersearch Landing', 'wp-photo-album-plus');
							$desc = __('Select the landing page for the Supersearch Box', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_supersearch_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$html1 = '';
							$clas = '';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, $onchange, $clas);
							$clas = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);
							}
							{
							$name = __('SM widget return', 'wp-photo-album-plus');
							$desc = __('Select the return link for social media from widgets', 'wp-photo-album-plus');
							$help = esc_js(__('If you select Landing page, and it wont work, it may be required to set the Occur to the sequence number of the landing shortcode on the page.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Normally it is 1, but you can try 2 etc. Always create a new shared link to test a setting.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_widget_sm_linktype';
							$slug2 = 'wppa_widget_sm_linkpage';
							wppa_verify_page($slug2);
							$slug3 = '';
							$slug4 = 'wppa_widget_sm_linkpage_oc';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$opts = array(
								__('Home page', 'wp-photo-album-plus'),
								__('Landing page', 'wp-photo-album-plus')
							);
							$vals = array(
								'home',
								'landing'
							);
							$onchange = 'wppaCheckSmWidgetLink();';
							$clas = 'wppa_smrt';
							$html1 = wppa_select($slug1, $opts, $vals, $onchange, $clas);
							$clas = 'wppa_smrp';
							$html2 = wppa_select($slug2, $options_page_auto, $values_page, '', $clas);
							$html3 = '<div style="font-size:9px;foat:left;" class="'.$clas.'" >'.__('Occur', 'wp-photo-album-plus').'</div>';
							$html4 = wppa_select($slug4, array('1','2','3','4','5'), array('1','2','3','4','5'), '', $clas);
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							}
							{
							$name = __('Album cover subalbums link', 'wp-photo-album-plus');
							$desc = __('Select the linktype and display type for sub-albums on parent album covers.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_cover_sublinks';
							$slug2 = 'wppa_cover_sublinks_display';
							$slug3 = '';
							$slug4 = '';
							$slug = array($slug1, $slug2, $slug3, $slug4);
							$opts = array(
								__('No link at all', 'wp-photo-album-plus'),
								__('Thumbnails and covers', 'wp-photo-album-plus'),
								__('Slideshow or covers', 'wp-photo-album-plus'),
							);
							$vals = array(
								'none',
								'content',
								'slide',
							);
							$clas = '';
							$html1 = wppa_select($slug1, $opts, $vals, $onchange, $clas);
							$opts = array(
								__('No display at all', 'wp-photo-album-plus'),
								__('A list with sub(sub) albums', 'wp-photo-album-plus'),
								__('A list of children only', 'wp-photo-album-plus'),
								__('An enumeration of names', 'wp-photo-album-plus'),
								__('Micro thumbnails', 'wp-photo-album-plus'),
							);
							$vals = array(
								'none',
								'recursivelist',
								'list',
								'enum',
								'microthumbs',
							);
							$html2 = wppa_select($slug2, $opts, $vals, $onchange, $clas);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'link';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);
							}

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_6">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Link type', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Link page', 'wp-photo-album-plus') ?></td>
								<td><?php _e('New tab', 'wp-photo-album-plus') ?></td>
								<th scope="col" title="<?php _e('Photo specific link overrules', 'wp-photo-album-plus') ?>" style="cursor: default"><?php _e('PSO', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 7: Security ?>
			<?php wppa_settings_box_header(
				'7',
				__('Table VII:', 'wp-photo-album-plus').' '.__('Permissions and Restrictions:', 'wp-photo-album-plus').' '.
				__('This table describes the access settings for admin and front-end activities.', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_7" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table" style="padding-bottom:0; margin-bottom:0;" >
						<thead style="font-weight: bold; " class="wppa_table_7">
							<tr>
								<?php
									$wppacaps = array(	'wppa_admin',
														'wppa_upload',
														'wppa_import',
														'wppa_moderate',
														'wppa_export',
														'wppa_settings',
														'wppa_potd',
														'wppa_comments',
														'wppa_help'
														);
									$wppanames = array( __('Album Admin', 'wp-photo-album-plus' ),
														__('Upload Photos', 'wp-photo-album-plus' ),
														__('Import Photos', 'wp-photo-album-plus' ),
														__('Moderate P+C', 'wp-photo-album-plus' ),
														__('Export Photos', 'wp-photo-album-plus' ),
														__('Settings', 'wp-photo-album-plus' ),
														__('Photo of the day', 'wp-photo-album-plus' ),
														__('Comment&nbsp;Admin', 'wp-photo-album-plus' ),
														__('Help & Info', 'wp-photo-album-plus' )
														);
									echo '<td>'.__('Role', 'wp-photo-album-plus').'</td>';
									for ($i = 0; $i < count($wppacaps); $i++) echo '<td style="width:11%;">'.$wppanames[$i].'</td>';
								?>
							</tr>
						</thead>
						<tbody class="wppa_table_7">
							<?php
							$wppa_table = 'VII';

							wppa_setting_subheader('A', '6', __('Admin settings per user role. Enabling these settings will overrule the front-end settings for the specific user role', 'wp-photo-album-plus'));

							$tags = 'access,system';
							$roles = $wp_roles->roles;
							foreach (array_keys($roles) as $key) {
								$role = $roles[$key];
								echo '<tr class="wppa-VII-A wppa-none '.wppa_tags_to_clas($tags).'" ><td>'.$role['name'].'</td>';
								$caps = $role['capabilities'];
								for ($i = 0; $i < count($wppacaps); $i++) {
									if (isset($caps[$wppacaps[$i]])) {
										$yn = $caps[$wppacaps[$i]] ? true : false;
									}
									else $yn = false;
									$enabled = ( $key != 'administrator' );
									echo '<td>'.wppa_checkbox_e('caps-'.$wppacaps[$i].'-'.$key, $yn, '', '', $enabled).'</td>';
								};
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
					<table class="widefat wppa-table wppa-setting-table" style="margin-top:-2px;padding-top:0;" >
						<tbody class="wppa_table_7">
							<?php
							wppa_setting_subheader( 'B', '2', __('Frontend create Albums and upload Photos enabling and limiting settings' , 'wp-photo-album-plus') );

							$name = __('User create Albums', 'wp-photo-album-plus');
							$desc = __('Enable frontend album creation.', 'wp-photo-album-plus');
							$help = esc_js(__('If you check this item, frontend album creation will be enabled.', 'wp-photo-album-plus'));
							$slug = 'wppa_user_create_on';
							$onchange = '';//wppaCheckUserUpload()';
							$html1 = wppa_checkbox($slug, $onchange);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,album';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User edit album', 'wp-photo-album-plus');
							$desc = __('Enable frontent edit album name and description.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_user_album_edit_on';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,album';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User delete Albums', 'wp-photo-album-plus');
							$desc = __('Enable frontend album deletion', 'wp-photo-album-plus');
							$help = esc_js(__('If you check this item, frontend album deletion will be enabled.', 'wp-photo-album-plus'));
							$slug = 'wppa_user_destroy_on';
							$onchange = '';
							$html1 = wppa_checkbox($slug, $onchange);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,album';
							wppa_setting($slug, '1.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('User create Albums login', 'wp-photo-album-plus');
							$desc = __('Frontend album creation requires the user is logged in.', 'wp-photo-album-plus');
							$help = '';//esc_js(__('If you uncheck this box, make sure you check the item Owners only in the next sub-table.'));
//							$help .= '\n'.esc_js(__('Set the owner to ---public--- of the albums that are allowed to be uploaded to.'));
							$slug = 'wppa_user_create_login';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,album';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							// User upload limits
							$options = array( 	__('for ever', 'wp-photo-album-plus'),
												__('per hour', 'wp-photo-album-plus'),
												__('per day', 'wp-photo-album-plus'),
												__('per week', 'wp-photo-album-plus'),
												__('per month', 'wp-photo-album-plus'), 	// 30 days
												__('per year', 'wp-photo-album-plus'));	// 364 days
							$values = array( '0', '3600', '86400', '604800', '2592000', '31449600');

							$roles = $wp_roles->roles;
							$roles['loggedout'] = '';
							unset ( $roles['administrator'] );
							foreach (array_keys($roles) as $role) {
								if ( get_option('wppa_'.$role.'_upload_limit_count', 'nil') == 'nil') update_option('wppa_'.$role.'_upload_limit_count', '0');
								if ( get_option('wppa_'.$role.'_upload_limit_time', 'nil') == 'nil') update_option('wppa_'.$role.'_upload_limit_time', '0');
								$name = sprintf(__('Upload limit %s', 'wp-photo-album-plus'), $role);
								if ( $role == 'loggedout' ) $desc = __('Limit upload capacity for logged out users.', 'wp-photo-album-plus');
								else $desc = sprintf(__('Limit upload capacity for the user role %s.', 'wp-photo-album-plus'), $role);
								if ( $role == 'loggedout' ) $help = esc_js(__('This setting has only effect when Table VII-B2 is unchecked.', 'wp-photo-album-plus'));
								else $help = esc_js(__('This limitation only applies to frontend uploads when the same userrole does not have the Upload checkbox checked in Table VII-A.', 'wp-photo-album-plus'));
								$help .= '\n'.esc_js(__('A value of 0 means: no limit.', 'wp-photo-album-plus'));
								$slug1 = 'wppa_'.$role.'_upload_limit_count';
								$html1 = wppa_input($slug1, '50px', '', __('photos', 'wp-photo-album-plus'));
								$slug2 = 'wppa_'.$role.'_upload_limit_time';
								$html2 = wppa_select($slug2, $options, $values);
								$html = array( $html1, $html2 );
								$clas = '';
								$tags = 'access,upload';
								wppa_setting(false, '5.'.$role, $name, $desc, $html, $help, $clas, $tags);
							}

							foreach (array_keys($roles) as $role) {
								if ( get_option('wppa_'.$role.'_album_limit_count', 'nil') == 'nil') update_option('wppa_'.$role.'_album_limit_count', '0');
								$name = sprintf(__('Album limit %s', 'wp-photo-album-plus'), $role);
								$desc = sprintf(__('Limit number of albums for the user role %s.', 'wp-photo-album-plus'), $role);
								$help = esc_js(__('This limitation only applies to frontend create albums when the same userrole does not have the Album admin checkbox checked in Table VII-A.', 'wp-photo-album-plus'));
								$help .= '\n'.esc_js(__('A value of 0 means: no limit.', 'wp-photo-album-plus'));
								$slug1 = 'wppa_'.$role.'_album_limit_count';
								$html1 = wppa_input($slug1, '50px', '', __('albums', 'wp-photo-album-plus'));
								$slug2 = '';
								$html2 = '';
								$html = array( $html1, $html2 );
								$clas = '';
								$tags = 'access,album';
								wppa_setting(false, '5a.'.$role, $name, $desc, $html, $help, $clas, $tags);
							}

							$name = __('Upload one only', 'wp-photo-album-plus');
							$desc = __('Non admin users can upload only one photo at a time.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_upload_one_only';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,upload';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload moderation', 'wp-photo-album-plus');
							$desc = __('Uploaded photos need moderation.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, photos uploaded by users who do not have photo album admin access rights need moderation.', 'wp-photo-album-plus'));
							$help .= esc_js(__('Users who have photo album admin access rights can change the photo status to publish or featured.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('You can set the album admin access rights in Table VII-A.', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_moderate';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload notify', 'wp-photo-album-plus');
							$desc = __('Notify admin at frontend upload.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, admin will receive a notification by email.', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_notify';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload backend notify', 'wp-photo-album-plus');
							$desc = __('Notify admin at backend upload.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, admin will receive a notification by email.', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_backend_notify';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max size in pixels', 'wp-photo-album-plus');
							$desc = __('Max size for height and width for front-end uploads.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the maximum size. 0 is unlimited','wppa', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_fronend_maxsize';
							$html1 = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Home after Upload', 'wp-photo-album-plus');
							$desc = __('After successfull front-end upload, go to the home page.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_home_after_upload';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fe alert', 'wp-photo-album-plus');
							$desc = __('Show alertbox on successful front-end upload/create.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_fe_alert';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'upload';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							wppa_setting_subheader( 'C', '2', __('Admin Functionality restrictions for non administrators' , 'wp-photo-album-plus') );

							$name = __('Alt thumb is restricted', 'wp-photo-album-plus');
							$desc = __('Using <b>alt thumbsize</b> is a restricted action.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: alt thumbsize can not be set in album admin by users not having admin rights.', 'wp-photo-album-plus'));
							$slug = 'wppa_alt_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,thumb';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Link is restricted', 'wp-photo-album-plus');
							$desc = __('Using <b>Link to</b> is a restricted action.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: Link to: can not be set in album admin by users not having admin rights.', 'wp-photo-album-plus'));
							$slug = 'wppa_link_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,link';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('CoverType is restricted', 'wp-photo-album-plus');
							$desc = __('Changing <b>Cover Type</b> is a restricted action.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: Cover Type: can not be set in album admin by users not having admin rights.', 'wp-photo-album-plus'));
							$slug = 'wppa_covertype_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,cover';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo order# is restricted', 'wp-photo-album-plus');
							$desc = __('Changing <b>Photo sort order #</b> is a restricted action.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: Photo sort order #: can not be set in photo admin by users not having admin rights.', 'wp-photo-album-plus'));
							$slug = 'wppa_porder_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Change source restricted', 'wp-photo-album-plus');
							$desc = __('Changing the import source dir requires admin rights.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the imput source for importing photos and albums is restricted to user role administrator.', 'wp-photo-album-plus'));
							$slug = 'wppa_chgsrc_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Extended status restricted', 'wp-photo-album-plus');
							$desc = __('Setting status other than pending or publish requires admin rights.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_ext_status_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo description restricted', 'wp-photo-album-plus');
							$desc = __('Edit photo description requires admin rights.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_desc_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Update photofiles restricted', 'wp-photo-album-plus');
							$desc = __('Re-upload files requires admin rights', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_reup_is_restricted';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							wppa_setting_subheader('D', '2', __('Miscellaneous limiting settings', 'wp-photo-album-plus'));

							$name = __('Owners only', 'wp-photo-album-plus');
							$desc = __('Limit edit album access to the album owners only.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, non-admin users can edit their own albums only.', 'wp-photo-album-plus'));
							$slug = 'wppa_owner_only';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload Owners only', 'wp-photo-album-plus');
							$desc = __('Limit uploads to the album owners only.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, users can upload to their own own albums and --- public --- only.', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_owner_only';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,upload';
							wppa_setting($slug, '1.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Uploader Edit', 'wp-photo-album-plus');
							$desc = __('Allow the uploader to edit the photo info', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, any logged in user that has upload rights and uploads an image has the capability to edit the photo information.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Note: This may be AFTER moderation!!', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_edit';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,upload';
							wppa_setting($slug, '2.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Uploader Moderate Comment', 'wp-photo-album-plus');
							$desc = __('The owner of the photo can moderate the photos comments.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting requires "Uploader edit" to be enabled also.', 'wp-photo-album-plus'));
							$slug = 'wppa_owner_moderate_comment';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,upload,comment';
							wppa_setting($slug, '2.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload memory check frontend', 'wp-photo-album-plus');
							$desc = __('Disable uploading photos that are too large.', 'wp-photo-album-plus');
							$help = esc_js(__('To prevent out of memory crashes during upload and possible database inconsistencies, uploads can be prevented if the photos are too big.', 'wp-photo-album-plus'));
							$slug = 'wppa_memcheck_frontend';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,upload';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload memory check admin', 'wp-photo-album-plus');
							$desc = __('Disable uploading photos that are too large.', 'wp-photo-album-plus');
							$help = esc_js(__('To prevent out of memory crashes during upload and possible database inconsistencies, uploads can be prevented if the photos are too big.', 'wp-photo-album-plus'));
							$slug = 'wppa_memcheck_admin';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,upload';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment captcha', 'wp-photo-album-plus');
							$desc = __('Use a simple calculate captcha on comments form.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_captcha';
							$opts = array(__('All users', 'wp-photo-album-plus'), __('Logged out users', 'wp-photo-album-plus'), __('No users', 'wp-photo-album-plus'));
							$vals = array('all', 'logout', 'none');
							$html1 = wppa_select($slug, $opts, $vals);
							$clas = 'wppa_comment_';
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,comment';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Spam lifetime', 'wp-photo-album-plus');
							$desc = __('Delete spam comments when older than.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_spam_maxage';
							$options = array(	__('--- off ---', 'wp-photo-album-plus'),
												sprintf( _n('%d minute', '%d minutes', '10', 'wp-photo-album-plus'), '10'),
												sprintf( _n('%d minute', '%d minutes', '30', 'wp-photo-album-plus'), '30'),
												sprintf( _n('%d hour', '%d hours', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d day', '%d days', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d week', '%d weeks', '1', 'wp-photo-album-plus'), '1'),
											);

							$values = array(	'none',
												'600',
												'1800',
												'3600',
												'86400',
												'604800',
											);

							$html1 = wppa_select($slug, $options, $values);
							$clas = 'wppa_comment_';
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system,comment';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Avoid duplicates', 'wp-photo-album-plus');
							$desc = __('Prevent the creation of duplicate photos.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: uploading, importing, copying or moving photos to other albums will be prevented when the desitation album already contains a photo with the same filename.', 'wp-photo-album-plus'));
							$slug = 'wppa_void_dups';
							$html1 = wppa_checkbox($slug);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Blacklist user', 'wp-photo-album-plus');
							$desc = __('Set the status of all the users photos to \'pending\'.', 'wp-photo-album-plus');
							$help = esc_js(__('Set the status of all the users photos to \'pending\'.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Also inhibits further uploads.', 'wp-photo-album-plus'));
							$slug = 'wppa_blacklist_user';
					//		$users = wppa_get_users();	// Already known
							$blacklist = get_option( 'wppa_black_listed_users', array() );

							if ( wppa_get_user_count() <= wppa_opt( 'max_users' ) ) {
								$options = array( __('--- select a user to blacklist ---', 'wp-photo-album-plus') );
								$values = array( '0' );
								foreach ( $users as $usr ) {
									if ( ! wppa_user_is( 'administrator', $usr['ID'] ) ) {	// an administrator can not be blacklisted
										if ( ! in_array( $usr['user_login'], $blacklist ) ) {	// skip already on blacklist
											$options[] = $usr['display_name'].' ('.$usr['user_login'].')';
											$values[]  = $usr['user_login'];
										}
									}
								}
								$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
								$html1 = wppa_select($slug, $options, $values, $onchange);
								$html2 = '';
							}
							else { // over 1000 users
								$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
								$html1 = __( 'User login name <b>( case sensitive! )</b>:' , 'wp-photo-album-plus');
								$html2 = wppa_input ( $slug, '150px', '', '', $onchange );
							}
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting(false, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Unblacklist user', 'wp-photo-album-plus');
							$desc = __('Set the status of all the users photos to \'publish\'.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_un_blacklist_user';
							$blacklist = get_option( 'wppa_black_listed_users', array() );
							$options = array( __('--- select a user to unblacklist ---', 'wp-photo-album-plus') );
							$values = array( '0' );
							foreach ( $blacklist as $usr ) {
								$u = get_user_by( 'login', $usr );
								$options[] = $u->display_name.' ('.$u->user_login.')';
								$values[]  = $u->user_login;
							}
							$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
							$html1 = wppa_select($slug, $options, $values, $onchange);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting(false, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo owner change', 'wp-photo-album-plus');
							$desc = __('Administrators can change photo owner', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_photo_owner_change';
							$html1 = wppa_checkbox( $slug );
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Super user', 'wp-photo-album-plus');
							$desc = __('Give these users all rights in wppa.', 'wp-photo-album-plus');
							$help = esc_js(__('This gives the user all the administrator privileges within wppa.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Make sure the user also has a role that has all the boxes ticked in Table VII-A', 'wp-photo-album-plus'));
							$slug = 'wppa_superuser_user';
					//		$users = wppa_get_users();	// Already known
							$superlist = get_option( 'wppa_super_users', array() );

							if ( wppa_get_user_count() <= wppa_opt( 'max_users' ) ) {
								$options = array( __('--- select a user to make superuser ---', 'wp-photo-album-plus') );
								$values = array( '0' );
								foreach ( $users as $usr ) {
									if ( ! wppa_user_is( 'administrator', $usr['ID'] ) ) {	// an administrator can not be made superuser
										if ( ! in_array( $usr['user_login'], $superlist ) ) {	// skip already on superlist
											$options[] = $usr['display_name'].' ('.$usr['user_login'].')';
											$values[]  = $usr['user_login'];
										}
									}
								}
								$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
								$html1 = wppa_select($slug, $options, $values, $onchange);
								$html2 = '';
							}
							else { // over 1000 users
								$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
								$html1 = __( 'User login name <b>( case sensitive! )</b>:' , 'wp-photo-album-plus');
								$html2 = wppa_input ( $slug, '150px', '', '', $onchange );
							}
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting(false, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Unsuper user', 'wp-photo-album-plus');
							$desc = __('Remove user from super user list.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_un_superuser_user';
							$superlist = get_option( 'wppa_super_users', array() );
							$options = array( __('--- select a user to unmake superuser ---', 'wp-photo-album-plus') );
							$values = array( '0' );
							foreach ( $superlist as $usr ) {
								$u = get_user_by( 'login', $usr );
								$options[] = $u->display_name.' ('.$u->user_login.')';
								$values[]  = $u->user_login;
							}
							$onchange = 'alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
							$html1 = wppa_select($slug, $options, $values, $onchange);
							$html2 = '';
							$html = array( $html1, $html2 );
							$clas = '';
							$tags = 'access,system';
							wppa_setting(false, '12', $name, $desc, $html, $help, $clas, $tags);
							?>

						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_7">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 8: Actions ?>
			<?php wppa_settings_box_header(
				'8',
				__('Table VIII:', 'wp-photo-album-plus').' '.__('Actions:', 'wp-photo-album-plus').' '.
				__('This table lists all actions that can be taken to the wppa+ system', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_8" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_8">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Specification', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Do it!', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('To Go', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_8">
							<?php
							$wppa_table = 'VIII';

						wppa_setting_subheader('A', '4', __('Harmless and reverseable actions', 'wp-photo-album-plus'));

							$name = __('Ignore concurrency', 'wp-photo-album-plus');
							$desc = __('Ignore the prevention of concurrent actions.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting is meant to recover from deadlock situations only. Use with care!', 'wp-photo-album-plus'));
							$slug = 'wppa_maint_ignore_concurrency_error';
							$html1 = wppa_checkbox( $slug );
							$html2 = '';
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Setup', 'wp-photo-album-plus');
							$desc = __('Re-initialize plugin.', 'wp-photo-album-plus');
							$help = esc_js(__('Re-initilizes the plugin, (re)creates database tables and sets up default settings and directories if required.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This action may be required to setup blogs in a multiblog (network) site as well as in rare cases to correct initilization errors.', 'wp-photo-album-plus'));
							$slug = 'wppa_setup';
							$html1 = '';
							$html2 = wppa_doit_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Backup settings', 'wp-photo-album-plus');
							$desc = __('Save all settings into a backup file.', 'wp-photo-album-plus');
							$help = esc_js(__('Saves all the settings into a backup file', 'wp-photo-album-plus'));
							$slug = 'wppa_backup';
							$html1 = '';
							$html2 = wppa_doit_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Load settings', 'wp-photo-album-plus');
							$desc = __('Restore all settings from defaults, a backup or skin file.', 'wp-photo-album-plus');
							$help = esc_js(__('Restores all the settings from the factory supplied defaults, the backup you created or from a skin file.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_skinfile';
							$slug2 = 'wppa_load_skin';
							$files = glob(WPPA_PATH.'/theme/*.skin');
							$options = false;
							$values = false;
							$options[] = __('--- set to defaults ---', 'wp-photo-album-plus');
							$values[] = 'default';
							if (is_file(WPPA_DEPOT_PATH.'/settings.bak')) {
								$options[] = __('--- restore backup ---', 'wp-photo-album-plus');
								$values[] = 'restore';
							}
							if ( count($files) ) {
								foreach ($files as $file) {
									$fname = basename($file);
									$ext = strrchr($fname, '.');
									if ( $ext == '.skin' )  {
										$options[] = $fname;
										$values[] = $file;
									}
								}
							}
							$html1 = wppa_select($slug1, $options, $values);
							$html2 = wppa_doit_button('', $slug2);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Regenerate', 'wp-photo-album-plus');
							$desc = __('Regenerate all thumbnails.', 'wp-photo-album-plus');
							$help = esc_js(__('Regenerate all thumbnails.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_regen_thumbs_skip_one';
							$slug2 = 'wppa_regen_thumbs';
							$html1 = wppa_ajax_button(__('Skip one', 'wp-photo-album-plus'), $slug1, '0', true );
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,thumb';
							wppa_setting(false, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Rerate', 'wp-photo-album-plus');
							$desc = __('Recalculate ratings.', 'wp-photo-album-plus');
							$help = esc_js(__('This function will recalculate all mean photo ratings from the ratings table.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You may need this function after the re-import of previously exported photos', 'wp-photo-album-plus'));
							$slug2 = 'wppa_rerate';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,rating';
							wppa_setting(false, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Lost and found', 'wp-photo-album-plus');
							$desc = __('Find "lost" photos.', 'wp-photo-album-plus');
							$help = esc_js(__('This function will attempt to find lost photos.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_cleanup';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Recuperate', 'wp-photo-album-plus');
							$desc = 'Recuperate IPTC and EXIF data from photos in WPPA+.';
							$help = esc_js(__('This action will attempt to find and register IPTC and EXIF data from photos in the WPPA+ system.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_recup';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remake Index Albums', 'wp-photo-album-plus');
							$desc = __('Remakes the index database table for albums.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_remake_index_albums';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,search';
							wppa_setting(false, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remake Index Photos', 'wp-photo-album-plus');
							$desc = __('Remakes the index database table for photos.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_remake_index_photos';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,search';
							wppa_setting(false, '9', $name, $desc, $html, $help, $clas, $tags);

							$fs = get_option('wppa_file_system');
							if ( ! $fs ) {	// Fix for wp delete_option bug
								$fs = 'flat';
								wppa_update_option('wppa_file_system', 'flat');
							}
							if ( $fs == 'flat' || $fs == 'to-tree' ) {
								$name = __('Convert to tree', 'wp-photo-album-plus');
								$desc = __('Convert filesystem to tree structure.', 'wp-photo-album-plus');
							}
							if ( $fs == 'tree' || $fs == 'to-flat' ) {
								$name = __('Convert to flat', 'wp-photo-album-plus');
								$desc = __('Convert filesystem to flat structure.', 'wp-photo-album-plus');
							}
							$help = esc_js(__('If you want to go back to a wppa+ version prior to 5.0.16, you MUST convert to flat first.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_file_system';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remake', 'wp-photo-album-plus');
							$desc = __('Remake the photofiles from photo sourcefiles.', 'wp-photo-album-plus');
							$help = esc_js(__('This action will remake the fullsize images, thumbnail images, and will refresh the iptc and exif data for all photos where the source is found in the corresponding album sub-directory of the source directory.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_remake_skip_one';
							$slug2 = 'wppa_remake';
							$html1 = wppa_ajax_button(__('Skip one', 'wp-photo-album-plus'), $slug1, '0', true );
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Orientation only', 'wp-photo-album-plus');
							$desc = __('Remake non standard orientated photos only.',  'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_remake_orientation_only';
							$html1 = '';
							$html2 = wppa_checkbox( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '11a', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Recalc sizes', 'wp-photo-album-plus');
							$desc = __('Recalculate photosizes and save to db.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_comp_sizes';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Renew album crypt', 'wp-photo-album-plus');
							$desc = __('Renew album encrcryption codes.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_crypt_albums';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Renew photo crypt', 'wp-photo-album-plus');
							$desc = __('Renew photo encrcryption codes.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_crypt_photos';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '14', $name, $desc, $html, $help, $clas, $tags);


						wppa_setting_subheader('B', '4', __('Clearing and other irreverseable actions', 'wp-photo-album-plus'));

							$name = __('Clear ratings', 'wp-photo-album-plus');
							$desc = __('Reset all ratings.', 'wp-photo-album-plus');
							$help = esc_js(__('WARNING: If checked, this will clear all ratings in the system!', 'wp-photo-album-plus'));
							$slug = 'wppa_rating_clear';
							$html1 = '';
							$html2 = wppa_ajax_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,rating';
							wppa_setting(false, '1.0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Clear viewcounts', 'wp-photo-album-plus');
							$desc = __('Reset all viewcounts.', 'wp-photo-album-plus');
							$help = esc_js(__('WARNING: If checked, this will clear all viewcounts in the system!', 'wp-photo-album-plus'));
							$slug = 'wppa_viewcount_clear';
							$html1 = '';
							$html2 = wppa_ajax_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Reset IPTC', 'wp-photo-album-plus');
							$desc = __('Clear all IPTC data.', 'wp-photo-album-plus');
							$help = esc_js(__('WARNING: If checked, this will clear all IPTC data in the system!', 'wp-photo-album-plus'));
							$slug = 'wppa_iptc_clear';
							$html1 = '';
							$html2 = wppa_ajax_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Reset EXIF', 'wp-photo-album-plus');
							$desc = __('Clear all EXIF data.', 'wp-photo-album-plus');
							$help = esc_js(__('WARNING: If checked, this will clear all EXIF data in the system!', 'wp-photo-album-plus'));
							$slug = 'wppa_exif_clear';
							$html1 = '';
							$html2 = wppa_ajax_button('', $slug);
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Apply New Photodesc', 'wp-photo-album-plus');
							$desc = __('Apply New photo description on all photos in the system.', 'wp-photo-album-plus');
							$help = esc_js('Puts the content of Table IX-D5 in all photo descriptions.');
							$slug2 = 'wppa_apply_new_photodesc_all';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Append to photodesc', 'wp-photo-album-plus');
							$desc = __('Append this text to all photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js('Appends a space character and the given text to the description of all photos.');
							$help .= '\n\n'.esc_js('First edit the text to append, click outside the edit window and wait for the green checkmark to appear. Then click the Start! button.');
							$slug1 = 'wppa_append_text';
							$slug2 = 'wppa_append_to_photodesc';
							$html1 = wppa_input( $slug1, '200px' );
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remove from photodesc', 'wp-photo-album-plus');
							$desc = __('Remove this text from all photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js('Removes all occurrencies of the given text from the description of all photos.');
							$help .= '\n\n'.esc_js('First edit the text to remove, click outside the edit window and wait for the green checkmark to appear. Then click the Start! button.');
							$slug1 = 'wppa_remove_text';
							$slug2 = 'wppa_remove_from_photodesc';
							$html1 = wppa_input( $slug1, '200px' );
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remove empty albums', 'wp-photo-album-plus');
							$desc = __('Removes albums that are not used.', 'wp-photo-album-plus');
							$help = esc_js('Removes all albums that have no photos and no sub albums in it.');
							$slug2 = 'wppa_remove_empty_albums';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,album';
							wppa_setting(false, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remove file-ext', 'wp-photo-album-plus');
							$desc = __('Remove possible file extension from photo name.', 'wp-photo-album-plus');
							$help = esc_js(__('This may be required for old photos, uploaded when the option in Table IX-D3 was not yet available/selected.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_remove_file_extensions';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Re-add file-ext', 'wp-photo-album-plus');
							$desc = __('Revert the <i>Remove file-ext</i> action.', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_readd_file_extensions';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '8.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Watermark all', 'wp-photo-album-plus');
							$desc = __('Apply watermark according to current settings to all photos.', 'wp-photo-album-plus');
							$help = esc_js(__('See Table IX_F for the current watermark settings', 'wp-photo-album-plus'));
							$slug2 = 'wppa_watermark_all';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,water';
							wppa_setting(false, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Create all autopages', 'wp-photo-album-plus');
							$desc = __('Create all the pages to display slides individually.', 'wp-photo-album-plus');
							$help = esc_js(__('See also Table IV-A10.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Make sure you have a custom menu and the "Automatically add new top-level pages to this menu" box UNticked!!', 'wp-photo-album-plus'));
							$slug2 = 'wppa_create_all_autopages';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,page';
							wppa_setting(false, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Delete all autopages', 'wp-photo-album-plus');
							$desc = __('Delete all the pages to display slides individually.', 'wp-photo-album-plus');
							$help = esc_js(__('See also Table IV-A10.', 'wp-photo-album-plus'));
							$help .= '';
							$slug2 = 'wppa_delete_all_autopages';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,page';
							wppa_setting(false, '10.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Leading zeroes', 'wp-photo-album-plus');
							$desc = __('If photoname numeric, add leading zeros', 'wp-photo-album-plus');
							$help = esc_js(__('You can extend the name with leading zeros, so alphabetic sort becomes equal to numeric sort order.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_zero_numbers';
							$slug2 = 'wppa_leading_zeros';
							$html1 = wppa_input( $slug1, '50px' ).__('Total chars', 'wp-photo-album-plus');
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Add GPX tag', 'wp-photo-album-plus');
							$desc = __('Make sure photos with gpx data have a Gpx tag', 'wp-photo-album-plus');
							$help = '';
							$slug2 = 'wppa_add_gpx_tag';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting(false, '12', $name, $desc, $html, $help, $clas, $tags);

							if ( function_exists( 'ewww_image_optimizer') ) {
								$name = __('Optimize files', 'wp-photo-album-plus');
								$desc = __('Optimize with EWWW image optimizer', 'wp-photo-album-plus');
								$help = '';
								$slug2 = 'wppa_optimize_ewww';
								$html1 = '';
								$html2 = wppa_maintenance_button( $slug2 );
								$html3 = wppa_status_field( $slug2 );
								$html4 = wppa_togo_field( $slug2 );
								$html = array($html1, $html2, $html3, $html4);
								$clas = '';
								$tags = 'system';
								wppa_setting(false, '13', $name, $desc, $html, $help, $clas, $tags);
							}

							$name = __('Edit tag', 'wp-photo-album-plus');
							$desc = __('Globally change a tagname.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_tag_to_edit';
							$slug2 = 'wppa_new_tag_value';
							$slug3 = 'wppa_edit_tag';
							$tags = wppa_get_taglist();
							$opts = array(__('-select a tag-', 'wp-photo-album-plus'));
							$vals = array( '' );
							if ( $tags ) foreach( array_keys( $tags ) as $tag ) {
								$opts[] = $tag;
								$vals[] = $tag;
							}
							$html1 = '<div><small style="float:left;margin-right:5px;" >'.__('Tag:', 'wp-photo-album-plus').'</small>'.wppa_select( $slug1, $opts, $vals ).'</div>';
							$html2 = '<div style="clear:both" ><small style="float:left;margin-right:5px;" >'.__('Change to:', 'wp-photo-album-plus').'</small>'.wppa_edit( $slug2, trim( get_option( $slug2 ), ',' ), '100px' ).'</div>';
							$html3 = wppa_maintenance_button( $slug3 );
							$html4 = wppa_status_field( $slug3 );
							$html5 = wppa_togo_field( $slug3 );
							$html = array( $html1 . '<br />' . $html2, $html3, $html4, $html5 );
							$clas = '';
							$tags = 'system,meta';
							wppa_setting( false, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Synchronize Cloudinary', 'wp-photo-album-plus');
							$desc = __('Removes/adds images in the cloud.', 'wp-photo-album-plus');
							$help = esc_js(__('Removes old images and verifies/adds new images to Cloudinary.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('See Table IX-K4.7 for the configured lifetime.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_sync_cloud';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = 'cloudinary';
							$tags = 'system';
							wppa_setting(false, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fix tags', 'wp-photo-album-plus');
							$desc = __('Make sure photo tags format is uptodate', 'wp-photo-album-plus');
							$help = esc_js(__('Fixes tags to be conform current database rules.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_sanitize_tags';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fix cats', 'wp-photo-album-plus');
							$desc = __('Make sure album cats format is uptodate', 'wp-photo-album-plus');
							$help = esc_js(__('Fixes cats to be conform current database rules.', 'wp-photo-album-plus'));
							$slug2 = 'wppa_sanitize_cats';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '17', $name, $desc, $html, $help, $clas, $tags);

/*
							$name = __('Test proc');
							$desc = __('For OpaJaap only');
							$help = '';
							$slug2 = 'wppa_test_proc';
							$html1 = '';
							$html2 = wppa_maintenance_button( $slug2 );
							$html3 = wppa_status_field( $slug2 );
							$html4 = wppa_togo_field( $slug2 );
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '99', $name, $desc, $html, $help, $clas, $tags);

/**/
						wppa_setting_subheader('C', '4', __('Listings', 'wp-photo-album-plus'));

							$name = __('List Logfile', 'wp-photo-album-plus');
							$desc = __('Show the content of wppa+ (error) log.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_errorlog_purge';
							$slug2 = 'wppa_list_errorlog';
							$html1 = wppa_ajax_button(__('Purge logfile', 'wp-photo-album-plus'), $slug1, '0', true );
							$html2 = wppa_popup_button( $slug2 );
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('List Ratings', 'wp-photo-album-plus');
							$desc = __('Show the most recent ratings.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_list_rating';
							$html1 = '';
							$html2 = wppa_popup_button( $slug2 );
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,rating';
							wppa_setting(false, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('List Index', 'wp-photo-album-plus');
							$desc = __('Show the content if the index table.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_list_index_display_start';
							$slug2 = 'wppa_list_index';
							$html1 = '<small style="float:left;">'.__('Start at text:', 'wp-photo-album-plus').'</small>'.wppa_input( $slug1, '150px' );
							$html2 = wppa_popup_button( $slug2 );
							$html3 = '';
							$html4 = '';
							$clas = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,search';
							wppa_setting(false, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('List active sessions', 'wp-photo-album-plus');
							$desc = __('Show the content of the sessions table.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = '';
							$slug2 = 'wppa_list_session';
							$html1 = '';
							$html2 = wppa_popup_button( $slug2 );
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system';
							wppa_setting(false, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('List comments', 'wp-photo-album-plus');
							$desc = __('Show the content of the comments table.', 'wp-photo-album-plus');
							$help = '';
							$slug1 = 'wppa_list_comments_by';
							$slug2 = 'wppa_list_comments';
							$opts = array( 'Email', 'Name', 'Timestamp' );
							$vals = array( 'email', 'name', 'timestamp' );
							$html1 = '<small style="float:left;">'.__('Order by:', 'wp-photo-album-plus').'</small>'.wppa_select($slug1, $opts, $vals);
							$html2 = wppa_popup_button( $slug2 );
							$html3 = '';
							$html4 = '';
							$html = array($html1, $html2, $html3, $html4);
							$clas = '';
							$tags = 'system,comment';
							wppa_setting(false, '5', $name, $desc, $html, $help, $clas, $tags);

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_8">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Specification', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Do it!', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('To Go', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 9: Miscellaneous ?>
			<?php wppa_settings_box_header(
				'9',
				__('Table IX:', 'wp-photo-album-plus').' '.__('Miscellaneous:', 'wp-photo-album-plus').' '.
				__('This table lists all settings that do not fit into an other table', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_9" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_9">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_9">
							<?php
							$wppa_table = 'IX';

						wppa_setting_subheader( 'A', '1', __( 'Internal engine related settings' , 'wp-photo-album-plus') );
							{
							$name = __('WPPA+ Filter priority', 'wp-photo-album-plus');
							$desc = __('Sets the priority of the wppa+ content filter.', 'wp-photo-album-plus');
							$help = esc_js(__('If you encounter conflicts with the theme or other plugins, increasing this value sometimes helps. Use with great care!', 'wp-photo-album-plus'));
							$slug = 'wppa_filter_priority';
							$html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Do_shortcode priority', 'wp-photo-album-plus');
							$desc = __('Sets the priority of the do_shortcode() content filter.', 'wp-photo-album-plus');
							$help = esc_js(__('If you encounter conflicts with the theme or other plugins, increasing this value sometimes helps. Use with great care!', 'wp-photo-album-plus'));
							$slug = 'wppa_shortcode_priority';
							$html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('WPPA shortcode at Filter priority', 'wp-photo-album-plus');
							$desc = __('Execute shortcode expansion on filter priority in posts and pages.', 'wp-photo-album-plus');
							$help = esc_js(__('Use to fix certain layout problems', 'wp-photo-album-plus'));
							$slug = 'wppa_shortcode_at_priority';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('WPPA shortcode at Filter priority widget', 'wp-photo-album-plus');
							$desc = __('Execute shortcode expansion on filter priority in widgets.', 'wp-photo-album-plus');
							$help = esc_js(__('Use to fix certain layout problems', 'wp-photo-album-plus'));
							$slug = 'wppa_shortcode_at_priority_widget';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('JPG image quality', 'wp-photo-album-plus');
							$desc = __('The jpg quality when photos are downsized', 'wp-photo-album-plus');
							$help = esc_js(__('The higher the number the better the quality but the larger the file', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Possible values 20..100', 'wp-photo-album-plus'));
							$slug = 'wppa_jpeg_quality';
							$html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Allow WPPA+ Debugging', 'wp-photo-album-plus');
							$desc = __('Allow the use of &amp;debug=.. in urls to this site.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: appending (?)(&)debug or (?)(&)debug=<int> to an url to this site will generate the display of special WPPA+ diagnostics, as well as php warnings', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_debug';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Auto continue', 'wp-photo-album-plus');
							$desc = __('Continue automatic after time out', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, an attempt will be made to restart an admin process when the time is out.', 'wp-photo-album-plus'));
							$slug = 'wppa_auto_continue';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max execution time', 'wp-photo-album-plus');
							$desc = __('Set max execution time here.', 'wp-photo-album-plus');
							$help = esc_js(__('If your php config does not properly set the max execution time, you can set it here. Seconds, 0 means do not change.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('A safe value is 45 in most cases', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(sprintf(__('The PHP setting max_execution_time is set to %s.', 'wp-photo-album-plus'), ini_get('max_execution_time')));
					//		$help .= '\n'.esc_js(sprintf(__('The PHP setting safe_mode is set to %s.', 'wp-photo-album-plus'), ini_get('safe_mode')));
							$slug = 'wppa_max_execution_time';
							$html = wppa_input($slug, '50px', '', 'seconds');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Feed use thumb', 'wp-photo-album-plus');
							$desc = __('Feeds use thumbnail pictures always.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_feed_use_thumb';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Enable <i>in-line</i> settings', 'wp-photo-album-plus');
							$desc = __('Activates shortcode [wppa_set][/wppa_set].', 'wp-photo-album-plus');
							$help = esc_js(__('Syntax: [wppa_set name="any wppa setting" value="new value"][/wppa_set]', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Example: [wppa_set name="wppa_thumbtype" value="masonry-v"][/wppa_set] sets the thumbnail type to vertical masonry style', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Do not forget to reset with [wppa_set][/wppa_set]', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Use with great care! There is no check on validity of values!', 'wp-photo-album-plus'));
							$slug = 'wppa_enable_shortcode_wppa_set';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Runtime modifyable settings', 'wp-photo-album-plus');
							$desc = __('The setting slugs that may be altered using [wppa_set] shortcode.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_set_shortcodes';
							$html = wppa_input($slug, '90%');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use translations', 'wp-photo-album-plus');
							$desc = __('If UNticked, wppa is in english always.', 'wp-photo-album-plus');
							$help = esc_js(__('Used to bypass the loading of any language file for wppa.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_language';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'B', '1', __( 'WPPA+ Admin related miscellaneous settings' , 'wp-photo-album-plus') );
							{
							$name = __('Allow HTML', 'wp-photo-album-plus');
							$desc = __('Allow HTML in album and photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: html is allowed. WARNING: No checks on syntax, it is your own responsability to close tags properly!', 'wp-photo-album-plus'));
							$slug = 'wppa_html';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Allow HTML custom', 'wp-photo-album-plus');
							$desc = __('Allow HTML in custom photo datafields.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: html is allowed. WARNING: No checks on syntax, it is your own responsability to close tags properly!', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_html_custom';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Check tag balance', 'wp-photo-album-plus');
							$desc = __('Check if the HTML tags are properly closed: "balanced".', 'wp-photo-album-plus');
							$help = esc_js(__('If the HTML tags in an album or a photo description are not in balance, the description is not updated, an errormessage is displayed', 'wp-photo-album-plus'));
							$slug = 'wppa_check_balance';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use WP editor', 'wp-photo-album-plus');
							$desc = __('Use the wp editor for multiline text fields.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_use_wp_editor';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Album sel hierarchic', 'wp-photo-album-plus');
							$desc = __('Show albums with (grand)parents in selection lists.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_hier_albsel';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page sel hierarchic', 'wp-photo-album-plus');
							$desc = __('Show pages with (grand)parents in selection lists.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_hier_pagesel';
							$warn = 'This setting will be effective after reload of the page';
							$html = wppa_checkbox_warn($slug, '', '', $warn);
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photo admin page size', 'wp-photo-album-plus');
							$desc = __('The number of photos per page on the <br/>Edit Album -> Manage photos and Edit Photos admin pages.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_photo_admin_pagesize';
							$options = array( __('--- off ---', 'wp-photo-album-plus'), '10', '20', '50', '100', '200');
							$values = array('0', '10', '20', '50', '100', '200');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Comment admin page size', 'wp-photo-album-plus');
							$desc = __('The number of comments per page on the Comments admin pages.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_comment_admin_pagesize';
							$options = array( __('--- off ---', 'wp-photo-album-plus'), '10', '20', '50', '100', '200');
							$values = array('0', '10', '20', '50', '100', '200');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,page,comment';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Geo info edit', 'wp-photo-album-plus');
							$desc = __('Lattitude and longitude may be edited in photo admin.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_geo_edit';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Admin bar menu admin', 'wp-photo-album-plus');
							$desc = __('Show menu on admin bar on admin pages.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_adminbarmenu_admin';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Admin bar menu frontend', 'wp-photo-album-plus');
							$desc = __('Show menu on admin bar on frontend pages.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_adminbarmenu_frontend';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Add shortcode to posts', 'wp-photo-album-plus');
							$desc = __('Add a shortcode to the end of all posts.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_add_shortcode_to_post';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Shortcode to add', 'wp-photo-album-plus');
							$desc = __('The shortcode to be added to the posts.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_shortcode_to_add';
							$html = wppa_input($slug, '300px');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('We use Scripts', 'wp-photo-album-plus');
							$desc = __('Use scripting syntax in shortcode generator.', 'wp-photo-album-plus');
							$help = esc_js(__('This setting defines if the shortcode generator outputs old style script tags or new style shortcodes.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_scripts_in_tinymce';
							$warn = esc_js('This is strongly discouraged. Using scripts in stead of shortcodes will restrict the functionality of WPPA+. Use only when you have serious conflicts in theme or with other plugins.');
							$html = wppa_checkbox_warn_on($slug, '', '', $warn);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Import page prieviews', 'wp-photo-album-plus');
							$desc = __('Show thumbnail previews in import admin page.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_import_preview';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload audiostub', 'wp-photo-album-plus');
							$desc = __('Upload a new audio stub file', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_audiostub_upload';
							$html = '<input id="my_file_element" type="file" name="file_3" style="float:left; font-size: 11px;" />';
							$html .= wppa_doit_button(__('Upload audio stub image', 'wp-photo-album-plus'), $slug, '', '31', '16');
							$clas = '';
							$tags = 'audio,upload';
							wppa_setting(false, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Confirm create', 'wp-photo-album-plus');
							$desc = __('Display confirmation dialog before creating album.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_confirm_create';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Import source root', 'wp-photo-album-plus');
							$desc = __('Specify the highest level in the filesystem where to import from', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_import_root';
							$opts = array();
							$prev = '';
							$curr = ABSPATH . 'wp-content';
							while ( $prev != $curr ) {
								$opts[] = $curr;
								$prev = $curr;
								$curr = dirname($prev);
							}
							$vals = $opts;
							$html = wppa_select($slug,$opts,$vals,'','',false,'','500');
							$clas = '';
							$tags = 'system,upload';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Allow import from WPPA+ source folders', 'wp-photo-album-plus');
							$desc = __('Only switch this on if you know what you are doing!', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_allow_import_source';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,upload';
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'C', '1', __( 'SEO related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Meta on page', 'wp-photo-album-plus');
							$desc = __('Meta tags for photos on the page.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the header of the page will contain metatags that refer to featured photos on the page in the page context.', 'wp-photo-album-plus'));
							$slug = 'wppa_meta_page';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Meta all', 'wp-photo-album-plus');
							$desc = __('Meta tags for all featured photos.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the header of the page will contain metatags that refer to all featured photo files.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('If you have many featured photos, you might wish to uncheck this item to reduce the size of the page header.', 'wp-photo-album-plus'));
							$slug = 'wppa_meta_all';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Add og meta tags', 'wp-photo-album-plus');
							$desc = __('Add og meta tags to the page header.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_og_tags_on';
							$warn = esc_js(__('Turning this off may affect the functionality of social media items in the share box that rely on open graph tags information.', 'wp-photo-album-plus'));
							$html = wppa_checkbox_warn_off($slug, '', '', $warn, false);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Image Alt attribute type', 'wp-photo-album-plus');
							$desc = __('Select kind of HTML alt="" content for images.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_alt_type';
							$options = array( __('--- none ---', 'wp-photo-album-plus'), __('photo name', 'wp-photo-album-plus'), __('name without file-ext', 'wp-photo-album-plus'), __('set in album admin', 'wp-photo-album-plus') );
							$values = array( 'none', 'fullname', 'namenoext', 'custom');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'D', '1', __( 'New Album and New Photo related miscellaneous settings' , 'wp-photo-album-plus') );
							{
							$options = array( 	__('--- off ---', 'wp-photo-album-plus'),
												sprintf( _n('%d hour', '%d hours', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d day', '%d days', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d day', '%d days', '2', 'wp-photo-album-plus'), '2'),
												sprintf( _n('%d day', '%d days', '3', 'wp-photo-album-plus'), '3'),
												sprintf( _n('%d day', '%d days', '4', 'wp-photo-album-plus'), '4'),
												sprintf( _n('%d day', '%d days', '5', 'wp-photo-album-plus'), '5'),
												sprintf( _n('%d day', '%d days', '6', 'wp-photo-album-plus'), '6'),
												sprintf( _n('%d week', '%d weeks', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d day', '%d days', '8', 'wp-photo-album-plus'), '8'),
												sprintf( _n('%d day', '%d days', '9', 'wp-photo-album-plus'), '9'),
												sprintf( _n('%d day', '%d days', '10', 'wp-photo-album-plus'), '10'),
												sprintf( _n('%d week', '%d weeks', '2', 'wp-photo-album-plus'), '2'),
												sprintf( _n('%d week', '%d weeks', '3', 'wp-photo-album-plus'), '3'),
												sprintf( _n('%d week', '%d weeks', '4', 'wp-photo-album-plus'), '4'),
												sprintf( _n('%d month', '%d months', '1', 'wp-photo-album-plus'), '1'),
											);
							$values = array( 	0,
												60*60,
												60*60*24,
												60*60*24*2,
												60*60*24*3,
												60*60*24*4,
												60*60*24*5,
												60*60*24*6,
												60*60*24*7,
												60*60*24*8,
												60*60*24*9,
												60*60*24*10,
												60*60*24*7*2,
												60*60*24*7*3,
												60*60*24*7*4,
												60*60*24*30,
											);

							$name = __('New Album', 'wp-photo-album-plus');
							$desc = __('Maximum time an album is indicated as New', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_album_newtime';
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '1.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('New Photo', 'wp-photo-album-plus');
							$desc = __('Maximum time a photo is indicated as New', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_photo_newtime';
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Modified Album', 'wp-photo-album-plus');
							$desc = __('Maximum time an album is indicated as Modified', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_album_modtime';
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '1.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Modified Photo', 'wp-photo-album-plus');
							$desc = __('Maximum time a photo is indicated as Modofied', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_photo_modtime';
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use text labels', 'wp-photo-album-plus');
							$desc = __('Use editable text for the New and Modified labels', 'wp-photo-album-plus');
							$help = esc_js(__('If UNticked, you can specify the urls for custom images to be used.', 'wp-photo-album-plus'));
							$slug = 'wppa_new_mod_label_is_text';
							$onch = 'wppaCheckCheck(\''.$slug.'\',\'nmtxt\');';
							$html = wppa_checkbox($slug,$onch);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1.5', $name, $desc, $html, $help, $clas, $tags);


							$opts = array(
											__('Red', 'wp-photo-album-plus'),
											__('Orange', 'wp-photo-album-plus'),
											__('Yellow', 'wp-photo-album-plus'),
											__('Green', 'wp-photo-album-plus'),
											__('Blue', 'wp-photo-album-plus'),
											__('Purple', 'wp-photo-album-plus'),
											__('Black/white', 'wp-photo-album-plus'),
										);
							$vals = array(
											'red',
											'orange',
											'yellow',
											'green',
											'blue',
											'purple',
											'black',
										);

							$name = __('New label', 'wp-photo-album-plus');
							$desc = __('Specify the "New" indicator details.', 'wp-photo-album-plus');
							$help = esc_js(__('If you use qTranslate, the text may be multilingual.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_new_label_text';
							$slug2 = 'wppa_new_label_color';
							$html1 = '<span style="float:left">'.__('Text', 'wp-photo-album-plus').': </span>'.wppa_input($slug1, '150px');
							$html2 = '<span style="float:left">'.__('Color', 'wp-photo-album-plus').': </span>'.wppa_select($slug2, $opts, $vals);
							$clas = 'nmtxt';
							$tags = 'system';
							wppa_setting($slug1, '1.6', $name, $desc, $html1.' '.$html2, $help, $clas, $tags);

							$name = __('Modified label', 'wp-photo-album-plus');
							$desc = __('Specify the "Modified" indicator details.', 'wp-photo-album-plus');
							$help = esc_js(__('If you use qTranslate, the text may be multilingual.', 'wp-photo-album-plus'));
							$slug1 = 'wppa_mod_label_text';
							$slug2 = 'wppa_mod_label_color';
							$html1 = '<span style="float:left">'.__('Text', 'wp-photo-album-plus').': </span>'.wppa_input($slug1, '150px');
							$html2 = '<span style="float:left">'.__('Color', 'wp-photo-album-plus').': </span>'.wppa_select($slug2, $opts, $vals);
							$clas = 'nmtxt';
							$tags = 'system';
							wppa_setting($slug1, '1.7', $name, $desc, $html1.' '.$html2, $help, $clas, $tags);

							$name = __('New label', 'wp-photo-album-plus');
							$desc = __('Specify the "New" indicator url.', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_new_label_url';
							$html = wppa_input($slug, '300px');
							$clas = '-nmtxt';
							$tags = 'system';
							wppa_setting($slug, '1.8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Modified label', 'wp-photo-album-plus');
							$desc = __('Specify the "Modified" indicator url.', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_mod_label_url';
							$html = wppa_input($slug, '300px');
							$clas = '-nmtxt';
							$tags = 'system';
							wppa_setting($slug, '1.9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Limit LasTen New', 'wp-photo-album-plus');
							$desc = __('Limits the LasTen photos to those that are \'New\', or newly modified.', 'wp-photo-album-plus');
							$help = esc_js(__('If you tick this box and configured the new photo time, you can even limit the number by the setting in Table I-F7, or set that number to an unlikely high value.', 'wp-photo-album-plus'));
							$slug = 'wppa_lasten_limit_new';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('LasTen use Modified', 'wp-photo-album-plus');
							$desc = __('Use the time modified rather than time upload for LasTen widget/shortcode.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_lasten_use_modified';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Apply Newphoto desc', 'wp-photo-album-plus');
							$desc = __('Give each new photo a standard description.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, each new photo will get the description (template) as specified in the next item.', 'wp-photo-album-plus'));
							$slug = 'wppa_apply_newphoto_desc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('New photo desc', 'wp-photo-album-plus');
							$desc = __('The description (template) to add to a new photo.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter the default description.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('If you use html, please check item A-1 of this table.', 'wp-photo-album-plus'));
							$slug = 'wppa_newphoto_description';
							$html = wppa_textarea($slug, $name);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload limit', 'wp-photo-album-plus');
							$desc = __('New albums are created with this upload limit.', 'wp-photo-album-plus');
							$help = esc_js(__('Administrators can change the limit settings in the "Edit Album Information" admin page.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('A value of 0 means: no limit.', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_limit_count';
							$html = wppa_input($slug, '50px', '', __('photos', 'wp-photo-album-plus'));
							$slug = 'wppa_upload_limit_time';
							$options = array( 	__('for ever', 'wp-photo-album-plus'),
												__('per hour', 'wp-photo-album-plus'),
												__('per day', 'wp-photo-album-plus'),
												__('per week', 'wp-photo-album-plus'),
												__('per month', 'wp-photo-album-plus'), 	// 30 days
												__('per year', 'wp-photo-album-plus'));	// 364 days
							$values = array( '0', '3600', '86400', '604800', '2592000', '31449600');
							$html .= wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,upload';
							wppa_setting(false, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default parent', 'wp-photo-album-plus');
							$desc = __('The parent album of new albums.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_default_parent';
							$opts = array( __('--- none ---', 'wp-photo-album-plus'), __('--- separate ---', 'wp-photo-album-plus') );
							$vals = array( '0', '-1');
							$albs = $wpdb->get_results( "SELECT `id`, `name` FROM`" . WPPA_ALBUMS . "` ORDER BY `name`", ARRAY_A );
							if ( $albs ) {
								foreach ( $albs as $alb ) {
									$opts[] = __(stripslashes($alb['name']), 'wp-photo-album-plus');
									$vals[] = $alb['id'];
								}
							}
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '7.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default parent always', 'wp-photo-album-plus');
							$desc = __('The parent album of new albums is always the default, except for administrators.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_default_parent_always';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '7.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Show album full', 'wp-photo-album-plus');
							$desc = __('Show the Upload limit reached message if appropriate.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_show_album_full';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Grant an album', 'wp-photo-album-plus');
							$desc = __('Create an album for each user logging in.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_grant_an_album';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Grant album name', 'wp-photo-album-plus');
							$desc = __('The name to be used for the album.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_grant_name';
							$opts = array(__('Login name', 'wp-photo-album-plus'), __('Display name', 'wp-photo-album-plus'), __('Id', 'wp-photo-album-plus'), __('Firstname Lastname', 'wp-photo-album-plus'));
							$vals = array('login', 'display', 'id', 'firstlast');
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Grant parent', 'wp-photo-album-plus');
							$desc = __('The parent album of the auto created albums.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_grant_parent';
							$opts = array( __('--- none ---', 'wp-photo-album-plus'), __('--- separate ---', 'wp-photo-album-plus') );
							$vals = array( '0', '-1');
							$albs = $wpdb->get_results( "SELECT `id`, `name` FROM`" . WPPA_ALBUMS . "` ORDER BY `name`", ARRAY_A );
							if ( $albs ) {
								foreach ( $albs as $alb ) {
									$opts[] = __(stripslashes($alb['name']), 'wp-photo-album-plus');
									$vals[] = $alb['id'];
								}
							}
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'system,album';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max user albums', 'wp-photo-album-plus');
							$desc = __('The max number of albums a user can create.', 'wp-photo-album-plus');
							$help = esc_js(__('The maximum number of albums a user can create when he is not admin and owner only is active', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('A number of 0 means No limit', 'wp-photo-album-plus'));
							$slug = 'wppa_max_albums';
							$html = wppa_input($slug, '50px', '', 'albums');
							$clas = '';
							$tags = 'system,count,album';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default photo name', 'wp-photo-album-plus');
							$desc = __('Select the way the name of a new uploaded photo should be determined.', 'wp-photo-album-plus');
							$help = esc_js('If you select an IPTC Tag and it is not found, the filename will be used instead.');
							$slug = 'wppa_newphoto_name_method';
							$opts = array( 	__('Filename', 'wp-photo-album-plus'),
											__('Filename without extension', 'wp-photo-album-plus'),
											__('IPTC Tag 2#005 (Graphic name)', 'wp-photo-album-plus'),
											__('IPTC Tag 2#120 (Caption)', 'wp-photo-album-plus'),
											__('No name at all', 'wp-photo-album-plus')
										);
							$vals = array( 'filename', 'noext', '2#005', '2#120', 'none' );
							$html = wppa_select($slug, $opts, $vals);
							$clas = '';
							$tags = 'system,meta,album';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default coverphoto', 'wp-photo-album-plus');
							$desc = __('Name of photofile to become cover image', 'wp-photo-album-plus');
							$help = esc_js(__('If you name a photofile like this setting before upload, it will become the coverimage automaticly.', 'wp-photo-album-plus'));
							$slug = 'wppa_default_coverimage_name';
							$html = wppa_input($slug, '150px');
							$clas = '';
							$tags = 'system,thumb,album';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Copy Timestamp', 'wp-photo-album-plus');
							$desc = __('Copy timestamp when copying photo.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the copied photo is not "new"', 'wp-photo-album-plus'));
							$slug = 'wppa_copy_timestamp';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '15.0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Copy Owner', 'wp-photo-album-plus');
							$desc = __('Copy the owner when copying photo.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_copy_owner';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '15.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('FE Albums public', 'wp-photo-album-plus');
							$desc = __('Frontend created albums are --- public ---', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_frontend_album_public';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,access,album';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Optimize files', 'wp-photo-album-plus');
							$desc = __('Optimize image files right after upload/import', 'wp-photo-album-plus');
							$help = esc_js(__('This option requires the plugin EWWW Image Optimizer to be activated', 'wp-photo-album-plus'));
							$slug = 'wppa_optimize_new';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Default album linktype', 'wp-photo-album-plus');
							$desc = __('The album linktype for new albums', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_default_album_linktype';
							$opts = array( 	__('the sub-albums and thumbnails', 'wp-photo-album-plus'),
											__('the sub-albums', 'wp-photo-album-plus'),
											__('the thumbnails', 'wp-photo-album-plus'),
											__('the album photos as slideshow', 'wp-photo-album-plus'),
											__('no link at all', 'wp-photo-album-plus')
										);

							$vals = array( 	'content',
											'albums',
											'thumbs',
											'slide',
											'none'
										);
							$html = wppa_select($slug, $opts, $vals);
							wppa_setting($slug, '18', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'E', '1', __( 'Search Albums and Photos related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Search page', 'wp-photo-album-plus');
							$desc = __('Display the search results on page.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the page to be used to display search results. The page MUST contain %%wppa%% or [wppa][/wppa].', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You may give it the title "Search results" or something alike.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Or you ou may use the standard page on which you display the generic album.', 'wp-photo-album-plus'));
							$slug = 'wppa_search_linkpage';
							wppa_verify_page($slug);
							$query = "SELECT ID, post_title, post_content FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_title ASC";
							$pages = $wpdb->get_results($query, ARRAY_A);
							$options = false;
							$values = false;
							$options[] = __('--- Please select a page ---', 'wp-photo-album-plus');
							$values[] = '0';
							if ($pages) {
								if ( wppa_switch('wppa_hier_pagesel') ) $pages = wppa_add_parents($pages);
								else {	// Just translate
									foreach ( array_keys($pages) as $index ) {
										$pages[$index]['post_title'] = __(stripslashes($pages[$index]['post_title']), 'wp-photo-album-plus');
									}
								}
								$pages = wppa_array_sort($pages, 'post_title');
								foreach ($pages as $page) {
									if ( strpos($page['post_content'], '%%wppa%%') !== false || strpos($page['post_content'], '[wppa') !== false ) {
										$options[] = __($page['post_title'], 'wp-photo-album-plus');
										$values[] = $page['ID'];
									}
									else {
										$options[] = '|'.__($page['post_title'], 'wp-photo-album-plus').'|';
										$values[] = $page['ID'];
									}
								}
							}
							$clas = '';
							$tags = 'system,search,page';
							$html = wppa_select($slug, $options, $values, '', '', true);
							wppa_setting(false, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Exclude separate', 'wp-photo-album-plus');
							$desc = __('Do not search \'separate\' albums.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, albums (and photos in them) that have the parent set to --- separate --- will be excluded from being searched.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Except when you start searching in a \'saparate\' album, with the "search in current section" box ticked.', 'wp-photo-album-plus'));
							$slug = 'wppa_excl_sep';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Include tags', 'wp-photo-album-plus');
							$desc = __('Do also search the photo tags.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, the tags of the photo will also be searched.', 'wp-photo-album-plus'));
							$slug = 'wppa_search_tags';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search,meta';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Include categories', 'wp-photo-album-plus');
							$desc = __('Do also search the album categories.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, the categories of the album will also be searched.', 'wp-photo-album-plus'));
							$slug = 'wppa_search_cats';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search,meta';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Include comments', 'wp-photo-album-plus');
							$desc = __('Do also search the comments on photos.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, the comments of the photos will also be searched.', 'wp-photo-album-plus'));
							$slug = 'wppa_search_comments' ;
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search,comment';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Photos only', 'wp-photo-album-plus');
							$desc = __('Search for photos only.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, only photos will be searched for.', 'wp-photo-album-plus'));
							$slug = 'wppa_photos_only';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);
/*	per 5.5.0 indexed search only
							$name = __('Indexed search');
							$desc = __('Searching uses index db table.');
							$help = '';
							$slug = 'wppa_indexed_search';
							$onchange = 'wppaCheckIndexSearch()';
							$html = wppa_checkbox($slug, $onchange);
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);
*/
							$name = __('Max albums found', 'wp-photo-album-plus');
							$desc = __('The maximum number of albums to be displayed.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_search_albums';
							$html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system,search,count';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max photos found', 'wp-photo-album-plus');
							$desc = __('The maximum number of photos to be displayed.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_max_search_photos';
							$html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system,search,count';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tags OR only', 'wp-photo-album-plus');
							$desc = __('No and / or buttons', 'wp-photo-album-plus');
							$help = esc_js(__('Hide the and/or radiobuttons and do the or method in the multitag widget and shortcode.', 'wp-photo-album-plus'));
							$slug = 'wppa_tags_or_only';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Tags add Inverse', 'wp-photo-album-plus');
							$desc = __('Add a checkbox to invert the selection.', 'wp-photo-album-plus');
							$help = esc_js(__('Adds an Invert (NOT) checkbox on the multitag widget and shortcode.', 'wp-photo-album-plus'));
							$slug = 'wppa_tags_not_on';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '10.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Floating searchtoken', 'wp-photo-album-plus');
							$desc = __('A match need not start at the first char.', 'wp-photo-album-plus');
							$help = esc_js(__('A match is found while searching also when the entered token is somewhere in the middle of a word.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This works in indexed search only!', 'wp-photo-album-plus'));
							$slug = 'wppa_wild_front';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Search results display', 'wp-photo-album-plus');
							$desc = __('Select the way the search results should be displayed.', 'wp-photo-album-plus');
							$help = esc_js(__('If you select anything different from "Albums and thumbnails", "Photos only" is assumed (Table IX-E6).', 'wp-photo-album-plus'));
							$slug = 'wppa_search_display_type';
							$opts = array( __('Albums and thumbnails', 'wp-photo-album-plus'), __('Slideshow', 'wp-photo-album-plus'), __('Slideonly slideshow', 'wp-photo-album-plus') );
							$vals = array( 'content', 'slide', 'slideonly' );
							$html = wppa_select( $slug, $opts, $vals);
							$clas = '';
							$tags = 'system,search,layout';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Name max length', 'wp-photo-album-plus');
							$desc = __('Max length of displayed photonames in supersearch selectionlist', 'wp-photo-album-plus');
							$help = esc_js(__('To limit the length of the selectionlist, enter the number of characters to show.', 'wp-photo-album-plus'));
							$slug = 'wppa_ss_name_max';
							$html = $html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Text max length', 'wp-photo-album-plus');
							$desc = __('Max length of displayed photo text in supersearch selectionlist', 'wp-photo-album-plus');
							$help = esc_js(__('To limit the length of the selectionlist, enter the number of characters to show.', 'wp-photo-album-plus'));
							$slug = 'wppa_ss_text_max';
							$html = $html = wppa_input($slug, '50px');
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Search toptext', 'wp-photo-album-plus');
							$desc = __('The text at the top of the search box.', 'wp-photo-album-plus');
							$help = esc_js(__('May contain unfiltered HTML.', 'wp-photo-album-plus'));
							$slug = 'wppa_search_toptext';
							$html = wppa_textarea($slug, $name);
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Section search text', 'wp-photo-album-plus');
							$desc = __('The labeltext at the checkbox.', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_search_in_section';
							$html = wppa_input($slug, '300px;');
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Results search text', 'wp-photo-album-plus');
							$desc = __('The labeltext at the checkbox.', 'wp-photo-album-plus');
							$help = ' ';
							$slug = 'wppa_search_in_results';
							$html = wppa_input($slug, '300px;');
							$clas = '';
							$tags = 'system,search';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'F', '1', __( 'Watermark related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Watermark', 'wp-photo-album-plus');
							$desc = __('Enable the application of watermarks.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, photos can be watermarked during upload / import.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_on';
							$onchange = 'wppaCheckWatermark()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'water,upload';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);


							$name = __('Watermark file', 'wp-photo-album-plus');
							$desc = __('The default watermarkfile to be used.', 'wp-photo-album-plus');
							$help = esc_js(__('Watermark files are of type png and reside in', 'wp-photo-album-plus') . ' ' . WPPA_UPLOAD_URL . '/watermarks/');
							$help .= '\n\n'.esc_js(__('A suitable watermarkfile typically consists of a transparent background and a black text or drawing.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__(sprintf('The watermark image will be overlaying the photo with %s%% transparency.', (100-wppa_opt( 'watermark_opacity' ))), 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('You may also select one of the textual watermark types at the bottom of the selection list.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_file';
							$html = '<select style="float:left; font-size:11px; height:20px; margin:0 4px 0 0; padding:0; " id="wppa_watermark_file" onchange="wppaAjaxUpdateOptionValue(\'wppa_watermark_file\', this)" >' . wppa_watermark_file_select('default') . '</select>';
							$html .= '<img id="img_wppa_watermark_file" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';
							$html .= '<span style="float:left; margin-left:12px;" >'.__('position:', 'wp-photo-album-plus').'</span><select style="float:left; font-size:11px; height:20px; margin:0 0 0 20px; padding:0; "  id="wppa_watermark_pos" onchange="wppaAjaxUpdateOptionValue(\'wppa_watermark_pos\', this)" >' . wppa_watermark_pos_select('default') . '</select>';
							$html .= '<img id="img_wppa_watermark_pos" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting(false, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload watermark', 'wp-photo-album-plus');
							$desc = __('Upload a new watermark file', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_watermark_upload';
							$html = '<input id="my_file_element" type="file" name="file_1" style="float:left; font-size: 11px;" />';
							$html .= wppa_doit_button(__('Upload watermark image', 'wp-photo-album-plus'), $slug, '', '31', '16');
							$clas = 'wppa_watermark';
							$tags = 'water,upload';
							wppa_setting(false, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Watermark opacity image', 'wp-photo-album-plus');
							$desc = __('You can set the intensity of image watermarks here.', 'wp-photo-album-plus');
							$help = esc_js(__('The higher the number, the intenser the watermark. Value must be > 0 and <= 100.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_opacity';
							$html = wppa_input($slug, '50px', '', '%');
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Textual watermark style', 'wp-photo-album-plus');
							$desc = __('The way the textual watermarks look like', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_textual_watermark_type';
							$clas = 'wppa_watermark';
							$sopts = array( __('TV subtitle style', 'wp-photo-album-plus'), __('White text on black background', 'wp-photo-album-plus'), __('Black text on white background', 'wp-photo-album-plus'), __('Reverse TV style (Utopia)', 'wp-photo-album-plus'), __('White on transparent background', 'wp-photo-album-plus'), __('Black on transparent background', 'wp-photo-album-plus') );
							$svals = array( 'tvstyle', 'whiteonblack', 'blackonwhite', 'utopia', 'white', 'black' );
							$font = wppa_opt( 'textual_watermark_font' );
							$onchange = 'wppaCheckFontPreview()';
							$html = wppa_select($slug, $sopts, $svals, $onchange);
							$preview = '<img style="background-color:#777;" id="wm-type-preview" src="" />';
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '6', $name, $desc, $html.' '.$preview, $help, $clas);

							$name = __('Predefined watermark text', 'wp-photo-album-plus');
							$desc = __('The text to use when --- pre-defined --- is selected.', 'wp-photo-album-plus');
							$help = esc_js(__('You may use the following keywords:', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('w#site, w#owner, w#name, w#filename', 'wp-photo-album-plus'));
							$slug = 'wppa_textual_watermark_text';
							$html = wppa_textarea($slug, $name);
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Textual watermark font', 'wp-photo-album-plus');
							$desc = __('The font to use with textusl watermarks.', 'wp-photo-album-plus');
							$help = esc_js(__('Except for the system font, are font files of type ttf and reside in', 'wp-photo-album-plus') . ' ' . WPPA_UPLOAD_URL . '/fonts/');
							$slug = 'wppa_textual_watermark_font';
							$fopts = array( 'System' );
							$fvals = array( 'system' );
							$style = wppa_opt( 'textual_watermark_type' );
							$fonts = glob( WPPA_UPLOAD_PATH . '/fonts/*.ttf' );
							sort($fonts);
							foreach ( $fonts as $font ) {
								$f = basename($font);
								$f = preg_replace('/\.[^.]*$/', '', $f);
								$F = strtoupper(substr($f,0,1)).substr($f,1);
								$fopts[] = $F;
								$fvals[] = $f;
							}
							$onchange = 'wppaCheckFontPreview()';
							$html = wppa_select($slug, $fopts, $fvals, $onchange);
							$preview = '<img style="background-color:#777;" id="wm-font-preview" src="" />';
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '8', $name, $desc, $html.' '.$preview, $help, $clas);

							foreach ( array_keys( $sopts ) as $skey ) {
								foreach ( array_keys( $fopts ) as $fkey ) {
									wppa_create_textual_watermark_file( array( 'content' => '---preview---', 'font' => $fvals[$fkey], 'text' => $sopts[$skey], 'style' => $svals[$skey], 'filebasename' => $svals[$skey].'-'.$fvals[$fkey] ) );
									wppa_create_textual_watermark_file( array( 'content' => '---preview---', 'font' => $fvals[$fkey], 'text' => $fopts[$fkey], 'style' => $svals[$skey], 'filebasename' => $fvals[$fkey].'-'.$svals[$skey] ) );
								}
							}

							$name = __('Textual watermark font size', 'wp-photo-album-plus');
							$desc = __('You can set the size of the truetype fonts only.', 'wp-photo-album-plus');
							$help = esc_js(__('System font can have size 1,2,3,4 or 5, in some stoneage fontsize units. Any value > 5 will be treated as 5.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Truetype fonts can have any positive integer size, if your PHPs GD version is 1, in pixels, in GD2 in points.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('It is unclear howmany pixels a point is...', 'wp-photo-album-plus'));
							$slug = 'wppa_textual_watermark_size';
							$html = wppa_input($slug, '50px', '', 'points');
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Upload watermark font', 'wp-photo-album-plus');
							$desc = __('Upload a new watermark font file', 'wp-photo-album-plus');
							$help = esc_js(__('Upload truetype fonts (.ttf) only, and test if they work on your server platform.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_font_upload';
							$html = '<input id="my_file_element" type="file" name="file_2" style="float:left; font-size: 11px;" />';
							$html .= wppa_doit_button(__('Upload TrueType font', 'wp-photo-album-plus'), $slug, '', '31', '16');
							$clas = 'wppa_watermark';
							$tags = 'water,upload';
							wppa_setting(false, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Watermark opacity text', 'wp-photo-album-plus');
							$desc = __('You can set the intensity of a text watermarks here.', 'wp-photo-album-plus');
							$help = esc_js(__('The higher the number, the intenser the watermark. Value must be > 0 and <= 100.', 'wp-photo-album-plus'));
							$slug = 'wppa_watermark_opacity_text';
							$html = wppa_input($slug, '50px', '', '%');
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Preview', 'wp-photo-album-plus');
							$desc = __('A real life preview. To update: refresh the page.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_watermark_preview';
							$id = $wpdb->get_var( "SELECT `id` FROM `".WPPA_PHOTOS."` ORDER BY RAND() LIMIT 1" );
							$tr = floor( 127 * ( 100 - wppa_opt( 'watermark_opacity_text' ) ) / 100 );
							$args = array( 'id' => $id, 'content' => '---predef---', 'pos' => 'cencen', 'url' => true, 'width' => '1000', 'height' => '400', 'transp' => $tr );
							$html = '<div style="text-align:center; max-width:400px; overflow:hidden; background-image:url('.WPPA_UPLOAD_URL.'/fonts/turkije.jpg);" ><img src="'.wppa_create_textual_watermark_file( $args ).'?ver='.rand(0, 4711).'" /></div><div style="clear:both;"></div>';
							$clas = 'wppa_watermark';
							$tags = 'water';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Watermark thumbnails', 'wp-photo-album-plus');
							$desc = __('Watermark also the thumbnail image files.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_watermark_thumbs';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_watermark';
							$tags = 'water,thumb';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'G', '1', __( 'Slideshow elements sequence order settings' , 'wp-photo-album-plus') );
							{
							if ( wppa_switch('wppa_split_namedesc') ) {
								$indexopt = wppa_opt( 'slide_order_split' );
								$indexes  = explode(',', $indexopt);
								$names    = array(
									__('StartStop', 'wp-photo-album-plus'),
									__('SlideFrame', 'wp-photo-album-plus'),
									__('Name', 'wp-photo-album-plus'),
									__('Desc', 'wp-photo-album-plus'),
									__('Custom', 'wp-photo-album-plus'),
									__('Rating', 'wp-photo-album-plus'),
									__('FilmStrip', 'wp-photo-album-plus'),
									__('Browsebar', 'wp-photo-album-plus'),
									__('Comments', 'wp-photo-album-plus'),
									__('IPTC data', 'wp-photo-album-plus'),
									__('EXIF data', 'wp-photo-album-plus'),
									__('Share box', 'wp-photo-album-plus')
									);
								$enabled  = '<span style="color:green; float:right;">( '.__('Enabled', 'wp-photo-album-plus');
								$disabled = '<span style="color:orange; float:right;">( '.__('Disabled', 'wp-photo-album-plus');
								$descs = array(
									__('Start/Stop & Slower/Faster navigation bar', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_startstop_navigation') ? $enabled : $disabled ) . ' II-B1 )</span>',
									__('The Slide Frame', 'wp-photo-album-plus') . '<span style="float:right;">'.__('( Always )', 'wp-photo-album-plus').'</span>',
									__('Photo Name Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_full_name') ? $enabled : $disabled ) .' II-B5 )</span>',
									__('Photo Description Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_full_desc') ? $enabled : $disabled ) .' II-B6 )</span>',
									__('Custom Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_custom_on') ? $enabled : $disabled ).' II-B14 )</span>',
									__('Rating Bar', 'wp-photo-album-plus') . ( wppa_switch('wppa_rating_on') ? $enabled : $disabled ).' II-B7 )</span>',
									__('Film Strip with embedded Start/Stop and Goto functionality', 'wp-photo-album-plus') . ( wppa_switch('wppa_filmstrip') ? $enabled : $disabled ).' II-B3 )</span>',
									__('Browse Bar with Photo X of Y counter', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_browse_navigation') ? $enabled : $disabled ).' II-B2 )</span>',
									__('Comments Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_comments') ? $enabled : $disabled ).' II-B10 )</span>',
									__('IPTC box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_iptc') ? $enabled : $disabled ).' II-B17 )</span>',
									__('EXIF box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_exif') ? $enabled : $disabled ).' II-B18 )</span>',
									__('Social media share box', 'wp-photo-album-plus') . ( wppa_switch('wppa_share_on') ? $enabled : $disabled ).' II-C1 )</span>'
									);
								$i = '0';
								while ( $i < '12' ) {
									$name = $names[$indexes[$i]];
									$desc = $descs[$indexes[$i]];
									$html = $i == '0' ? '&nbsp;' : wppa_doit_button(__('Move Up', 'wp-photo-album-plus'), 'wppa_moveup', $i);
									$help = '';
									$slug = 'wppa_slide_order';
									$clas = '';
									$tags = 'slide,layout';
									wppa_setting($slug, $indexes[$i]+1 , $name, $desc, $html, $help, $clas, $tags);
									$i++;
								}
							}
							else {
								$indexopt = wppa_opt( 'slide_order' );
								$indexes  = explode(',', $indexopt);
								$names    = array(
									__('StartStop', 'wp-photo-album-plus'),
									__('SlideFrame', 'wp-photo-album-plus'),
									__('NameDesc', 'wp-photo-album-plus'),
									__('Custom', 'wp-photo-album-plus'),
									__('Rating', 'wp-photo-album-plus'),
									__('FilmStrip', 'wp-photo-album-plus'),
									__('Browsebar', 'wp-photo-album-plus'),
									__('Comments', 'wp-photo-album-plus'),
									__('IPTC data', 'wp-photo-album-plus'),
									__('EXIF data', 'wp-photo-album-plus'),
									__('Share box', 'wp-photo-album-plus')
									);
								$enabled  = '<span style="color:green; float:right;">( '.__('Enabled', 'wp-photo-album-plus');
								$disabled = '<span style="color:orange; float:right;">( '.__('Disabled', 'wp-photo-album-plus');
								$descs = array(
									__('Start/Stop & Slower/Faster navigation bar', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_startstop_navigation') ? $enabled : $disabled ) . ' II-B1 )</span>',
									__('The Slide Frame', 'wp-photo-album-plus') . '<span style="float:right;">'.__('( Always )', 'wp-photo-album-plus').'</span>',
									__('Photo Name & Description Box', 'wp-photo-album-plus') . ( ( wppa_switch('wppa_show_full_name') || wppa_switch('wppa_show_full_desc') ) ? $enabled : $disabled ) .' II-B5,6 )</span>',
									__('Custom Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_custom_on') ? $enabled : $disabled ).' II-B14 )</span>',
									__('Rating Bar', 'wp-photo-album-plus') . ( wppa_switch('wppa_rating_on') ? $enabled : $disabled ).' II-B7 )</span>',
									__('Film Strip with embedded Start/Stop and Goto functionality', 'wp-photo-album-plus') . ( wppa_switch('wppa_filmstrip') ? $enabled : $disabled ).' II-B3 )</span>',
									__('Browse Bar with Photo X of Y counter', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_browse_navigation') ? $enabled : $disabled ).' II-B2 )</span>',
									__('Comments Box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_comments') ? $enabled : $disabled ).' II-B10 )</span>',
									__('IPTC box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_iptc') ? $enabled : $disabled ).' II-B17 )</span>',
									__('EXIF box', 'wp-photo-album-plus') . ( wppa_switch('wppa_show_exif') ? $enabled : $disabled ).' II-B18 )</span>',
									__('Social media share box', 'wp-photo-album-plus') . ( wppa_switch('wppa_share_on') ? $enabled : $disabled ).' II-C1 )</span>'
									);
								$i = '0';
								while ( $i < '11' ) {
									$name = $names[$indexes[$i]];
									$desc = $descs[$indexes[$i]];
									$html = $i == '0' ? '&nbsp;' : wppa_doit_button(__('Move Up', 'wp-photo-album-plus'), 'wppa_moveup', $i);
									$help = '';
									$slug = 'wppa_slide_order';
									$clas = '';
									$tags = 'slide,layout';
									wppa_setting($slug, $indexes[$i]+1 , $name, $desc, $html, $help, $clas, $tags);
									$i++;
								}
							}

							$name = __('Swap Namedesc', 'wp-photo-album-plus');
							$desc = __('Swap the order sequence of name and description', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_swap_namedesc';
							$html = wppa_checkbox($slug);
							$clas = 'swap_namedesc';
							$tags = 'slide,layout,meta';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Split Name and Desc', 'wp-photo-album-plus');
							$desc = __('Put Name and Description in separate boxes', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_split_namedesc';
							$html = wppa_checkbox($slug,'alert(\''.__('Please reload this page after the green checkmark appears!', 'wp-photo-album-plus').'\');wppaCheckSplitNamedesc();');
							$clas = '';
							$tags = 'slide,layout,meta';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);
							}
						wppa_setting_subheader( 'H', '1', __( 'Source file management and other upload/import settings and actions.' , 'wp-photo-album-plus') );
							{
							$name = __('Keep sourcefiles admin', 'wp-photo-album-plus');
							$desc = __('Keep the original uploaded and imported photo files.', 'wp-photo-album-plus');
							$help = esc_js(__('The files will be kept in a separate directory with subdirectories for each album', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('These files can be used to update the photos used in displaying in wppa+ and optionally for downloading original, un-downsized images.', 'wp-photo-album-plus'));
							$slug = 'wppa_keep_source_admin';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Keep sourcefiles frontend', 'wp-photo-album-plus');
							$desc = __('Keep the original frontend uploaded photo files.', 'wp-photo-album-plus');
							$help = esc_js(__('The files will be kept in a separate directory with subdirectories for each album', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('These files can be used to update the photos used in displaying in wppa+ and optionally for downloading original, un-downsized images.', 'wp-photo-album-plus'));
							$slug = 'wppa_keep_source_frontend';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Source directory', 'wp-photo-album-plus');
							$desc = __('The path to the directory where the original photofiles will be saved.', 'wp-photo-album-plus');
							$help = esc_js(__('You may change the directory path, but it can not be an url.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The parent of the directory that you enter here must exist and be writable.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The directory itsself will be created if it does not exist yet.', 'wp-photo-album-plus'));
							$slug = 'wppa_source_dir';
							$html = wppa_input($slug, '300px');
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Keep sync', 'wp-photo-album-plus');
							$desc = __('Keep source synchronously with wppa system.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, photos that are deleted from wppa, will also be removed from the sourcefiles.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Also, copying or moving photos to different albums, will also copy/move the sourcefiles.', 'wp-photo-album-plus'));
							$slug = 'wppa_keep_sync';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Remake add', 'wp-photo-album-plus');
							$desc = __('Photos will be added from the source pool', 'wp-photo-album-plus');
							$help = esc_js(__('If checked: If photo files are found in the source directory that do not exist in the corresponding album, they will be added to the album.', 'wp-photo-album-plus'));
							$slug = 'wppa_remake_add';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Save IPTC data', 'wp-photo-album-plus');
							$desc = __('Store the iptc data from the photo into the iptc db table', 'wp-photo-album-plus');
							$help = esc_js(__('You will need this if you enabled the display of iptc data in Table II-B17 or if you use it in the photo descriptions.', 'wp-photo-album-plus'));
							$slug = 'wppa_save_iptc';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Save EXIF data', 'wp-photo-album-plus');
							$desc = __('Store the exif data from the photo into the exif db table', 'wp-photo-album-plus');
							$help = esc_js(__('You will need this if you enabled the display of exif data in Table II-B18 or if you use it in the photo descriptions.', 'wp-photo-album-plus'));
							$slug = 'wppa_save_exif';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Max EXIF tag array size', 'wp-photo-album-plus');
							$desc = __('Truncate array tags to ...', 'wp-photo-album-plus');
							$help = esc_js(__('A value of 0 disables this feature', 'wp-photo-album-plus'));
							$slug = 'wppa_exif_max_array_size';
							$html = wppa_input($slug, '40px', '', __('elements', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'system,meta';
							wppa_setting($slug, '9', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Import Create page', 'wp-photo-album-plus');
							$desc = __('Create wp page when a directory to album is imported.', 'wp-photo-album-plus');
							$help = esc_js(__('As soon as an album is created when a directory is imported, a wp page is made that displays the album content.', 'wp-photo-album-plus'));
							$slug = 'wppa_newpag_create';
							$onchange = 'wppaCheckNewpag()';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '10', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page content', 'wp-photo-album-plus');
							$desc = __('The content of the page. Must contain <b>w#album</b>', 'wp-photo-album-plus');
							$help = esc_js(__('The content of the page. Note: it must contain w#album. This will be replaced by the album number in the generated shortcode.', 'wp-photo-album-plus'));
							$slug = 'wppa_newpag_content';
							$clas = 'wppa_newpag';
							$html = wppa_input($slug, '90%');
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '11', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page type', 'wp-photo-album-plus');
							$desc = __('Select the type of page to create.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_newpag_type';
							$clas = 'wppa_newpag';
							$options = array(__('Page', 'wp-photo-album-plus'), __('Post', 'wp-photo-album-plus'));
							$values = array('page', 'post');
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '12', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Page status', 'wp-photo-album-plus');
							$desc = __('Select the initial status of the page.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_newpag_status';
							$clas = 'wppa_newpag';
							$options = array(__('Published', 'wp-photo-album-plus'), __('Draft', 'wp-photo-album-plus'));
							$values = array('publish', 'draft');	// 'draft' | 'publish' | 'pending'| 'future' | 'private'
							$html = wppa_select($slug, $options, $values);
							$clas = '';
							$tags = 'system,page';
							wppa_setting($slug, '13', $name, $desc, $html, $help, $clas, $tags);

							if ( ! is_multisite() || WPPA_MULTISITE_GLOBAL ) {
								$name = __('Permalink root', 'wp-photo-album-plus');
								$desc = __('The name of the root for the photofile ermalink structure.', 'wp-photo-album-plus');
								$help = esc_js(__('Choose a convenient name like "albums" or so; this will be the name of a folder inside .../wp-content/. Make sure you choose a unique name', 'wp-photo-album-plus'));
								$slug = 'wppa_pl_dirname';
								$clas = '';
								$tags = 'system';
								$html = wppa_input($slug, '150px');
								wppa_setting($slug, '14', $name, $desc, $html, $help, $clas, $tags);
							}

							$name = __('Import parent check', 'wp-photo-album-plus');
							$desc = __('On import dirs to albums: keep dir tree as albums.', 'wp-photo-album-plus');
							$help = esc_js(__('Untick only if all your albums have unique names. Then: additional photos may be ftp\'d to toplevel depot subdirs.', 'wp-photo-album-plus'));
							$slug = 'wppa_import_parent_check';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,upload';
							wppa_setting($slug, '15', $name, $desc, $html, $help, $clas, $tags);

							$name = __('IPTC need utf8 conversion', 'wp-photo-album-plus');
							$desc = __('This setting converts iso to utf8 in iptc text.', 'wp-photo-album-plus');
							$help = esc_js(__('Untick if you have funny characters in iptc data', 'wp-photo-album-plus') );
							$slug = 'wppa_iptc_need_utf8';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,upload,meta';
							wppa_setting($slug, '16', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Keep dir to album files', 'wp-photo-album-plus');
							$desc = __('Keep imported files after dir to album import', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_keep_import_files';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,upload';
							wppa_setting($slug, '17', $name, $desc, $html, $help, $clas, $tags);

							}
						wppa_setting_subheader( 'J', '1', __( 'Other plugins related settings' , 'wp-photo-album-plus') );
							{
							$name = __('Foreign shortcodes general', 'wp-photo-album-plus');
							$desc = __('Enable foreign shortcodes in album names, albums desc and photo names', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_allow_foreign_shortcodes_general';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,album,cover,meta,slide';
							wppa_setting($slug, '0', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Foreign shortcodes fullsize', 'wp-photo-album-plus');
							$desc = __('Enable the use of non-wppa+ shortcodes in fullsize photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, you can use shortcodes from other plugins in the description of photos.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The shortcodes will be expanded in the descriptions of fullsize images.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You will most likely need also to check Table IX-A1 (Allow HTML).', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_foreign_shortcodes';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,slide,meta';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Foreign shortcodes thumbnails', 'wp-photo-album-plus');
							$desc = __('Enable the use of non-wppa+ shortcodes in thumbnail photo descriptions.', 'wp-photo-album-plus');
							$help = esc_js(__('When checked, you can use shortcodes from other plugins in the description of photos.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The shortcodes will be expanded in the descriptions of thumbnail images.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('You will most likely need also to check Table IX-A1 (Allow HTML).', 'wp-photo-album-plus'));
							$slug = 'wppa_allow_foreign_shortcodes_thumbs';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system,thumb,meta';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Lightbox keyname', 'wp-photo-album-plus');
							$desc = __('The identifier of lightbox.', 'wp-photo-album-plus');
							$help = esc_js(__('If you use a lightbox plugin that uses rel="lbox-id" you can enter the lbox-id here.', 'wp-photo-album-plus'));
							$slug = 'wppa_lightbox_name';
							$html = wppa_input($slug, '100px');
							$clas = 'wppa_alt_lightbox';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('myCRED / Cube Points: Comment', 'wp-photo-album-plus');
							$desc = __('Number of points for a comment', 'wp-photo-album-plus');
							$help = esc_js(__('This setting requires the plugin myCRED or Cube Points', 'wp-photo-album-plus'));
							$slug = 'wppa_cp_points_comment';
							$html = wppa_input($slug, '50px', '', __('points per comment', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'system,comment';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							$name = __('myCRED / Cube Points: Rating', 'wp-photo-album-plus');
							$desc = __('Number of points for a rating vote', 'wp-photo-album-plus');
							$help = esc_js(__('This setting requires the plugin myCRED or Cube Points', 'wp-photo-album-plus'));
							$slug = 'wppa_cp_points_rating';
							$html = wppa_input($slug, '50px', '', __('points per vote', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'system,rating';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('myCRED / Cube Points: Upload', 'wp-photo-album-plus');
							$desc = __('Number of points for a successfull frontend upload', 'wp-photo-album-plus');
							$help = esc_js(__('This setting requires the plugin myCRED or Cube Points', 'wp-photo-album-plus'));
							$slug = 'wppa_cp_points_upload';
							$html = wppa_input($slug, '50px', '', __('points per upload', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'system,upload';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use SCABN', 'wp-photo-album-plus');
							$desc = __('Use the wppa interface to Simple Cart & Buy Now plugin.', 'wp-photo-album-plus');
							$help = esc_js(__('If checked, the shortcode to use for the "add to cart" button in photo descriptions is [cart ...]', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('as opposed to [scabn ...] for the original scabn "add to cart" button.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('The shortcode for the check-out page is still [scabn]', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('The arguments are the same, the defaults are: name = photoname, price = 0.01.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Supplying the price should be sufficient; supply a name only when it differs from the photo name.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This shortcode handler will also work with Ajax enabled.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Using this interface makes sure that the item urls and callback action urls are correct.', 'wp-photo-album-plus'));
							$slug = 'wppa_use_scabn';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '7', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Use CM Tooltip Glossary', 'wp-photo-album-plus');
							$desc = __('Use plugin CM Tooltip Glossary on photo and album descriptions.', 'wp-photo-album-plus');
							$help = esc_js(__('You MUST set Table IV-A13: Defer javascript, also if you do not want this plugin to act on album and photo descriptions!', 'wp-photo-album-plus'));
							$slug = 'wppa_use_CMTooltipGlossary';
							$html = wppa_checkbox($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '8', $name, $desc, $html, $help, $clas, $tags);

							}
							wppa_setting_subheader( 'K', '1', __('External services related settings and actions.', 'wp-photo-album-plus'));
							{
							$name = __('QR Code widget size', 'wp-photo-album-plus');
							$desc = __('The size of the QR code display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_qr_size';
							$html = wppa_input($slug, '50px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('QR color', 'wp-photo-album-plus');
							$desc = __('The display color of the qr code (dark)', 'wp-photo-album-plus');
							$help = esc_js(__('This color MUST be given in hexadecimal format!', 'wp-photo-album-plus'));
							$slug = 'wppa_qr_color';
							$html = wppa_input($slug, '100px', '', '', "checkColor('".$slug."')") . wppa_color_box($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('QR background color', 'wp-photo-album-plus');
							$desc = __('The background color of the qr code (light)', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_qr_bgcolor';
							$html = wppa_input($slug, '100px', '', '', "checkColor('".$slug."')") . wppa_color_box($slug);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('CDN Service', 'wp-photo-album-plus');
							$desc = __('Select a CDN Service you want to use.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_cdn_service';
							$opts = array(__('--- none ---', 'wp-photo-album-plus'), 'Cloudinary', __('Cloudinary in maintenance mode', 'wp-photo-album-plus') );
							$vals = array('', 'cloudinary', 'cloudinarymaintenance');
							$onch = 'wppaCheckCDN()';
							$html = wppa_select($slug, $opts, $vals, $onch);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							if ( PHP_VERSION_ID >= 50300 ) {

								$name = __('Cloud name', 'wp-photo-album-plus');
								$desc = '';
								$help = '';
								$slug = 'wppa_cdn_cloud_name';
								$html = wppa_input($slug, '500px');
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4.1', $name, $desc, $html, $help, $clas, $tags);

								$name = __('API key', 'wp-photo-album-plus');
								$desc = '';
								$help = '';
								$slug = 'wppa_cdn_api_key';
								$html = wppa_input($slug, '500px');
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4.2', $name, $desc, $html, $help, $clas, $tags);

								$name = __('API secret', 'wp-photo-album-plus');
								$desc = '';
								$help = '';
								$slug = 'wppa_cdn_api_secret';
								$html = wppa_input($slug, '500px');
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4.3', $name, $desc, $html, $help, $clas, $tags);

								$name = __('Delete all', 'wp-photo-album-plus');
								$desc = '<span style="color:red;" >'.__('Deletes them all !!!', 'wp-photo-album-plus').'</span>';
								$help = '';
								$slug = 'wppa_delete_all_from_cloudinary';
								$html = wppa_doit_button('', $slug);
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting(false, '4.5', $name, $desc, $html, $help, $clas, $tags);

								$name = __('Delete derived images', 'wp-photo-album-plus');
								$desc = '<span style="color:red;" >'.__('Deletes all derived images !!!', 'wp-photo-album-plus').'</span>';
								$help = '';
								$slug = 'wppa_delete_derived_from_cloudinary';
								$html = wppa_doit_button('', $slug);
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting(false, '4.6', $name, $desc, $html, $help, $clas, $tags);

								$name = __('Max lifetime', 'wp-photo-album-plus');
								$desc = __('Old images from local server, new images from Cloudinary.', 'wp-photo-album-plus');
								$help = esc_js(__('If NOT set to Forever: You need to run Table VIII-B15 on a regular basis.', 'wp-photo-album-plus'));
								$slug = 'wppa_max_cloud_life';
								$opts = array( 	__('Forever', 'wp-photo-album-plus'),
												sprintf( _n('%d day', '%d days', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d week', '%d weeks', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d month', '%d months', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d month', '%d months', '2', 'wp-photo-album-plus'), '2'),
												sprintf( _n('%d month', '%d months', '3', 'wp-photo-album-plus'), '3'),
												sprintf( _n('%d month', '%d months', '6', 'wp-photo-album-plus'), '6'),
												sprintf( _n('%d month', '%d months', '9', 'wp-photo-album-plus'), '9'),
												sprintf( _n('%d year', '%d years', '1', 'wp-photo-album-plus'), '1'),
												sprintf( _n('%d month', '%d months', '18', 'wp-photo-album-plus'), '18'),
												sprintf( _n('%d year', '%d years', '2', 'wp-photo-album-plus'), '2'),
												);
								$vals = array(	0,
												24*60*60,
												7*24*60*60,
												31*24*60*60,
												61*24*60*60,
												92*24*60*60,
												183*24*60*60,
												274*24*60*60,
												365*24*60*60,
												548*24*60*60,
												730*24*60*60,
												);

								$onch = '';
								$html = wppa_select($slug, $opts, $vals, $onch);
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4.7', $name, $desc, $html, $help, $clas, $tags);

								$name = __('Cloudinary usage', 'wp-photo-album-plus');
								if ( function_exists( 'wppa_get_cloudinary_usage' ) ) {
									$data = wppa_get_cloudinary_usage();
									if ( is_array( $data ) ) {
										$desc = '<style type="text/css" scoped>table, tbody, tr, td { margin:0; padding:0; border:none; font-size: 9px; line-height: 11px; } td { height:11px; }</style>';
										$desc .= '<table style="margin:0;padding:0;border:none:" ><tbody>';
										foreach ( array_keys( $data ) as $i ) {
											$item = $data[$i];
											if ( is_array( $item ) ) {
												$desc .= 	'<tr>' .
																'<td>' . $i . '</td>';
																foreach ( array_keys( $item ) as $j ) {
																	if ( $j == 'used_percent' ) {
																		$color = 'green';
																		if ( $item[$j] > 80.0 ) $color = 'orange';
																		if ( $item[$j] > 95.0 ) $color = 'red';
												$desc .= 				'<td>' . $j . ': <span style="color:' . $color . '">' . $item[$j] . '</span></td>';
																	}
																	else {
												$desc .= 				'<td>' . $j . ': ' . $item[$j] . '</td>';
																	}
																}
												$desc .= 	'</tr>';
											}
											else {
												$desc .= 	'<tr>' .
																'<td>' . $i . '</td>' .
																'<td>' . $item . '</td>' .
																'<td></td>' .
																'<td></td>' .
															'</tr>';
											}
										}
										$desc .= '</tbody></table>';
									}
									else {
										$desc = __('Cloudinary usage data not available', 'wp-photo-album-plus');
									}
								}
								else {
									$desc = __('Cloudinary routines not installed.', 'wp-photo-album-plus');
								}
								$help = '';
								$html = '';
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4.8', $name, $desc, $html, $help, $clas, $tags);

							}
							else {

								$name = __('Cloudinary', 'wp-photo-album-plus');
								$desc = __('<span style="color:red;">Requires at least PHP version 5.3</span>', 'wp-photo-album-plus');
								$help = '';
								$html = '';
								$clas = 'cloudinary';
								$tags = 'system';
								wppa_setting($slug, '4', $name, $desc, $html, $help, $clas, $tags);

							}

							$name = __('GPX Implementation', 'wp-photo-album-plus');
							$desc = __('The way the maps are produced.', 'wp-photo-album-plus');
							$help = esc_js(__('Select the way the maps are produced.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('When using Google maps GPX viewer plugin, you can not use Ajax (Table IV-A1)', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('When using WPPA+ Embedded code, you can use Ajax, but there are less display options.', 'wp-photo-album-plus'));
							$slug = 'wppa_gpx_implementation';
							$opts = array( __('--- none ---', 'wp-photo-album-plus'), __('WPPA+ Embedded code', 'wp-photo-album-plus'), __('Google maps GPX viewer plugin', 'wp-photo-album-plus') );
							$vals = array( 'none', 'wppa-plus-embedded', 'google-maps-gpx-viewer' );
							$onch = 'wppaCheckGps();alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
							$html = wppa_select($slug, $opts, $vals, $onch);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '5', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Map height', 'wp-photo-album-plus');
							$desc = __('The height of the map display.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_map_height';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa_gpx_native';
							$tags = 'system';
							wppa_setting($slug, '5.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Google maps API key', 'wp-photo-album-plus');
							$desc = __('Enter your Google maps api key here if you have one.', 'wp-photo-album-plus');
							$help = '';
							$slug = 'wppa_map_apikey';
							$html = wppa_input($slug, '200px', '');
							$clas = 'wppa_gpx_native';
							$tags = 'system';
							wppa_setting($slug, '5.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('GPX Shortcode', 'wp-photo-album-plus');
							$desc = __('The shortcode to be used for the gpx feature.', 'wp-photo-album-plus');
							$help = esc_js(__('Enter / modify the shortcode to be generated for the gpx plugin. It must contain w#lat and w#lon as placeholders for the lattitude and longitude.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('This item is required for using Google maps GPX viewer plugin only', 'wp-photo-album-plus'));
							$slug = 'wppa_gpx_shortcode';
							$html = wppa_input($slug, '500px');
							$clas = 'wppa_gpx_plugin';
							$tags = 'system';
							wppa_setting($slug, '5.3', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fotomoto', 'wp-photo-album-plus');
							$desc = __('Yes, we use Fotomoto on this site. Read the help text!', 'wp-photo-album-plus');
							$help = esc_js(__('In order to function properly:', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('1. Get yourself a Fotomoto account.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('2. Install the Fotomoto plugin, enter the "Fotomoto Site Key:" and check the "Use API Mode:" checkbox.', 'wp-photo-album-plus'));
							$help .= '\n\n'.esc_js(__('Note: Do NOT Disable the Custom box in Table II-B14.', 'wp-photo-album-plus'));
							$help .= '\n'.esc_js(__('Do NOT remove the text w#fotomoto from the Custombox ( Table II-B15 ).', 'wp-photo-album-plus'));
							$slug = 'wppa_fotomoto_on';
							$onchange = 'wppaCheckFotomoto();alert(\''.__('The page will be reloaded after the action has taken place.', 'wp-photo-album-plus').'\');wppaRefreshAfter();';
							$html = wppa_checkbox($slug, $onchange);
							$clas = '';
							$tags = 'system';
							wppa_setting($slug, '6', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fotomoto fontsize', 'wp-photo-album-plus');
							$desc = __('Fontsize for the Fotomoto toolbar.', 'wp-photo-album-plus');
							$help = esc_js(__('If you set it here, it overrules a possible setting for font-size in .FotomotoToolbarClass on the Fotomoto dashboard.', 'wp-photo-album-plus'));
							$slug = 'wppa_fotomoto_fontsize';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa_fotomoto';
							$tags = 'system';
							wppa_setting($slug, '6.1', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Hide when running', 'wp-photo-album-plus');
							$desc = __('Hide toolbar on running slideshows', 'wp-photo-album-plus');
							$help = esc_js(__('The Fotomoto toolbar will re-appear when the slidshow stops.', 'wp-photo-album-plus'));
							$slug = 'wppa_fotomoto_hide_when_running';
							$html = wppa_checkbox($slug);
							$clas = 'wppa_fotomoto';
							$tags = 'system';
							wppa_setting($slug, '6.2', $name, $desc, $html, $help, $clas, $tags);

							$name = __('Fotomoto minwidth', 'wp-photo-album-plus');
							$desc = __('Minimum width to display Fotomoto toolbar.', 'wp-photo-album-plus');
							$help = esc_js(__('The display of the Fotomoto Toolbar will be suppressed on smaller slideshows.', 'wp-photo-album-plus'));
							$slug = 'wppa_fotomoto_min_width';
							$html = wppa_input($slug, '40px', '', __('pixels', 'wp-photo-album-plus'));
							$clas = 'wppa_fotomoto';
							$tags = 'system';
							wppa_setting($slug, '6.3', $name, $desc, $html, $help, $clas, $tags);
							}
							?>

						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_9">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Setting', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 10: IPTC Configuration ?>
			<?php wppa_settings_box_header(
				'10',
				__('Table X:', 'wp-photo-album-plus').' '.__('IPTC Configuration:', 'wp-photo-album-plus').' '.
				__('This table defines the IPTC configuration', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_10" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_10">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Tag', 'wp-photo-album-plus') ?></td>
								<td></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_10">
							<?php
							$wppa_table = 'X';

							$wppa_subtable = 'Z';

							$labels = $wpdb->get_results( "SELECT * FROM `".WPPA_IPTC."` WHERE `photo` = '0' ORDER BY `tag`", ARRAY_A );
							if ( is_array( $labels ) ) {
								$i = '1';
								foreach ( $labels as $label ) {
									$name = $label['tag'];
									$desc = '';
									$help = '';
									$slug1 = 'wppa_iptc_label_'.$name;
									$slug2 = 'wppa_iptc_status_'.$name;
									$html1 = wppa_edit($slug1, $label['description']);
									$options = array(__('Display', 'wp-photo-album-plus'), __('Hide', 'wp-photo-album-plus'), __('Optional', 'wp-photo-album-plus'));
									$values = array('display', 'hide', 'option');
									$html2 = wppa_select_e($slug2, $label['status'], $options, $values);
									$html = array($html1, $html2);
									$clas = '';
									$tags = 'meta';
									wppa_setting(false, $i, $name, $desc, $html, $help, $clas, $tags);
									$i++;

								}
							}

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_10">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Tag', 'wp-photo-album-plus') ?></td>
								<td></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 11: EXIF Configuration ?>
			<?php wppa_settings_box_header(
				'11',
				__('Table XI:', 'wp-photo-album-plus').' '.__('EXIF Configuration:', 'wp-photo-album-plus').' '.
				__('This table defines the EXIF configuration', 'wp-photo-album-plus')
			); ?>

				<div id="wppa_table_11" style="display:none" >
					<table class="widefat wppa-table wppa-setting-table">
						<thead style="font-weight: bold; " class="wppa_table_11">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Tag', 'wp-photo-album-plus') ?></td>
								<td></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</thead>
						<tbody class="wppa_table_11">
							<?php
							$wppa_table = 'XI';

							$wppa_subtable = 'Z';

							if ( ! function_exists('exif_read_data') ) {
								wppa_setting_subheader('', '1', '</b><span style="color:red;">'.
									__('Function exif_read_data() does not exist. This means that <b>EXIF</b> is not enabled. If you want to use <b>EXIF</b> data, ask your hosting provider to add <b>\'--enable-exif\'</b> to the php <b>Configure Command</b>.', 'wp-photo-album-plus').
									'<b></span>');
							}

							$labels = $wpdb->get_results( "SELECT * FROM `".WPPA_EXIF."` WHERE `photo` = '0' ORDER BY `tag`", ARRAY_A);
							if ( is_array( $labels ) ) {
								$i = '1';
								foreach ( $labels as $label ) {
									$name = $label['tag'];
									$desc = '';
									$help = '';
									$slug1 = 'wppa_exif_label_'.$name;
									$slug2 = 'wppa_exif_status_'.$name;
									$html1 = wppa_edit($slug1, $label['description']);
									$options = array(__('Display', 'wp-photo-album-plus'), __('Hide', 'wp-photo-album-plus'), __('Optional', 'wp-photo-album-plus'));
									$values = array('display', 'hide', 'option');
									$html2 = wppa_select_e($slug2, $label['status'], $options, $values);
									$html = array($html1, $html2);
									$clas = '';
									$tags = 'meta';
									wppa_setting(false, $i, $name, $desc, $html, $help, $clas, $tags);
									$i++;

								}
							}

							?>
						</tbody>
						<tfoot style="font-weight: bold;" class="wppa_table_11">
							<tr>
								<td><?php _e('#', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Tag', 'wp-photo-album-plus') ?></td>
								<td></td>
								<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Status', 'wp-photo-album-plus') ?></td>
								<td><?php _e('Help', 'wp-photo-album-plus') ?></td>
							</tr>
						</tfoot>
					</table>
				</div>

			<?php // Table 12: Php configuration ?>
			<?php wppa_settings_box_header(
				'12',
				__('Table XII:', 'wp-photo-album-plus').' '.__('WPPA+ and PHP Configuration:', 'wp-photo-album-plus').' '.
				__('This table lists all WPPA+ constants and PHP server configuration parameters and is read only', 'wp-photo-album-plus')
			); ?>

			<?php
			$wppa_table = 'XII';
			$wppa_subtable = 'Z';
			?>

				<div id="wppa_table_12" style="display:none" >
		<!--		<div class="wppa_table_12" style="margin-top:20px; text-align:left; ">	-->
						<table class="widefat wppa-table wppa-setting-table">
							<thead style="font-weight: bold; " class="wppa_table_12">
								<tr>
									<td><?php _e('Name', 'wp-photo-album-plus') ?></td>
									<td><?php _e('Description', 'wp-photo-album-plus') ?></td>
									<td><?php _e('Value', 'wp-photo-album-plus') ?></td>
									<td></td>
								</tr>
							<tbody class="wppa_table_12">
								<tr style="color:#333;">
									<td>WPPA_ALBUMS</td>
									<td><small><?php _e('Albums db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_ALBUMS) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_ALBUMS . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_ALBUMS . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_ALBUMS . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_PHOTOS</td>
									<td><small><?php _e('Photos db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_PHOTOS) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_PHOTOS . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_PHOTOS . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_PHOTOS . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_RATING</td>
									<td><small><?php _e('Rating db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_RATING) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_RATING . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_RATING . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_RATING . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_COMMENTS</td>
									<td><small><?php _e('Comments db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_COMMENTS) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_COMMENTS . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_COMMENTS . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_COMMENTS . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_IPTC</td>
									<td><small><?php _e('IPTC db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_IPTC) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_IPTC . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_IPTC . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_IPTC . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_EXIF</td>
									<td><small><?php _e('EXIF db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_EXIF) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_EXIF . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_EXIF . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_EXIF . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_INDEX</td>
									<td><small><?php _e('Index db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_INDEX) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_INDEX . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_INDEX . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_INDEX . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_SESSION</td>
									<td><small><?php _e('Index db table name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_SESSION) ?></td>
									<td><?php if ( wppa_user_is( 'administrator' ) ) {
											echo 	'<a onclick="wppaExportDbTable(\'' . WPPA_SESSION . '\')" >' .
														__('Download', 'wp-photo-album-plus') . ' ' . WPPA_SESSION . '.csv' .
													'</a> ' .
													'<img id="' . WPPA_SESSION . '-spin" src="' . wppa_get_imgdir( 'wpspin.gif' ) . '" style="display:none;" />';
										} ?>
									</td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_FILE</td>
									<td><small><?php _e('Plugins main file name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_FILE) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_ABSPATH</td>
									<td><small><?php _e('ABSPATH windows proof', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo WPPA_ABSPATH ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_PATH</td>
									<td><small><?php _e('Path to plugins directory.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_PATH) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_NAME</td>
									<td><small><?php _e('Plugins directory name.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_NAME) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_URL</td>
									<td><small><?php _e('Plugins directory url.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_URL) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_UPLOAD</td>
									<td><small><?php _e('The relative upload directory.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_UPLOAD) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_UPLOAD_PATH</td>
									<td><small><?php _e('The upload directory path.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_UPLOAD_PATH) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_UPLOAD_URL</td>
									<td><small><?php _e('The upload directory url.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_UPLOAD_URL) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_DEPOT</td>
									<td><small><?php _e('The relative depot directory.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_DEPOT) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_DEPOT_PATH</td>
									<td><small><?php _e('The depot directory path.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_DEPOT_PATH) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_DEPOT_URL</td>
									<td><small><?php _e('The depot directory url.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_DEPOT_URL) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>WPPA_CONTENT_PATH</td>
									<td><small><?php _e('The path to wp-content.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(WPPA_CONTENT_PATH) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>wp_upload_dir() : ['basedir']</td>
									<td><small><?php _e('WP Base upload dir.', 'wp-photo-album-plus') ?></small></td>
									<td><?php 	$wp_uploaddir = wp_upload_dir();
												echo $wp_uploaddir['basedir']; ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>content_url()</td>
									<td><small><?php _e('WP Content url.', 'wp-photo-album-plus') ?></small></td>
									<td><?php echo(content_url()) ?></td>
									<td></td>
								</tr>
								<tr style="color:#333;">
									<td>$_SERVER['HTTP_HOST']</td>
									<td><small><?php ?></small></td>
									<td><?php echo $_SERVER['HTTP_HOST'] ?></td>
									<td></td>
								</tr>
							</tbody>
						</table>
						<p>&nbsp;</p>
						<?php wppa_phpinfo() ?>
		<!--			</div>-->
				</div>

		</form>
		<script type="text/javascript">wppaInitSettings();wppaCheckInconsistencies();</script>
		<?php echo sprintf(__('<br />Memory used on this page: %6.2f Mb.', 'wp-photo-album-plus'), memory_get_peak_usage(true)/(1024*1024)); ?>
		<?php echo sprintf(__('<br />There are %d settings and %d runtime parameters.', 'wp-photo-album-plus'), count($wppa_opt), count($wppa)); ?>
	</div>

<?php
	wppa_initialize_runtime( true );
}

function wppa_settings_box_header($id, $title) {
	echo '
		<div id="wppa_settingbox_'.$id.'" class="postbox metabox-holder" style="padding-top:0; margin-bottom:-1px; margin-top:20px; " >
			<div class="handlediv" title="Click to toggle table" onclick="wppaToggleTable('.$id.');" >
				<br>
			</div>
			<h3 class="hndle" style="cursor:pointer;" title="Click to toggle table" onclick="wppaToggleTable('.$id.');" >
				<span>'.$title.'</span>
				<br>
			</h3>
		</div>
		';
}

function wppa_setting_subheader($lbl, $col, $txt, $cls = '') {
global $wppa_subtable;
global $wppa_table;

	$wppa_subtable = $lbl;
	$colspan = $col + 3;
	echo 	'<tr class="'.$cls.'" style="background-color:#f0f0f0;" >'.
				'<td style="color:#333;"><b>'.$lbl.'</b></td>'.
				'<td title="Click to toggle subtable" onclick="wppaToggleSubTable(\''.$wppa_table.'\',\''.$wppa_subtable.'\');" colspan="'.$colspan.'" style="color:#333; cursor:pointer;" ><em><b>'.$txt.'</b></em></td>'.
			'</tr>';
}


function wppa_setting( $slug, $num, $name, $desc, $html, $help, $cls = '', $tags = '-' ) {
global $wppa_status;
global $wppa_defaults;
global $wppa_table;
global $wppa_subtable;
global $no_default;
global $wppa_opt;

	if ( is_array($slug) ) $slugs = $slug;
	else {
		$slugs = false;
		if ( $slug ) $slugs[] = $slug;
	}
	if ( is_array($html) ) $htmls = $html;
	else {
		$htmls = false;
		if ( $html ) $htmls[] = $html;
	}
	if ( strpos($num, ',') !== false ) {
		$nums = explode(',', $num);
		$nums[0] = substr($nums[0], 1);
	}
	else {
		$nums = false;
		if ( $num ) $nums[] = $num;
	}

	// Convert tags to classes
	$tagcls = wppa_tags_to_clas( $tags );

	// Build the html
	$result = "\n";
	$result .= '<tr id="'.$wppa_table.$wppa_subtable.$num.'" class="wppa-'.$wppa_table.'-'.$wppa_subtable.' '.$cls.$tagcls.' wppa-none" style="color:#333;">';
	$result .= '<td>'.$num.'</td>';
	$result .= '<td>'.$name.'</td>';
	$result .= '<td><small>'.$desc.'</small></td>';
	if ( $htmls ) foreach ( $htmls as $html ) {
		$result .= '<td>'.$html.'</td>';
	}

	if ( $help || ( defined( 'WP_DEBUG') && WP_DEBUG ) ) {
		$is_dflt = true;
		$hlp = esc_js($name).':\n\n'.$help;
		if ( ! $no_default ) {
			if ( $slugs ) {
				$hlp .= '\n\n'.__('The default for this setting is:', 'wp-photo-album-plus');
				if ( count($slugs) == 1) {
					if ( $slugs[0] != '' ) {
						$hlp .= ' '.esc_js(wppa_dflt($slugs[0]));
						if ( $wppa_opt[$slugs[0]] != $wppa_defaults[$slugs[0]] ) {
							$is_dflt = false;
						}
					}
				}
				else foreach ( array_keys($slugs) as $slugidx ) {
					if ( $slugs[$slugidx] != '' && isset($nums[$slugidx]) ) $hlp .= ' '.$nums[$slugidx].'. '.esc_js(wppa_dflt($slugs[$slugidx]));
					if ( $slugs[$slugidx] != '' && $wppa_opt[$slugs[$slugidx]] != $wppa_defaults[$slugs[$slugidx]] ) {
						$is_dflt = false;
					}
				}
			}
		}
		$result .= '<td><input type="button" style="font-size: 11px; height:20px; padding:0; cursor: pointer;" title="'.__('Click for help', 'wp-photo-album-plus').'" onclick="alert('."'".$hlp."'".')" value="&nbsp;' . ( $is_dflt ? '?' : '!' ) . '&nbsp;"></td>';
	}
	else {
		$result .= '<td></td>';//$hlp = __('No help available');
	}

	$result .= '</tr>';

	echo $result;

}

function wppa_tags_to_clas( $tags = '-' ) {
global $wppa_tags;

	if ( ! $tags ) $tags = '-';

	$tagcls = '';
	$my_tags = explode( ',', $tags );
	$wppa_tag_keys = array_keys($wppa_tags);

	// Test for non-supported tags
	foreach( $my_tags as $tag ) {
		if ( ! in_array( $tag, $wppa_tag_keys ) ) {
			wppa_error_message( 'Unexpected tag: '.$tag );
		}
	}

	// Compose classes
	foreach( $wppa_tag_keys as $tag ) {
		if ( in_array( $tag, $my_tags ) ) {
			$tagcls .= ' wppatag-'.$tag;
		}
		else {
			$tagcls .= ' _wppatag-'.$tag;
		}
	}

	return $tagcls;
}

function wppa_input($slug, $width, $minwidth = '', $text = '', $onchange = '', $placeholder = '') {
global $wppa_opt;

	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug;
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$val = isset ( $wppa_opt[ $slug ] ) ? esc_attr( $wppa_opt[ $slug ] ) : get_option( $slug, '' );
	$html = '<input'.$title.' style="float:left; width: '.$width.'; height:20px;';
	if ($minwidth != '') $html .= ' min-width:'.$minwidth.';';
	$html .= ' font-size: 11px; margin: 0px; padding: 0px;" type="text" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	if ( $placeholder ) $html .= ' placeholder="'.$placeholder.'"';
	$html .= ' value="'.$val.'" />';
	$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	$html .= '<span style="float:left">'.$text.'</span>';

	return $html;
}

function wppa_edit($slug, $value, $width = '90%', $minwidth = '', $text = '', $onchange = '') {

	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug;
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$html = '<input'.$title.' style="float:left; width: '.$width.'; height:20px;';
	if ($minwidth != '') $html .= ' min-width:'.$minwidth.';';
	$html .= ' font-size: 11px; margin: 0px; padding: 0px;" type="text" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	$html .= ' value="'.esc_attr($value).'" />';
	$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	$html .= $text;

	return $html;

}

function wppa_textarea($slug, $buttonlabel = '') {

	if ( wppa_switch('wppa_use_wp_editor') ) {	// New style textarea, use wp_editor
		$editor_id = str_replace( '_', '', $slug);
		ob_start();
			$quicktags_settings = array( 'buttons' => 'strong,em,link,block,ins,ul,ol,li,code,close' );
			wp_editor( wppa_opt( $slug ), $editor_id, $settings = array('wpautop' => false, 'media_buttons' => false, 'textarea_rows' => '6', 'textarea_name' => $slug, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
		$html = ob_get_clean();
		$blbl = __('Update', 'wp-photo-album-plus');
		if ( $buttonlabel ) $blbl .= ' '.$buttonlabel;

		$html .= wppa_ajax_button($blbl, $slug, $editor_id, 'no_confirm');
	}
	else {
		$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug;
		$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';

		$html = '<textarea id="'.$slug.'"'.$title.' style="float:left; width:300px;" onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)" >';
		$html .= esc_textarea( stripslashes( wppa_opt( $slug )));
		$html .= '</textarea>';

		$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	}
	return $html;
}

function wppa_checkbox($slug, $onchange = '', $class = '') {
global $wppa_defaults;
global $wppa_opt;

	// Check for wp delete_option bug
	if ( ! get_option( $slug, 'nil' ) ) { // Switch can only be 'yes' or 'no', not '' caused by a faulty delete_option() that did not remove the option but replaced the value by ''.
		update_option( $slug, $wppa_defaults[$slug] );	// Missing option takes the default
		wppa_opt( $slug, $wppa_defaults[$slug] );		// Also in memory
		wppa_log('Repair', 'Fixed option '.$slug.' set to '.$wppa_defaults[$slug]);
	}

	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug."\n".__('Values = yes, no', 'wp-photo-album-plus');
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'.$title;
	if ( wppa_switch( $slug ) ) $html .= ' checked="checked"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';

	if ( substr( $onchange, 0, 10 ) == 'wppaFollow' ) {
		$html .= '<script type="text/javascript" >jQuery(document).ready(function(){'.$onchange.'})</script>';
	}

	return $html;
}

function wppa_checkbox_warn($slug, $onchange = '', $class = '', $warning) {
global $wppa_defaults;

	// Check for wp delete_option bug
	if ( ! get_option( $slug, 'nil' ) ) { // Switch can only be 'yes' or 'no', not '' caused by a faulty delete_option() that did not remove the option but replaced the value by ''.
		update_option( $slug, $wppa_defaults[$slug] );	// Missing option takes the default
		wppa_opt( $slug, $wppa_defaults[$slug] );		// Also in memory
		wppa_log('Repair', 'Fixed option '.$slug.' set to '.$wppa_defaults[$slug]);
	}

	$warning = esc_js(__('Warning!', 'wp-photo-album-plus')).'\n\n'.$warning;
	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug."\n".__('Values = yes, no', 'wp-photo-album-plus');
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'.$title;
	if ( wppa_switch( $slug ) ) $html .= ' checked="checked"';
	if ($onchange != '') $html .= ' onchange="alert(\''.$warning.'\'); '.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="alert(\''.$warning.'\'); wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';

	return $html;
}

function wppa_checkbox_warn_off($slug, $onchange = '', $class = '', $warning, $is_help = true) {
global $wppa_defaults;

	// Check for wp delete_option bug
	if ( ! get_option( $slug, 'nil' ) ) { // Switch can only be 'yes' or 'no', not '' caused by a faulty delete_option() that did not remove the option but replaced the value by ''.
		update_option( $slug, $wppa_defaults[$slug] );	// Missing option takes the default
		wppa_opt( $slug, $wppa_defaults[$slug] );		// Also in memory
		wppa_log('Repair', 'Fixed option '.$slug.' set to '.$wppa_defaults[$slug]);
	}

	$warning = esc_js(__('Warning!', 'wp-photo-album-plus')).'\n\n'.$warning;
	if ( $is_help) $warning .= '\n\n'.esc_js(__('Please read the help', 'wp-photo-album-plus'));
	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug."\n".__('Values = yes, no', 'wp-photo-album-plus');
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'.$title;
	if ( wppa_switch( $slug ) ) $html .= ' checked="checked"';
	if ($onchange != '') $html .= ' onchange="if (!this.checked) alert(\''.$warning.'\'); '.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="if (!this.checked) alert(\''.$warning.'\'); wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';

	return $html;
}

function wppa_checkbox_warn_on($slug, $onchange = '', $class = '', $warning) {
global $wppa_defaults;

	// Check for wp delete_option bug
	if ( ! get_option( $slug, 'nil' ) ) { // Switch can only be 'yes' or 'no', not '' caused by a faulty delete_option() that did not remove the option but replaced the value by ''.
		update_option( $slug, $wppa_defaults[$slug] );	// Missing option takes the default
		wppa_opt( $slug, $wppa_defaults[$slug] );		// Also in memory
		wppa_log('Repair', 'Fixed option '.$slug.' set to '.$wppa_defaults[$slug]);
	}

	$warning = esc_js(__('Warning!', 'wp-photo-album-plus')).'\n\n'.$warning.'\n\n'.esc_js(__('Please read the help', 'wp-photo-album-plus'));
	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug."\n".__('Values = yes, no', 'wp-photo-album-plus');
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';
	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'.$title;
	if ( wppa_switch( $slug ) ) $html .= ' checked="checked"';
	if ($onchange != '') $html .= ' onchange="if (this.checked) alert(\''.$warning.'\'); '.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="if (this.checked) alert(\''.$warning.'\'); wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';

	return $html;
}

function wppa_checkbox_e($slug, $curval, $onchange = '', $class = '', $enabled = true) {

	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"';
	if ($curval) $html .= ' checked="checked"';
	if ( ! $enabled ) $html .= ' disabled="disabled"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';

	return $html;
}

function wppa_select($slug, $options, $values, $onchange = '', $class = '', $first_disable = false, $postaction = '', $max_width = '220' ) {
global $wppa_opt;
global $wppa_defaults;

	if ( ! is_array( $options ) ) {
		$html = __('There are no pages (yet) to link to.', 'wp-photo-album-plus');
		return $html;
	}

/*
	// Check for wp delete_option bug
	$opt = get_option( $slug, 'nil' );
	if ( ! $opt && ! in_array( $opt, $values ) && $slug != 'wppa_blacklist_user' && $slug != 'wppa_un_blacklist_user' ) { // Value can not be '' caused by a faulty delete_option() that did not remove the option but replaced the value by ''.
		update_option( $slug, $wppa_defaults[$slug] );	// Missing option takes the default
		wppa_opt( $slug, $wppa_defaults[$slug] );		// Also in memory
		wppa_log('Repair', 'Fixed option '.$slug.' set to '.$wppa_defaults[$slug]);
	}
*/
	$tit = __('Slug =', 'wp-photo-album-plus').' '.$slug."\n".__('Values = ', 'wp-photo-album-plus');
	foreach( $values as $val ) $tit.= $val.', ';
	$tit = trim( $tit, ', ');
	$title = wppa_switch( 'enable_shortcode_wppa_set' ) ? ' title="'.esc_attr( $tit ).'"' : '';

	$html = '<select style="float:left; font-size: 11px; height: 20px; margin: 0px; padding: 0px; max-width:'.$max_width.'px;" id="'.$slug.'"'.$title;
//	if ($onchange != '')..
	$html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this);'.$postaction.'"';
//	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= '>';

	$val = get_option( $slug ); // value can be yes or no in Table 0 !! so do not use wppa_opt()
	$idx = 0;
	$cnt = count($options);
	while ($idx < $cnt) {
		$html .= "\n";
		$html .= '<option value="'.$values[$idx].'" ';
		$dis = false;
		if ($idx == 0 && $first_disable) $dis = true;
		$opt = trim($options[$idx], '|');
		if ($opt != $options[$idx]) $dis = true;
		if ($val == $values[$idx]) $html .= ' selected="selected"';
		if ($dis) $html .= ' disabled="disabled"';
		$html .= '>'.$opt.'</option>';
		$idx++;
	}
	$html .= '</select>';
	$html .= '<img id="img_'.$slug.'" class="'.$class.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';

	return $html;
}

function wppa_select_e( $slug, $curval, $options, $values, $onchange = '', $class = '' ) {

	if ( ! is_array( $options ) ) {
		$html = __('There are no pages (yet) to link to.', 'wp-photo-album-plus');
		return $html;
	}

	$html = '<select style="float:left; font-size: 11px; height: 20px; margin: 0px; padding: 0px;" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= '>';

	$val = $curval;
	$idx = 0;
	$cnt = count($options);
	while ($idx < $cnt) {
		$html .= "\n";
		$html .= '<option value="'.$values[$idx].'" ';
		if ($val == $values[$idx]) $html .= ' selected="selected"';
		$html .= '>'.$options[$idx].'</option>';
		$idx++;
	}
	$html .= '</select>';
	$html .= '<img id="img_'.$slug.'" class="'.$class.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wp-photo-album-plus').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';

	return $html;
}

function wppa_dflt($slug) {
global $wppa_defaults;
global $no_default;

	if ( $slug == '' ) return '';
	if ( $no_default ) return '';

	$dflt = isset( $wppa_defaults[$slug] ) ? $wppa_defaults[$slug] : '';

	$dft = $dflt;
	switch ($dflt) {
		case 'yes': 	$dft .= ': '.__('Checked', 'wp-photo-album-plus'); break;
		case 'no': 		$dft .= ': '.__('Unchecked', 'wp-photo-album-plus'); break;
		case 'none': 	$dft .= ': '.__('no link at all.', 'wp-photo-album-plus'); break;
		case 'file': 	$dft .= ': '.__('the plain photo (file).', 'wp-photo-album-plus'); break;
		case 'photo': 	$dft .= ': '.__('the full size photo in a slideshow.', 'wp-photo-album-plus'); break;
		case 'single': 	$dft .= ': '.__('the fullsize photo on its own.', 'wp-photo-album-plus'); break;
		case 'indiv': 	$dft .= ': '.__('the photo specific link.', 'wp-photo-album-plus'); break;
		case 'album': 	$dft .= ': '.__('the content of the album.', 'wp-photo-album-plus'); break;
		case 'widget': 	$dft .= ': '.__('defined at widget activation.', 'wp-photo-album-plus'); break;
		case 'custom': 	$dft .= ': '.__('defined on widget admin page.', 'wp-photo-album-plus'); break;
		case 'same': 	$dft .= ': '.__('same as title.', 'wp-photo-album-plus'); break;
		default:
	}

	return $dft;
}

function wppa_color_box( $slug ) {

	return '<div id="colorbox-' . $slug . '" style="width:100px; height:16px; float:left; background-color:' . wppa_opt( $slug ) . '; border:1px solid #dfdfdf;" ></div>';

}

function wppa_doit_button( $label = '', $key = '', $sub = '', $height = '16', $fontsize = '11' ) {
	if ( $label == '' ) $label = __('Do it!', 'wp-photo-album-plus');

	$result = '<input type="submit" class="button-primary" style="float:left; font-size:'.$fontsize.'px; height:'.$height.'px; margin: 0 4px; padding: 0px; line-height:12px;"';
	$result .= ' name="wppa_settings_submit" value="&nbsp;'.$label.'&nbsp;"';
	$result .= ' onclick="';
	if ( $key ) $result .= 'document.getElementById(\'wppa-key\').value=\''.$key.'\';';
	if ( $sub ) $result .= 'document.getElementById(\'wppa-sub\').value=\''.$sub.'\';';
	$result .= 'if ( confirm(\''.__('Are you sure?', 'wp-photo-album-plus').'\')) return true; else return false;" />';

	return $result;
}

function wppa_popup_button( $slug ) {

	$label 	= __('Show!', 'wp-photo-album-plus');
	$result = '<input type="button" class="button-secundary" style="float:left; border-radius:3px; font-size: 11px; height: 18px; margin: 0 4px; padding: 0px;" value="'.$label.'"';
	$result .= ' onclick="wppaAjaxPopupWindow(\''.$slug.'\')" />';

	return $result;
}

function wppa_ajax_button( $label = '', $slug, $elmid = '0', $no_confirm = false ) {
	if ( $label == '' ) $label = __('Do it!', 'wp-photo-album-plus');

	$result = '<input type="button" class="button-secundary" style="float:left; border-radius:3px; font-size: 11px; height: 18px; margin: 0 4px; padding: 0px;" value="'.$label.'"';
	$result .= ' onclick="';
	if ( ! $no_confirm ) $result .= 'if (confirm(\''.__('Are you sure?', 'wp-photo-album-plus').'\')) ';
	if ( $elmid ) {
		$result .= 'wppaAjaxUpdateOptionValue(\''.$slug.'\', document.getElementById(\''.$elmid.'\'))" />';
	}
	else {
		$result .= 'wppaAjaxUpdateOptionValue(\''.$slug.'\', 0)" />';
	}

	$result .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Not done yet', 'wp-photo-album-plus').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';

	return $result;
}

function wppa_maintenance_button( $slug ) {

	$label 	= __('Start!', 'wp-photo-album-plus');
	$me 	= wppa_get_user();
	$user 	= get_option( $slug.'_user', $me );

	if ( $user && $user != $me ) {
		$label = __('Locked!', 'wp-photo-album-plus');
		$locked = true;
	}
	else {
		$locked = false;
	}

	$result = '<input id="'.$slug.'_button" type="button" class="button-secundary" style="float:left; border-radius:3px; font-size: 11px; height: 18px; margin: 0 4px; padding: 0px;" value="'.$label.'"';
	if ( ! $locked ) {
		$result .= ' onclick="if ( jQuery(\'#'.$slug.'_status\').html() != \'\' || confirm(\'Are you sure ?\') ) wppaMaintenanceProc(\''.$slug.'\', false);" />';
	}
	else {
		$result .= ' onclick="alert(\'Is currently being executed by '.$user.'.\')" />';
	}
	$result .= '<input id="'.$slug.'_continue" type="hidden" value="no" />';

	return $result;
}
function wppa_status_field( $slug ) {
	$result = '<span id="'.$slug.'_status" >'.get_option( $slug.'_status', '' ).'</span>';
	return $result;
}
function wppa_togo_field( $slug ) {
	$result = '<span id="'.$slug.'_togo" >'.get_option($slug.'_togo', '' ).'</span>';
	return $result;
}

function wppa_htmlerr($slug) {

	switch ($slug) {
		case 'popup-lightbox':
			$title = __('You can not have popup and lightbox on thumbnails at the same time. Uncheck either Table IV-C8 or choose a different linktype in Table VI-2.', 'wp-photo-album-plus');
			break;
		default:
			$title = __('It is important that you select a page that contains at least %%wppa%% or [wppa][/wppa].', 'wp-photo-album-plus');
			$title .= " ".__('If you ommit this, the link will not work at all or simply refresh the (home)page.', 'wp-photo-album-plus');
			break;
	}
	$result = '<img  id="'.$slug.'-err" '.
					'src="'.wppa_get_imgdir().'error.png" '.
					'class="'.$slug.'-err" '.
					'style="height:16px; width:16px; float:left; display:none;" '.
					'title="'.$title.'" '.
					'onmouseover="jQuery(this).animate({width: 32, height:32}, 100)" '.
					'onmouseout="jQuery(this).animate({width: 16, height:16}, 200)" />';

	return $result;
}

function wppa_verify_page( $slug ) {
global $wpdb;
global $wppa_opt;

	// Does slug exist?
	if ( ! isset( $wppa_opt[$slug] ) ) {
		wppa_error_message('Unexpected error in wppa_verify_page()', 'red', 'force');
		return;
	}

	// A page number 0 is allowed ( same post/page )
	if ( ! $wppa_opt[$slug] ) {
		return;
	}

	// If page vanished, update to 0
	$iret = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->posts . "` WHERE `post_type` = 'page' AND `post_status` = 'publish' AND `ID` = %s", wppa_opt( $slug )));
	if ( ! $iret ) {
		wppa_update_option($slug, '0');
	}
}