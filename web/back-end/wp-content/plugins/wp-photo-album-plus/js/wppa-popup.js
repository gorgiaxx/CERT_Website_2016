// wppa-popup.js
//
// Contains popup modules
// Dependancies: wppa.js and default wp jQuery library
// 
var wppaJsPopupVersion = '6.1.9';

// Popup of thumbnail images 
function wppaPopUp( mocc, elm, id, name, desc, rating, ncom, videohtml, maxsizex, maxsizey ) {

	var topDivBig, topDivSmall, leftDivBig, leftDivSmall;
	var heightImgBig, heightImgSmall, widthImgBig, widthImgSmall, widthImgBigSpace;
	var puImg;
	var imghtml;
	
	// Give this' occurrances popup its content
	if ( document.getElementById( 'x-'+id+'-'+mocc ) ) {
	
		var namediv 	= name ? '<div id="wppa-name-'+mocc+'" style="display:none; padding:1px;" class="wppa_pu_info">'+name+'</div>' : '';
		var descdiv 	= desc ? '<div id="wppa-desc-'+mocc+'" style="clear:both; display:none; padding:1px;" class="wppa_pu_info">'+desc+'</div>' : '';
		var ratediv 	= rating ? '<div id="wppa-rat-'+mocc+'" style="clear:both; display:none; padding:1px;" class="wppa_pu_info">'+rating+'</div>' : '';
		var ncomdiv 	= ncom ? '<div id="wppa-ncom-'+mocc+'" style="clear:both; display:none; padding:1px;" class="wppa_pu_info">'+ncom+'</div>' : '';
		var popuptext 	= namediv+descdiv+ratediv+ncomdiv;
		var target 		= '';
		if ( wppaThumbTargetBlank ) {
			target = 'target="_blank"';
		}

		switch ( wppaPopupLinkType ) {
			case 'none':
				imghtml = videohtml != '' ? videohtml : '<img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" />';
				jQuery( '#wppa-popup-'+mocc ).html( '<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;">'+imghtml+popuptext+'</div>' );
				break;
			case 'fullpopup':
				imghtml = videohtml != '' ? videohtml : '<img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" onclick="'+wppaPopupOnclick[id]+'" />';
				jQuery( '#wppa-popup-'+mocc ).html( '<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;">'+imghtml+popuptext+'</div>' );
				break;
			default:
				if ( elm.onclick ) {
					imghtml = videohtml != '' ? videohtml : '<img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" />';
					jQuery( '#wppa-popup-'+mocc ).html( '<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;">'+imghtml+popuptext+'</div>' );
					document.getElementById( 'wppa-img-'+mocc ).onclick = elm.onclick;
				}
				else {
					imghtml = videohtml != '' ? videohtml : '<img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" />';
					jQuery( '#wppa-popup-'+mocc ).html( '<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;"><a id="wppa-a" href="'+document.getElementById( 'x-'+id+'-'+mocc ).href+'" '+target+' style="line-height:1px;" >'+imghtml+'</a>'+popuptext+'</div>' );
				}
		}
	}
	
	// Find handle to the popup image 
	puImg = document.getElementById( 'wppa-img-'+mocc );

	// Compute ending sizes
	widthImgBig = parseInt(maxsizex);
	heightImgBig = parseInt(maxsizey);
		
	// Set width of text fields to width of a landscape image	
	if ( puImg ) jQuery( ".wppa_pu_info" ).css( 'width', ( ( widthImgBig > heightImgBig ? widthImgBig : heightImgBig ) - 8 )+'px' );
	
	// Compute starting coords
	leftDivSmall = parseInt( elm.offsetLeft ) - 7 - 5 - 1; // thumbnail_area:padding, wppa-img:padding, wppa-border; jQuery().css( "padding" ) does not work for padding in css file, only when litaral in the tag
	topDivSmall = parseInt( elm.offsetTop ) - 7 - 1;
		
	// Compute starting sizes
	widthImgSmall = parseInt( elm.clientWidth );
	heightImgSmall = parseInt( elm.clientHeight );

	// The hor space for a portrait image is the height of the image to create room for the text on very portrait images
	widthImgBigSpace = widthImgBig > heightImgBig ? widthImgBig : heightImgBig;
	
	// Compute ending coords
	leftDivBig = leftDivSmall - parseInt( ( widthImgBigSpace - widthImgSmall ) / 2 );
	topDivBig = topDivSmall - parseInt( ( heightImgBig - heightImgSmall ) / 2 );
	
	// Margin for portrait images
	var lrMarg = parseInt( ( widthImgBigSpace - widthImgBig ) / 2 );
	
	// To fix a Chrome bug where a theme class effect is: max-width:100% causing the width not being animated:
	jQuery( '#wppa-img-'+mocc ).css( {"maxWidth":widthImgBig+"px" } );
	
	// Setup starting properties
	jQuery( '#wppa-popup-'+mocc ).css( {"marginLeft":leftDivSmall+"px","marginTop":topDivSmall+"px"} );
	jQuery( '#wppa-img-'+mocc ).css( {"marginLeft":0,"marginRight":0,"width":widthImgSmall+"px","height":heightImgSmall+"px"} );
	
	// Do the animation
	jQuery( '#wppa-popup-'+mocc ).stop().animate( {"marginLeft":leftDivBig+"px","marginTop":topDivBig+"px"}, 400 );
	jQuery( '#wppa-img-'+mocc ).stop().animate( {"marginLeft":lrMarg+"px","marginRight":lrMarg+"px","width":widthImgBig+"px","height":heightImgBig+"px"}, 400 );

	// adding ", 'linear', wppaPopReady( occ ) " fails, therefor our own timer to the "show info" module
	setTimeout( 'wppaPopReady( '+mocc+' )', 400 );
}
function wppaPopReady( mocc ) {
	jQuery( "#wppa-name-"+mocc ).show();
	jQuery( "#wppa-desc-"+mocc ).show();
	jQuery( "#wppa-rat-"+mocc ).show();
	jQuery( "#wppa-ncom-"+mocc ).show();
}

