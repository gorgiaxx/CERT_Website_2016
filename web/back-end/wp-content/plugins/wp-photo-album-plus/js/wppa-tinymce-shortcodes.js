/* wppa-tinymce-shortcodes.js
* Pachkage: wp-photo-album-plus
*
*
* Version 6.4.05
*
*/

// Add the wppa button to the mce editor
tinymce.PluginManager.add('wppagallery', function(editor, url) {

		function openWppaShortcodeGenerator() {
			// triggers the thickbox
			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = jQuery(window).height();
			H = H - 120;
			tb_show( 'WPPA+ Shortcode Generator', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=wppagallery-form' );
		}

		editor.addButton('mygallery_button', {
			image: wppaImageDirectory+'albumnew32.png',
			tooltip: 'WPPA+ Shortcode Generator',
			onclick: openWppaShortcodeGenerator
		});

});

// executes this when the DOM is ready
jQuery(function(){

	// creates a form to be displayed everytime the button is clicked
	var xmlhttp = wppaGetXmlHttp();				// located in wppa-admin-scripts.js

	// wppa-ajax.php calls wppa_make_tinymce_dialog(); which is located in wppa-tinymce.php
	var url = wppaAjaxUrl+'?action=wppa&wppa-action=tinymcedialog';

	xmlhttp.open("GET",url,true);
	xmlhttp.send();
	xmlhttp.onreadystatechange=function() {
		if  (xmlhttp.readyState == 4 && xmlhttp.status!=404 ) {
			var formtext = xmlhttp.responseText;

			var form = jQuery(formtext);

			var table = form.find('table');
			form.appendTo('body').hide();

			// handles the click event of the submit button
			form.find('#wppagallery-submit').click(function(){

				// Get the shortcode from the preview/edit box
				newShortcode = document.getElementById('wppagallery-shortcode-preview').value;

				// Filter
				newShortcode = newShortcode.replace(/&quot;/g, '"');

				// inserts the shortcode into the active editor
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, newShortcode);

				// closes Thickbox
				tb_remove();
			});
		}
	}
});

