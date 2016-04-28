/* wppa-tinymce-scripts.js
* Pachkage: wp-photo-album-plus
*
*
* Version 5.4.5
*
*/


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
				
				var type 	= table.find('#wppagallery-type').val();
				var temp1;
					if (table.find('#wppagallery-album').val()) temp1 = table.find('#wppagallery-album').val().split('|');
					else temp1 = [''];
				var album 	= temp1[0];
				var tags 	= table.find('#wppagallery-tags').val();
				var andor	= document.getElementById('wppagallery-andor').checked;
				var size 	= table.find('#wppagallery-size').val();
				var align	= table.find('#wppagallery-align').val();
				var temp2;
					if (table.find('#wppagallery-photo').val()) temp2 = table.find('#wppagallery-photo').val().split('.');
					else temp2 = [''];
				var photo	= temp2[0];		
				var temp3 	= photo.split('/');
					photo = '';
					for ( i=0; i<temp3.length; i++ ) photo += temp3[i];
					
				var alb 	= table.find('#wppagallery-alb').val();
					if ( alb == '' ) alb = '0'; else alb = parseInt(alb);
				var cnt		= table.find('#wppagallery-cnt').val();
					if ( cnt == '' ) cnt = '0'; else cnt = parseInt(cnt);
				
				// Sinitize input
				if (size == 0) {}								// Ok, use default
				else if (size == parseInt(size) && size > 0) {}	// Ok, positive number
				else if (size == 'auto') {}						// Ok, auto
				else {
					alert('Sorry, you made a mistake\n\nSize must be a positive number or auto\nA number less than 100 will be interpreted as a percentage of the current column width\n\nPlease try again');
					return;
				}
				if (size < 100) size=size/100;

				// Check for inconsistencies
				if ( type == 'cover' ) {
					if ( album == '#topten' || album == '#lasten' || album == '#comten' || album == '#featen' || album == '#tags' || album == '#all' ) {
						alert('Sorry, you made a mistake\n\nA --- special --- selection has no album cover\n\nPlease try again');
						return;
					}
				}
				if ( type != 'photo' && type != 'mphoto' && type != 'slphoto' && type != 'generic' && type != 'upload' ) {
					if ( album == 0 ) {
						alert('Sorry, you made a mistake\n\nPlease select an album\n\nPlease try again');
						return;
					}
				}
				if ( type == 'photo' || type == 'mphoto' || type == 'slphoto' )	{
					if ( photo == 0 ) {
						alert('Sorry, you made a mistake\n\nPlease select a photo\n\nPlease try again');
						return;
					}
				}

				if ( album == '#tags' && ! tags ) {	
					alert('Select at least one tag and try again.');
					return;
				}
				
				// Make the shortcode
				var shortcode = '%%wppa%%';
				
				if ( type == 'generic' ) {
				}
				else if ( type == 'photo' || type == 'mphoto' || type == 'slphoto' )	{			
					shortcode += ' %%'+type+'='+photo+'%%';
				}
				else if ( album == '#tags' ) {
					shortcode += ' %%album=#tags,';
					var sep = andor ? ',' : ';';
					var last = tags.length - 1;
					for (var tag in tags) {
						shortcode += tags[tag];
						if ( tag != last ) shortcode += sep;
					}
					shortcode += '%%';
				}
				else {
					var temp = album.split('|');
					if ( temp[0] == '#topten'|| temp[0] == '#lasten' || temp[0] == '#comten' || temp[0] == '#featen' ) {
						if ( cnt != '0' ) {
							shortcode += ' %%'+type+'='+temp[0]+','+alb+','+cnt+'%%';
						}
						else if ( alb != '0' ) {
							shortcode += ' %%'+type+'='+temp[0]+','+alb+'%%';
						}
						else {
							shortcode += ' %%'+type+'='+album+'%%';
						}
					}
					else {
						shortcode += ' %%'+type+'='+album+'%%';
					}
				}
					
				if ( size != 0 )
					shortcode += ' %%size='+size+'%%';
				
				if ( align != 'none' )
					shortcode += ' %%align='+align+'%%';			
				
				// inserts the shortcode into the active editor
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
				
				// closes Thickbox
				tb_remove();
			});
		}
	}
});

function wppaGalleryTypeChange(value) {

	if (value == 'generic' ) {
		jQuery('.wppagallery-photo').hide();
		jQuery('.wppagallery-album').hide();
		jQuery('.wppagallery-help').show();
	}
	else if (value == 'photo' || value == 'mphoto' || value == 'slphoto' ) {
		jQuery('.wppagallery-photo').show();
		jQuery('.wppagallery-album').hide();
		jQuery('.wppagallery-help').hide();
	}
	else {
		jQuery('.wppagallery-photo').hide();
		jQuery('.wppagallery-album').show();
		jQuery('.wppagallery-help').hide();
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
		jQuery('#wppagallery-photo-preview').html('<img src="'+wppaThumbDirectory+id+'" style="max-width:600px; max-height:150px; margin-top:3px;" />');
	}
}

function wppaTinyMceAlbumPreview(id) {
	var html = '';
	var temp = id.split('|');
	var count = temp.length - 1;
	
	if (count > 0) for (var i = 1; i <= count; i++) {
		if ( temp[i] != '' ) html += '<img src="'+wppaThumbDirectory+temp[i]+'" title="'+parseInt(temp[i])+'" style="max-width:75px; max-height:75px; margin:2px;" />';
	}
	else {
		html = '<br /><br /><br />'+wppaNoPreview;
	}
	jQuery('#wppagallery-album-preview').html(html);
	
	if ( temp[0] == '#topten' || temp[0] == '#lasten' || temp[0] == '#comten' || temp[0] == '#featen' ) { 
		jQuery('.wppagallery-extra').show();
	}
	else {
		jQuery('.wppagallery-extra').hide();
	}
}

function wppaGalleryAlbumChange(value) {
	if ( value == '#tags' ) jQuery('.wppagallery-tags').css('display', '');
	else jQuery('.wppagallery-tags').css('display', 'none');
}
/**/