// Dismiss popup
function wppaPopDown( mocc ) {	
	jQuery( '#wppa-popup-'+mocc ).html( "" );
	return;
}

// Popup of fullsize image
function wppaFullPopUp( mocc, id, url, xwidth, xheight ) {

	var height 	= xheight+50;
	var width  	= xwidth+14;
	var name 	= '';
	var desc 	= '';
	var elm 	= document.getElementById( 'i-'+id+'-'+mocc );
	if ( elm ) {
		name = elm.alt;
		desc = elm.title;
	}	
	
	// Open new browser window
	var wnd = window.open( '', 'Print', 'width='+width+', height='+height+', location=no, resizable=no, menubar=yes ' );
	
	// Fill it in with the html
	wnd.document.write( '<html>' );
		wnd.document.write( '<head>' );	
			wnd.document.write( '<style type="text/css">body{margin:0; padding:6px; background-color:'+wppaBackgroundColorImage+'; text-align:center;}</style>' );
			wnd.document.write( '<title>'+name+'</title>' );
			wnd.document.write( 
			'<script type="text/javascript" src="/wp-includes/js/jquery/jquery.js" ></script>' +
			'<script type="text/javascript">function wppa_downl() {'+
				'jQuery.ajax( { 	url: 		\'' + wppaAjaxUrl + '\',' + 
									'data: 		\'action=wppa' +
												'&wppa-action=makeorigname' +
												'&photo-id=' + id +
												'&from=popup' + '\',' +
									'async: 	true,' +
									'type: 		\'GET\',' +
									'timeout: 	10000,' +
									'beforeSend:	function( xhr ) {' +
						
													'},' +
									'success: 		function( result, status, xhr ) {' +
														'result = result.split( "||" );'+
														'if ( result[1] == "0" ) {'+
															'window.open( result[2] );'+
															'return true;'+
														'}'+
														'else {'+
															'alert( "Error: "+result[1]+" "+result[2] );'+
															'return false;'+
														'}'+

													'},' +
									'error: 		function( xhr, status, error ) {' +
														'wppaConsoleLog( \'wppaFullPopUp failed. Error = \' + error + \', status = \' + status, \'force\' );' +
													'},' +
								'} );' +				
			'}</script>' );
			wnd.document.write( 
			'<script type="text/javascript">function wppa_print() {'+
				'document.getElementById( "wppa_printer" ).style.visibility="hidden"; '+
				'document.getElementById( "wppa_download" ).style.visibility="hidden"; '+
				'window.print();'+
			'}</script>' );
		wnd.document.write( '</head>' );
		wnd.document.write( '<body>' );
			wnd.document.write( '<div style="width:'+xwidth+'px;">' );
				wnd.document.write( '<img src="'+url+'" style="padding-bottom:6px;" /><br/>' );
				wnd.document.write( '<div style="text-align:center">'+desc+'</div>' );
				var left = xwidth-66;
				wnd.document.write( '<img src="'+wppaImageDirectory+'download.png" id="wppa_download" title="Download" style="position:absolute; top:6px; left:'+left+'px; background-color:'+wppaBackgroundColorImage+'; padding: 2px; cursor:pointer;" onclick="wppa_downl();" />' );
				left = xwidth-30;
				wnd.document.write( '<img src="'+wppaImageDirectory+'printer.png" id="wppa_printer" title="Print" style="position:absolute; top:6px; left:'+left+'px; background-color:'+wppaBackgroundColorImage+'; padding: 2px; cursor:pointer;" onclick="wppa_print();" />' );
			wnd.document.write( '</div>' );
		wnd.document.write( '</body>' );
	wnd.document.write( '</html>' );
}

// Say we're in
wppaConsoleLog( 'wppa-popup.js version '+wppaJsPopupVersion+' loaded.', 'force' );