function wppaGalleryEvaluate() {

	// Assume shortcode complete
	var shortcodeOk = true;

	// Hide option elements
	jQuery('#wppagallery-galery-type-tr').hide();
	jQuery('#wppagallery-slides-type-tr').hide();
	jQuery('#wppagallery-single-type-tr').hide();
	jQuery('#wppagallery-search-type-tr').hide();
	jQuery('#wppagallery-miscel-type-tr').hide();
	jQuery('#wppagallery-album-type-tr').hide();
	jQuery('#wppagallery-album-real-tr').hide();
	jQuery('#wppagallery-album-realopt-tr').hide();
	jQuery('#wppagallery-album-virt-tr').hide();
	jQuery('#wppagallery-album-virt-cover-tr').hide();
	jQuery('#wppagallery-owner-tr').hide();
	jQuery('#wppagallery-owner-parent-tr').hide();
	jQuery('#wppagallery-album-parent-tr').hide();
	jQuery('#wppagallery-album-count-tr').hide();
	jQuery('#wppagallery-photo-count-tr').hide();
	jQuery('#wppagallery-albumcat-tr').hide();
	jQuery('#wppagallery-photo-tr').hide();
	jQuery('#wppagallery-photo-preview-tr').hide();
	jQuery('#wppagallery-phototags-tr').hide();
	jQuery('#wppagallery-search-tr').hide();
	jQuery('#wppagallery-taglist-tr').hide();
	jQuery('#wppagallery-album-super-tr').hide();
	jQuery('#wppagallery-calendar-tr').hide();
	jQuery('#wppagallery-tags-cats-tr').hide();
	jQuery('#wppagallery-landing-tr').hide();
	jQuery('#wppagallery-rootalbum-tr').hide();

	// Init shortcode parts
	var shortcode 		= '[wppa';
	var topType 		= '';
	var type 			= '';
	var galType 		= '';
	var slideType 		= '';
	var albumType 		= '';
	var searchType 		= '';
	var miscType 		= '';
	var album 			= '';
	var parent 			= '';
	var count 			= '';
	var photo 			= '';
	var id 				= '';
	var sub 			= '';
	var root 			= '';
	var needGalType 	= false;
	var needSlideType 	= false;
	var needAlbum 		= false;
	var needPhoto 		= false;
	var needOwner 		= false;
	var needTag 		= false;
	var needTagList 	= false;
	var needCat 		= false;
	var needSearchType 	= false;
	var needMiscType 	= false;
	var alltags 		= '';
	var taglist 		= '';
	var owner 			= '';
	var tags 			= '';
	var cats 			= '';
	var i,j,t;
	var caltype 		= '';
	var reverse 		= false;
	var allopen 		= false;
	var andor 			= false;
	var sep 			= '';
	var landing 		= '0';
	var rootalbum 		= '0';

	// Type
	topType = jQuery('#wppagallery-top-type').attr('value');
	switch ( topType ) {
		case 'galerytype':
			jQuery('#wppagallery-galery-type-tr').show();
			type = jQuery('#wppagallery-galery-type').attr('value');
			needGalType = true;
			needAlbum = true;
			jQuery('#wppagallery-album-type-tr').show();
			jQuery('#wppagallery-top-type').css('color', '#070');
			if ( type == '' ) {
				jQuery('#wppagallery-galery-type').css('color', '#700');
			}
			else {
				jQuery('#wppagallery-galery-type').css('color', '#070');
				galType = type;
			}
			break;
		case 'slidestype':
			jQuery('#wppagallery-slides-type-tr').show();
			type = jQuery('#wppagallery-slides-type').attr('value');
			needSlideType = true;
			needAlbum = true;
			jQuery('#wppagallery-album-type-tr').show();
			jQuery('#wppagallery-top-type').css('color', '#070');
			if ( type == '' ) {
				jQuery('#wppagallery-slides-type').css('color', '#700');
			}
			else {
				jQuery('#wppagallery-slides-type').css('color', '#070');
				slideType = type;
			}
			break;
		case 'singletype':
			jQuery('#wppagallery-single-type-tr').show();
			type = jQuery('#wppagallery-single-type').attr('value');
			needPhoto = true;
			jQuery('#wppagallery-photo-tr').show();
			jQuery('#wppagallery-top-type').css('color', '#070');
			if ( type == '' ) {
				jQuery('#wppagallery-single-type').css('color', '#700');
			}
			else {
				jQuery('#wppagallery-single-type').css('color', '#070');
			}
			break;
		case 'searchtype':
			jQuery('#wppagallery-search-type-tr').show();
			type = jQuery('#wppagallery-search-type').attr('value');
			needSearchType = true;
			searchType = type;
			switch ( type ) {
				case 'search':
					jQuery('#wppagallery-search-tr').show();
					if ( jQuery('#wppagallery-root').attr('checked') == 'checked' ) jQuery('#wppagallery-rootalbum-tr').show();
					rootalbum = jQuery('#wppagallery-rootalbum').val();
					jQuery('#wppagallery-landing-tr').show();
					landing = jQuery('#wppagallery-landing').val();
					break;
				case 'supersearch':
					break;
				case 'tagcloud':
				case 'multitag':
					jQuery('#wppagallery-taglist-tr').show();
					alltags = jQuery('#wppagallery-alltags').attr('checked');
					if ( alltags != 'checked' ) {
						needTagList = true;
						jQuery('#wppagallery-seltags').show();
						t = jQuery('.wppagallery-taglist-tags');
						var tagarr = [];
						i = 0;
						j = 0;
						while ( i < t.length ) {
							if ( t[i].selected ) {
								tagarr[j] = t[i].value;
								j++;
							}
							i++;
						}
						taglist = wppaArrayToEnum( tagarr, ',' );
						if ( taglist == '' ) {
							jQuery('.wppagallery-tags').css('color', '#700');
						}
						else {
							jQuery('.wppagallery-tags').css('color', '#070');
						}
					}
					break;
				case 'superview':
					jQuery('#wppagallery-album-super-tr').show();
					album = jQuery('#wppagallery-album-super-parent').attr('value');
					break;
				case 'calendar':
					jQuery('#wppagallery-calendar-tr').show();
					jQuery('#wppagallery-album-super-tr').show();	// Optional parent album
					caltype = jQuery('#wppagallery-calendar-type').attr('value');
					reverse = jQuery('#wppagallery-calendar-reverse').attr('checked');
					allopen = jQuery('#wppagallery-calendar-allopen').attr('checked');
					parent  = jQuery('#wppagallery-album-super-parent').attr('value');
					break;
				default:
			}
			jQuery('#wppagallery-top-type').css('color', '#070');
			if ( type == '' ) {
				jQuery('#wppagallery-search-type').css('color', '#700');
			}
			else {
				jQuery('#wppagallery-search-type').css('color', '#070');
			}
			break;
		case 'misceltype':
			jQuery('#wppagallery-miscel-type-tr').show();
			type = jQuery('#wppagallery-miscel-type').attr('value');
			needMiscType = true;
			switch ( type ) {
				case 'generic':
				case 'upload':
				case 'landing':
				case 'stereo':
					miscType = type;
					break;
				default:
			}
			jQuery('#wppagallery-top-type').css('color', '#070');
			if ( type == '' ) {
				jQuery('#wppagallery-miscel-type').css('color', '#700');
			}
			else {
				jQuery('#wppagallery-miscel-type').css('color', '#070');
			}
			break;
		default:
			jQuery('#wppagallery-top-type').css('color', '#700');
	}
	if ( type != '' ) {
		shortcode += ' type="'+type+'"';
	}
	else {
	}

	// Album
	if ( needAlbum ) {
		albumType = jQuery('#wppagallery-album-type').attr('value');
		switch ( albumType ) {
			case 'real':
				jQuery('#wppagallery-album-real-tr').show();
				album = wppaGetSelectionEnumByClass('.wppagallery-album-r');
				/*
				t = jQuery('.wppagallery-album-r');
				var albumarr = [];
				i = 0;
				j = 0;
				while ( i < t.length ) {
					if ( t[i].selected ) {
						albumarr[j] = t[i].value;
						j++;
					}
					i++;
				}
				album = wppaArrayToEnum( albumarr, '.' );
				*/
				if ( album != '' ) {
					jQuery('#wppagallery-album-type').css('color', '#070');
				}
				break;
			case 'virtual':
				// Open the right selection box dependant of type is cover or not
				// and get the album identifier
				if ( type == 'cover') {
					jQuery('#wppagallery-album-virt-cover-tr').show();
					album = jQuery('#wppagallery-album-virt-cover').attr('value');
				}
				else {	// type != cover
					jQuery('#wppagallery-album-virt-tr').show();
					album = jQuery('#wppagallery-album-virt').attr('value');
				}
				// Now displatch on album identifier found
				// and get the (optional) additional data
				if ( album != '' ) {
					switch ( album ) {
						case '#topten':
						case '#lasten':
						case '#featen':
						case '#comten':
							jQuery('#wppagallery-album-realopt-tr').show();
//							parent = jQuery('#wppagallery-album-real').attr('value');
							// We use parent here for optional album(s), because album is already used for virtual album type
							parent = wppaGetSelectionEnumByClass('.wppagallery-album-ropt');
							if ( parent == '' ) parent = '0';
							jQuery('#wppagallery-photo-count-tr').show();
							count = jQuery('#wppagallery-photo-count').attr('value');
							break;
						case '#tags':
							jQuery('#wppagallery-phototags-tr').show();
							jQuery('#wppagallery-tags-cats-tr').show();
							andor = jQuery('[name=andor]:checked').val();
							if ( ! andor ) jQuery('#wppagallery-or').attr( 'checked', 'checked' );
							andor = jQuery('[name=andor]:checked').val();
							if ( andor == 'or' ) sep = ';';
							else sep = ',';
							needTag = true;
							t = jQuery('.wppagallery-phototags');
							var tagarr = [];
							i = 0;
							j = 0;
							while ( i < t.length ) {
								if ( t[i].selected ) {
									tagarr[j] = t[i].value;
									j++;
								}
								i++;
							}
							tags = wppaArrayToEnum( tagarr, sep );
							if ( tags != '' ) {
								jQuery('.wppagallery-phototags').css('color', '#070');
							}
							break;
						case '#last':
							jQuery('#wppagallery-album-parent-tr').show();
							parent = jQuery('#wppagallery-album-parent-parent').attr('value');
							jQuery('#wppagallery-album-count-tr').show();
							count = jQuery('#wppagallery-album-count').attr('value');
							break;
						case '#cat':
							jQuery('#wppagallery-albumcat-tr').show();
							jQuery('#wppagallery-tags-cats-tr').show();
							andor = jQuery('[name=andor]:checked').val();
							if ( ! andor ) jQuery('#wppagallery-or').attr( 'checked', 'checked' );
							andor = jQuery('[name=andor]:checked').val();
							if ( andor == 'or' ) sep = ';';
							else sep = ',';
							needCat = true;
							t = jQuery('.wppagallery-albumcat');
							var catarr = [];
							i = 0;
							j = 0;
							while ( i < t.length ) {
								if ( t[i].selected ) {
									catarr[j] = t[i].value;
									j++;
								}
								i++;
							}
							cats = wppaArrayToEnum( catarr, sep );
							if ( cats != '' ) {
								jQuery('#wppagallery-albumcat').css('color', '#070');
							}
							break;
						case '#owner':
						case '#upldr':
							jQuery('#wppagallery-owner-tr').show();
							jQuery('#wppagallery-owner').css('color', '#700');
							needOwner = true;
							owner = jQuery('#wppagallery-owner').attr('value');
							if ( owner != '' ) {
								jQuery('#wppagallery-owner').css('color', '#070');
								jQuery('#wppagallery-owner-parent-tr').show();
								parent = wppaGetSelectionEnumByClass('.wppagallery-album-p');
							}
							break;
						case '#all':
							break;
						default:
							alert( 'Unimplemented virtual album: '+album );
					}
					if ( ( album != '#cat' || cats != '' ) &&
						( album != '#owner' || owner != '' ) &&
						( album != '#upldr' || owner != '' ) &&
						( album != '#topten' || parent != '' ) &&
						( album != '#lasten' || parent != '' ) &&
						( album != '#comten' || parent != '' ) &&
						( album != '#featen' || parent != '' ) ) {
						jQuery('#wppagallery-album-type').css('color', '#070');
					}
				}
				break;
			default:
				jQuery('#wppagallery-album-type').css('color', '#700');
				album = '';
		}
	}

	// No album specified
	if ( album == '' ) {
		jQuery('#wppagallery-album-real').css('color', '#700');
		jQuery('#wppagallery-album-virt').css('color', '#700');
		jQuery('#wppagallery-album-virt-cover').css('color', '#700');
	}

	// Add album specs to shortcode
	else {
		jQuery('#wppagallery-album-real').css('color', '#070');
		jQuery('#wppagallery-album-parent').css('color', '#070');
		jQuery('#wppagallery-album-virt').css('color', '#070');
		jQuery('#wppagallery-album-virt-cover').css('color', '#070');
		shortcode += ' album="'+album;
		if ( owner != '' ) 	shortcode += ','+owner;
		if ( parent == '' && count != '' ) 	parent = '0';
		if ( parent != '' ) shortcode += ','+parent;
		if ( count != '' ) 	shortcode += ','+count;
		if ( tags != '' ) 	shortcode += ','+tags;
		if ( cats != '' ) 	shortcode += ','+cats;
		shortcode += '"';
	}

	// Photo
	if ( needPhoto ) {
		photo = jQuery('#wppagallery-photo').attr('value');
		id = photo.split('.');
		id = id[0];
		if ( photo == '' ) {
			jQuery('#wppagallery-photo').css('color', '#700');
		}
		else {
			jQuery('#wppagallery-photo-preview-tr').show();
			wppaTinyMcePhotoPreview( photo )
			shortcode += ' photo="'+id+'"';
			jQuery('#wppagallery-photo').css('color', '#070');
		}
	}

	// Search options
	if ( type == 'search' ) {
		sub = jQuery('#wppagallery-sub').attr('checked');
		root = jQuery('#wppagallery-root').attr('checked');
		if ( sub == 'checked' ) shortcode += ' sub="1"';
		if ( root == 'checked' ) {
			if ( rootalbum != '0' ) shortcode += ' root="#'+rootalbum+'"';
			else  shortcode += ' root="1"';
		}
		if ( landing != '0' ) shortcode += ' landing="'+landing+'"';
	}
	if ( type == 'tagcloud' || type == 'multitag' ) {
		if ( taglist != '' ) {
			shortcode += ' taglist="'+taglist+'"';
		}
	}
	if ( type == 'calendar' ) {
		shortcode += ' calendar="'+caltype+'"';
		if ( parent ) {
			shortcode += ' album="'+parent+'"';
		}
		if ( reverse ) {
			shortcode += ' reverse="1"';
		}
		if ( allopen ) {
			shortcode += ' all="1"';
		}
	}

	// Size
	var size = document.getElementById('wppagallery-size').value;
	if ( size != '' && size != 'auto' ) {
		if ( parseInt(size) != size ) {
			size = 0;
		}
	}
	if ( size < 0 ) {
		size = -size;
	}
	if ( size < 100 ) {
		size = size / 100;
	}
	if ( size != 0 ) {
		shortcode += ' size="'+size+'"';
	}

	// Align
	var align = document.getElementById('wppagallery-align').value;
	if ( align != 'none' ) {
		shortcode += ' align="'+align+'"';
	}

	// Extract comment
	var t = document.getElementById('wppagallery-shortcode-preview').value;
	t = t.replace(/&quot;/g, '"');
	t = t.split(']');
	t = t[1];
	t = t.split('[');
	var shortcodeComment = t[0];

	// Close
	shortcode += ']'+shortcodeComment+'[/wppa]';

	// Display shortcode
	shortcode = shortcode.replace(/"/g, '&quot;');
	var html = '<input type="text" id="wppagallery-shortcode-preview" style="background-color:#ddd; width:100%; height:26px;" value="'+shortcode+'" />';
	document.getElementById('wppagallery-shortcode-preview-container').innerHTML = html;

	// Is shortcode complete?
	shortcodeOk = 	( album != '' || ! needAlbum ) &&
					( photo != '' || ! needPhoto ) &&
					( owner != '' || ! needOwner ) &&
					( taglist != '' || ! needTagList ) &&
					( galType != '' || ! needGalType ) &&
					( slideType != '' || ! needSlideType ) &&
					( searchType != '' || ! needSearchType ) &&
					( miscType != '' || ! needMiscType ) &&
					( tags != '' || ! needTag ) &&
					( cats != '' || ! needCat );

	// Debug
	if ( ! shortcodeOk ) {
		var text = '';
		if ( album == '' && needAlbum ) text += 'Need album\n';
		if ( photo == '' && needPhoto ) text += 'Need photo\n';
		if ( owner == '' && needOwner ) text += 'Need owner';
		if ( taglist == '' && needTagList ) text += 'Need taglist';
		if ( galType == '' && needGalType ) text += 'Need galType';
		if ( slideType == '' && needSlideType ) text += 'Need slideType';
		if ( searchType == '' && needSearchType ) text += 'Need searchType';
		if ( miscType == '' && needMiscType ) text += 'Need miscType';
		if ( tags == '' && needTag ) text += 'Need tags';
		if ( cats == '' && needCat ) text += 'Need cats';
//		alert( text );
	}

	// Display the right button
	if ( shortcodeOk ) {
		jQuery('#wppagallery-submit').show();
		jQuery('#wppagallery-submit-notok').hide();
	}
	else {
		jQuery('#wppagallery-submit').hide();
		jQuery('#wppagallery-submit-notok').show();
	}
}

function wppaTinyMcePhotoPreview( id ) {
	if ( id.indexOf('xxx') != -1 ) { // its a video
		var idv = id.replace('xxx', '');
		jQuery('#wppagallery-photo-preview').html('<video preload="metadata" style="max-width:600px; max-height:150px; margin-top:3px;" controls>'+
													'<source src="'+wppaPhotoDirectory+idv+'mp4" type="video/mp4">'+
													'<source src="'+wppaPhotoDirectory+idv+'ogg" type="video/ogg">'+
													'<source src="'+wppaPhotoDirectory+idv+'ogv" type="video/ogg">'+
													'<source src="'+wppaPhotoDirectory+idv+'webm" type="video/webm">'+
												'</video>');
	}
	else {
		jQuery('#wppagallery-photo-preview').html('<img src="'+wppaPhotoDirectory+id+'" style="max-width:400px; max-height:300px;" />');
	}
}

