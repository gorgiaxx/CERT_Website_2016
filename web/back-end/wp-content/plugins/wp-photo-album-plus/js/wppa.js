// wppa.js
//
// conatins common vars and functions
//
var wppaJsVersion = '6.4.09';

// Important notice:
// All external vars that may be given a value in wppa-non-admin.php must be declared here and not in other front-end js files!!
// This will prevent overwriting with default values in case defereed js is activated

// 'External' variables ( public )
var wppaVersion = '0';
var wppaDebug;
var wppaFullValign = [];
var wppaFullHalign = [];
var wppaFullFrameDelta = [];
var wppaAnimationSpeed;
var wppaImageDirectory;
var wppaAutoColumnWidth = [];
var wppaAutoColumnFrac = [];
var wppaThumbnailAreaDelta;
var wppaSlideShowTimeOut = 2500;
var wppaFadeInAfterFadeOut = false;
var wppaTextFrameDelta = 0;
var wppaBoxDelta = 0;
var wppaPreambule;
var wppaHideWhenEmpty = false;
var wppaThumbnailPitch = [];
var wppaFilmStripLength = [];
var wppaFilmStripMargin = [];
var wppaFilmStripAreaDelta = [];
var wppaFilmShowGlue = false;
var wppaIsMini = [];
var wppaPortraitOnly = [];
var wppaSlideShow;				// = 'Slideshow' or its translation
var wppaPhoto;					// = 'Photo' or its translation
var wppaOf;						// = 'of' or its translation
var wppaNextPhoto;				// = 'Next photo' or its translation
var wppaPreviousPhoto;			// = 'Previous photo' or its translation
var wppaSlower;
var wppaFaster;
var wppaNextP;
var wppaPrevP;
var wppaAvgRating;
var wppaMyRating;
var wppaAvgRat;
var wppaMyRat;
var wppaDislikeMsg;
var wppaMiniTreshold = 300;
var wppaStart = 'Start';		// defaults
var wppaStop = 'Stop';			//
var wppaPleaseName;
var wppaPleaseEmail;
var wppaPleaseComment;
var wppaRatingOnce = true;
var wppaBGcolorNumbar = 'transparent';
var wppaBcolorNumbar = 'transparent';
var wppaBGcolorNumbarActive = 'transparent';
var wppaBcolorNumbarActive = 'transparent';
var wppaFontFamilyNumbar = '';
var wppaFontSizeNumbar = '';
var wppaFontColorNumbar = '';
var wppaFontWeightNumbar = '';
var wppaFontFamilyNumbarActive = '';
var wppaFontSizeNumbarActive = '';
var wppaFontColorNumbarActive = '';
var wppaFontWeightNumbarActive = '';
var wppaNumbarMax = '10';
var wppaAjaxUrl = '';
var wppaLang = '';
var wppaNextOnCallback = false;
var wppaStarOpacity = 0.2;
var wppaSlideWrap = true;
var wppaLightBox = [];
var wppaEmailRequired = 'required';
var wppaSlideBorderWidth = 0;
var wppaSlideInitRunning = [];
var wppaAnimationType = 'fadeover';
var wppaSlidePause = [];
var wppaSlideBlank = [];
var wppaRatingMax = 5;
var wppaRatingDisplayType = 'graphic';
var wppaRatingPrec = 2;
var wppaFilmPageSize = [];
var wppaAspectRatio = [];
var wppaFullSize = [];
var wppaStretch = false;
var wppaThumbSpaceAuto = false;
var wppaMinThumbSpace = 4;
var wppaMagnifierCursor = '';
var wppaArtMonkyLink = 'none';
var wppaAutoOpenComments = false;
var wppaUpdateAddressLine = false;
var wppaFilmThumbTitle = '';
var wppaUploadUrl = '';
var wppaVoteForMe = '';
var wppaVotedForMe = '';
var wppaSlideSwipe = true;
var wppaLightboxSingle = [];
var wppaMaxCoverWidth = 300;	// For responsive multicolumn covers
var wppaDownLoad = 'Download';
var wppaSiteUrl = '';
var wppaWppaUrl = '';
var wppaIncludeUrl = '';
var wppaSlideToFullpopup = false;
var wppaComAltSize = 75;
var wppaBumpViewCount = true;
var wppaFotomoto = false;
var wppaArtMonkeyButton = true;
var wppaShortQargs = false;
var wppaOvlHires = false;
var wppaMasonryCols = [];
var wppaVideoPlaying = [];
var wppaAudioPlaying = [];
var wppaSlideVideoStart = false;
var wppaSlideAudioStart = false;
var wppaAudioHeight = 28;
var wppaHis = 0;
var wppaStartHtml = [];
var wppaCanAjaxRender = false;				// Assume failure
var wppaCanPushState = false;
var wppaAllowAjax = true;					// Assume we are allowed to use ajax
var wppaMaxOccur = 0;
var wppaFirstOccur = 0;
var wppaUsePhotoNamesInUrls = false;
var wppaShareHideWhenRunning = false;
var wppaCommentRequiredAfterVote = true;
var wppaTopMoc = 0;							// Set by wppa_functions.php -> function wppa_container( 'open' );
var wppaColWidth = [];						// [mocc] Set by wppa_functions.php -> function wppa_container( 'open' );
var wppaFotomotoHideWhenRunning = false;	// Set by wppa-non-admin.php -> wppa_create_wppa_init_js();
var wppaFotomotoMinWidth = 400;				// Set by wppa-non-admin.php -> wppa_create_wppa_init_js();
var wppaPhotoView = [];						// [id] Set to true by a bump viewcount to prevent duplicate bumps.

var wppaBackgroundColorImage = '';
var wppaPopupLinkType = '';
var wppaPopupOnclick = [];
var wppaThumbTargetBlank = false;
var wppaRel = 'rel';
var wppaStartSymbolUrl = '';
var wppaPauseSymbolUrl = '';
var wppaStopSymbolUrl = '';
var wppaStartPauseSymbolSize = '64';
var wppaStartPauseSymbolBradius = '32';
var wppaStopSymbolSize = '48';
var wppaStopSumbolBradius = '24';

// 'Internal' variables ( private )
var _wppaId = [];
var _wppaAvg = [];
var _wppaDisc = [];
var _wppaMyr = [];
var _wppaVRU = [];							// Vote Return Url
var _wppaLinkUrl = [];
var _wppaLinkTitle = [];
var _wppaLinkTarget = [];
var _wppaCommentHtml = [];
var _wppaIptcHtml = [];
var _wppaExifHtml = [];
var _wppaToTheSame = false;
var _wppaSlides = [];
var _wppaNames = [];
var _wppaFullNames = [];
var _wppaDsc = [];
var _wppaOgDsc = [];
var _wppaCurIdx = [];
var _wppaNxtIdx = [];
var _wppaTimeOut = [];
var _wppaSSRuns = [];
var _wppaFg = [];
var _wppaTP = [];
var _wppaIsBusy = [];
var _wppaFirst = [];
var _wppaVoteInProgress = false;
var _wppaTextDelay;
var _wppaUrl = [];
var _wppaSkipRated = [];
var _wppaLbTitle = [];
var _wppaStateCount = 0;
var _wppaDidGoto = [];
var _wppaShareUrl = [];
var _wppaShareHtml = [];
var _wppaFilmNoMove = [];
var _wppaHiresUrl = [];
var _wppaIsVideo = [];
var _wppaVideoHtml = [];
var _wppaAudioHtml = [];
var _wppaVideoNatWidth = [];
var	_wppaVideoNatHeight = [];

var __wppaOverruleRun = false;

var wppaOvlUrls;
var wppaOvlTitles;
var wppaOvlIdx = 0;
var wppaOvlFirst = true;
var wppaOvlKbHandler = '';
var wppaOvlSizeHandler = '';
var wppaOvlPadTop = 5;
var wppaOvlIsSingle;
var wppaOvlRunning = false;
var wppaOvlVideoHtmls;
var wppaOvlAudioHtmls;
var wppaOvlVideoNaturalWidths;
var wppaOvlVideoNaturalHeights;
var wppaOvlModeInitial = 'normal';
var wppaOvlVideoPlaying = false;
var wppaOvlAudioPlaying = false;
var wppaOvlShowLegenda = true;
var wppaOvlShowStartStop = true;
var wppaOvlRadius = 0;
var wppaOvlBorderWidth = 16;
var wppaOvlLeftSymbolUrl;
var wppaOvlRightSymbolUrl;
var wppaLeftRightSymbolSize = 32;
var wppaLeftRightSymbolBradius = 4;


// The next lightbox var values become overwritten in wppa-non-admin.php -> wppa_load_footer()
// Therefor they are placed here!
var wppaOvlTxtHeight = 36;	// 12 * ( n lines of text including the n/m line )
var wppaOvlOpacity = 0.8;
var wppaOvlOnclickType = 'none';
var wppaOvlTheme = 'black';
var wppaOvlAnimSpeed = 300;
var wppaOvlSlideSpeed = 3000;
var wppaVer4WindowWidth = 800;
var wppaVer4WindowHeight = 600;
var wppaOvlFontFamily = 'Helvetica';
var wppaOvlFontSize = '10';
var wppaOvlFontColor = '';
var wppaOvlFontWeight = 'bold';
var wppaOvlLineHeight = '12';
var wppaOvlShowCounter = true;
var wppaOvlIsVideo = false;
var wppaShowLegenda = '';
var wppaOvlFsPhotoId = 0;
var wppaPhotoId = 0;
var wppaOvlVideoStart = false;
var wppaOvlAudioStart = false;
var wppaLastIptc = '';
var wppaLastExif = '';

// Init at dom ready
jQuery( document ).ready(function() {
	var anyAutocol = false;

	// Check for occurrences that are responsive
	for ( mocc = 1; mocc <= wppaTopMoc; mocc++ ) {
		if ( wppaAutoColumnWidth[mocc] ) {
			wppaColWidth[mocc] = 0;
			_wppaDoAutocol( mocc );
			anyAutocol = true;
		}
	}

	// Misc. init
	_wppaTextDelay = wppaAnimationSpeed;
	if ( wppaFadeInAfterFadeOut ) _wppaTextDelay *= 2;

	// Install resize handler
	if ( anyAutocol ) {
		jQuery( window ).resize(function() {
			for ( mocc = 1; mocc <= wppaTopMoc; mocc++ ) {
				if ( wppaAutoColumnWidth[mocc] ) {
					wppaColWidth[mocc] = 0;
					_wppaDoAutocol( mocc );
				}
			}
		});
	}
});

// Initialize Ajax render partial page content with history update
jQuery( document ).ready( function( e ) {

	// Are we allowed and capable to ajax?
	if ( wppaAllowAjax && jQuery.ajax() ) {
		wppaCanAjaxRender = true;
	}

	// Can we do history.pushState ?
	if ( typeof( history.pushState ) != 'undefined' ) {

		// Save entire initial page content ( I do not know which container is going to be modified first )
		var i=1;
		while ( i <= wppaMaxOccur ) {
			wppaStartHtml[i] = jQuery( '#wppa-container-'+i ).html();
			i++;
		}

		wppaCanPushState = true;
	}
});

// On the fly init lightbox
function wppaUpdateLightboxes() {

	// Native wppa lightbox
	if ( typeof( wppaInitOverlay ) == 'function' ) {
		wppaInitOverlay();
	}

	// Lightbox-3
	if ( typeof( myLightbox ) != 'undefined' ) {
		if ( typeof( myLightbox.updateImageList ) == 'function' ) {
			myLightbox.updateImageList();
		}
	}

	// PrettyPhoto
	if ( jQuery().prettyPhoto ) {
		jQuery( "a[rel^='prettyPhoto']" ).prettyPhoto( {
			deeplinking: false,
		});
	}
}

// Stop video of a given occurrency
function wppaStopVideo( mocc ) {
	var id = [];
	var video;
	var i;

	id[1] = 'wppa-overlay-img';
	id[2] = 'theimg0-'+mocc;
	id[3] = 'theimg1-'+mocc;
	i = 0;

	while ( i < 3 ) {
		i++;
		if ( i != 1 || mocc == 0 ) {	// Stop video on lighbox only when mocc = 0, i.e. stop all video
			video = document.getElementById( id[i] );
			if ( video ) {
				if ( typeof( video.pause ) == 'function' ) {
					video.pause();
					wppaConsoleLog( 'Video '+id[i]+' paused', 'force' );
				}
			}
		}
	}
}

// Stop all audio
function wppaStopAudio() {

	var items = jQuery( 'audio' );

	if ( items.length > 0 ) {
		var i = 0;
		while ( i < items.length ) {
			if ( jQuery( items[i] ).attr( 'data-from' ) == 'wppa' ) {
				items[i].pause();
			}
			i++;
		}
	}
}

// Convert a thumbnail url to a fs url
function wppaMakeFullsizeUrl( url ) {
var temp;
var temp2;

	url = url.replace( '/thumbs/', '/' );	// Not a thumb
	// Remove sizespec for Cloudinary
	temp = url.split( '//' );
	if ( temp[1] ) {
		temp2 = temp[1].split( '/' );
		url = temp[0]+'//';
	}
	else {
		temp2 = temp[0].split( '/' );
		url = '';
	}
	var j = 0;
	while ( j < temp2.length ) {
		var chunk = temp2[j];
		var w = chunk.split( '_' );
		if ( w[0] != 'w' ) {
			if ( j != 0 ) url += '/';
			url += chunk;
		}
		j++;
	}
	return url;
}

// Get the width of a container
function wppaGetContainerWidth( mocc ) {
	var elm = document.getElementById( 'wppa-container-'+mocc );
	var w = 0;

	if ( ! wppaAutoColumnWidth[mocc] ) return elm.clientWidth;

	while ( w == 0 ) {
		elm = elm.parentNode;
		w = jQuery( elm ).width();
	}

	return w * wppaAutoColumnFrac[mocc];
}

// Do the responsive size adjustment
function _wppaDoAutocol( mocc ) {

	wppaConsoleLog( 'Doing autocol '+mocc );

	if ( ! wppaAutoColumnWidth[mocc] ) return;	// Not auto

	var w;
	var h;

	// Container
	w = wppaGetContainerWidth( mocc );//document.getElementById( 'wppa-container-'+mocc ).parentNode.clientWidth;
	wppaColWidth[mocc] = w;
	jQuery( ".wppa-container-"+mocc ).css( 'width',w );

	// Covers
	jQuery( ".wppa-asym-text-frame-"+mocc ).css( 'width',w - wppaTextFrameDelta );
	jQuery( ".wppa-cover-box-"+mocc ).css( 'width',w - wppaBoxDelta );

	// Multi Column Responsive covers
	var exists = jQuery( ".wppa-cover-box-mcr-"+mocc );
	var MCRWidth;
	if ( exists.length > 1 ) {	// Yes there are
		var nCovers = parseInt( ( w + 8 )/( wppaMaxCoverWidth+8 ) ) + 1;
		var coverMax1 = nCovers - 1;
		MCRWidth = parseInt( ( ( w + 8 )/nCovers ) - 8 );
		var idx = 0;
		while ( idx < exists.length ) {
			var col = idx % nCovers;
			switch ( col ) {
				case 0:	/* left */
					jQuery( exists[idx] ).css( {'marginLeft': '0px', 'clear': 'both', 'float': 'left'});
					break;
				case coverMax1:	/* right */
					jQuery( exists[idx] ).css( {'marginLeft': '8px', 'clear': 'none', 'float': 'right'});
					break;
				default:
					jQuery( exists[idx] ).css( {'marginLeft': '8px', 'clear': 'none', 'float': 'left'});
			}
			idx++;
		}
		jQuery( ".wppa-asym-text-frame-mcr-"+mocc ).css( 'width',MCRWidth - wppaTextFrameDelta );
		jQuery( ".wppa-cover-box-mcr-"+mocc ).css( 'width',MCRWidth - wppaBoxDelta );
	}
	else {	// One cover: full width, 0 covers don't care
		jQuery( ".wppa-asym-text-frame-mcr-"+mocc ).css( 'width',w - wppaTextFrameDelta );
		jQuery( ".wppa-cover-box-mcr-"+mocc ).css( {'width': ( w - wppaBoxDelta ), 'marginLeft': '0px', 'float':  'left'});
	}

	// Thumbnail area
	jQuery( ".wppa-thumb-area-"+mocc ).css( 'width',w - wppaThumbnailAreaDelta );

	// Thumbframes default
	if ( wppaThumbSpaceAuto ) {
		var tfw = parseInt( jQuery( ".thumbnail-frame-"+mocc ).css( 'width' ) );
		if ( tfw ) {
			var minspc = wppaMinThumbSpace;
			var weff = w - wppaThumbnailAreaDelta - 7;
			var nthumbs = parseInt( weff / ( tfw + minspc ) );
			var availsp = weff - nthumbs * tfw;
			var newspc = parseInt( 0.5 + availsp / ( nthumbs+1 ) );

			jQuery( ".thumbnail-frame-"+mocc ).css( {marginLeft:newspc});
		}
	}

	// Comalt thumbmails
	jQuery( ".thumbnail-frame-comalt-"+mocc ).css( 'width', w - wppaThumbnailAreaDelta );
	jQuery( ".wppa-com-alt-"+mocc ).css( 'width', w - wppaThumbnailAreaDelta - wppaComAltSize - 20 );

	// Masonry thumbnails horizontal
	var row = 1;
	var rowHeightPix;
	var rowHeightPerc = jQuery( '#wppa-mas-h-'+row+'-'+mocc ).attr( 'data-height-perc' );
	while ( rowHeightPerc ) {
		rowHeightPix = rowHeightPerc * ( w - wppaThumbnailAreaDelta ) / 100;
		jQuery( '#wppa-mas-h-'+row+'-'+mocc ).css( 'height', rowHeightPix );
		row++;
		rowHeightPerc = jQuery( '#wppa-mas-h-'+row+'-'+mocc ).attr( 'data-height-perc' );
	}

	// Fix bug in ie and chrome
	wppaSetMasHorFrameWidthsForIeAndChrome(mocc);

	// User upload
	jQuery( ".wppa-file-"+mocc ).css( 'width',w - 16 );

	// User upload responsive covers
	jQuery( ".wppa-file-mcr-"+mocc ).css( 'width', MCRWidth - wppaBoxDelta - 6 );

	// Slide
	wppaFormatSlide( mocc );

	// Audio on slide
	jQuery( "#audio-slide-"+mocc ).css( 'width', w - wppaBoxDelta - 6 );

	// Comments
	jQuery( ".wppa-comment-textarea-"+mocc ).css( 'width',w * 0.7 );

	// Filmstrip
	wppaFilmStripLength[mocc] = w - wppaFilmStripAreaDelta[mocc];
	jQuery( "#filmwindow-"+mocc ).css( 'width',wppaFilmStripLength[mocc] );
	_wppaAdjustFilmstrip( mocc );	// reposition content

	// Texts in slideshow and browsebar
	if ( ! wppaIsMini[mocc] && typeof( _wppaSlides[mocc] ) != 'undefined' ) {	// Mini is properly initialized
		if ( wppaColWidth[mocc] < wppaMiniTreshold ) {
			jQuery( '#prev-arrow-'+mocc ).html( wppaPrevP );
			jQuery( '#next-arrow-'+mocc ).html( wppaNextP );
			jQuery( '#wppa-avg-rat-'+mocc ).html( wppaAvgRat );
			jQuery( '#wppa-my-rat-'+mocc ).html( wppaMyRat );

			jQuery( '#counter-'+mocc ).html( ( _wppaCurIdx[mocc]+1 )+' / '+_wppaSlides[mocc].length );
		}
		else {
			jQuery( '#prev-arrow-'+mocc ).html( wppaPreviousPhoto );
			jQuery( '#next-arrow-'+mocc ).html( wppaNextPhoto );
			jQuery( '#wppa-avg-rat-'+mocc ).html( wppaAvgRating );
			jQuery( '#wppa-my-rat-'+mocc ).html( wppaMyRating );

			jQuery( '#counter-'+mocc ).html( wppaPhoto+' '+( _wppaCurIdx[mocc]+1 )+' '+wppaOf+' '+_wppaSlides[mocc].length );
		}
	}

	// Single photo
	jQuery( ".wppa-sphoto-"+mocc ).css( 'width',w );
	jQuery( ".wppa-simg-"+mocc ).css( 'width',w - 2*wppaSlideBorderWidth );
	jQuery( ".wppa-simg-"+mocc ).css( 'height', '' );

	// Mphoto
	jQuery( ".wppa-mphoto-"+mocc ).css( 'width',w + 10 );
	jQuery( ".wppa-mimg-"+mocc ).css( 'width',w );
	jQuery( ".wppa-mimg-"+mocc ).css( 'height', '' );

}

// Fix bug in IE and Chrome
function wppaSetMasHorFrameWidthsForIeAndChrome(mocc) {
	// For IE and Chrome there is the class .wppa-mas-h-{mocc}
	// Set widths of frames for IE and Chrome. These browsers interprete width:auto
	// sometimes not in relation to the specified height, but to the available space.
	var frames = jQuery( '.wppa-mas-h-'+mocc );
	var tnm = wppaMinThumbSpace;
	for ( var i=0;i<frames.length;i++ ) {

		var img = wppaGetChildI( frames[i] );
		if ( img ) {
			if ( img.nodeName == 'IMG' ) {
				if ( ! img.complete ) {
					setTimeout( 'wppaSetMasHorFrameWidthsForIeAndChrome( '+mocc+' )', 400 ); // Try again after 400 ms.
					return;
				}
			}
			var wd = ( ( img.naturalWidth ) / ( img.naturalHeight ) * ( img.height ) ) + tnm;
			jQuery( frames[i] ).css( {'width': wd } );
		}
	}
}
// get (grand)child of parent with id like i-...
function wppaGetChildI( parent ) {

	var children = parent.childNodes;
	var img = false;
	var i;

	for ( i=0; i<children.length; i++ ) {
		var child = children[i];
		if ( child.id ) {
			if ( child.id.substr( 0, 2 ) == 'i-' ) {
				return child;
			}
		}
		var grandChild = wppaGetChildI( child );
		if ( grandChild ) {
			return grandChild;
		}
	}
	return false;
}

// Fotomoto
var wppaFotomotoLoaded = false;
var wppaFotomotoToolbarIds = [];
function fotomoto_loaded() {
	wppaFotomotoLoaded = true;
}
function wppaFotomotoToolbar( mocc, url ) {
	if ( wppaColWidth[mocc] >= wppaFotomotoMinWidth ) {	// Space enough to show the toolbar
		jQuery( '#wppa-fotomoto-container-'+mocc ).css( 'display','inline' );
		jQuery( '#wppa-fotomoto-checkout-'+mocc ).css( 'display','inline' );
	}
	else {
		jQuery( '#wppa-fotomoto-container-'+mocc ).css( 'display','none' );
		jQuery( '#wppa-fotomoto-checkout-'+mocc ).css( 'display','none' );
		return;	// Too small
	}
	if ( wppaFotomoto && document.getElementById( 'wppa-fotomoto-container-'+mocc ) ) { // Configured and container present
		if ( wppaFotomotoLoaded ) {
			FOTOMOTO.API.checkinImage( url );
			wppaFotomotoToolbarIds[mocc] = FOTOMOTO.API.showToolbar( 'wppa-fotomoto-container-'+mocc, url );
		}
		else { // Not loaded yet, retry after 200 ms
			setTimeout( 'wppaFotomotoToolbar( '+mocc+',"'+url+'" )', 200 );
			wppaConsoleLog( 'Waiting for Fotomoto' );
		}
	}
}
function wppaFotomotoHide( mocc ) {
	jQuery( '#wppa-fotomoto-container-'+mocc ).css( 'display','none' );
	jQuery( '#wppa-fotomoto-checkout-'+mocc ).css( 'display','none' );
}

// Compute fullsize ( slideshow ) url with current photo
function wppaGetCurrentFullUrl( mocc, idx ) {

var xurl = document.location.href;
var url;

	// Remove &wppa-photo=... if present.
	var temp1 = xurl.split( "?" );
	var temp2 = 'nil';
	var temp3;
	var i = 0;
	var first = true;
	var pfx;

	if ( ! wppaShortQargs ) pfx = 'wppa-';
	else pfx = '';

	if ( temp1[1] ) temp2 = temp1[1].split( "&" );

	var albumSeen = false;
	url = temp1[0];	// everything before '?'
	if ( temp2 != 'nil' ) {
		if ( temp2.length > 0 ) {
			while ( i<temp2.length ) {
				temp3 = temp2[i].split( "=" );
				if ( temp3[0] == pfx+'album' ) albumSeen = true;
				if ( temp3[0] != pfx+'photo' ) {
					if ( first ) url += "?";
					else url += "&";
					first = false;
					url += temp2[i];
				}
				i++;
			}
		}
	}
	if ( ! albumSeen ) return '';

	// Append new &wppa-photo=...
	if ( first ) url += "?";
	else url += "&";
	if ( wppaUsePhotoNamesInUrls ) { //&& ! wppaStringContainsForbiddenChars( _wppaNames[mocc][idx] ) ) {
		url += pfx+'photo='+encodeURIComponent(_wppaNames[mocc][idx]);
	}
	else {
		url += pfx+'photo='+_wppaId[mocc][idx];
	}

	return url;
}

// Sanitize utility
function wppaStringContainsForbiddenChars( str ) {
var forbidden = [ '?', '&', '#', '/', '"', "'" ];
var i=0;

	while ( i < forbidden.length ) {
		if ( str.indexOf( forbidden[i] ) != -1 ) {
			return true;
		}
		i++;
	}
	return false;
}

// Setup an event handler for popstate events
window.onpopstate = function( event ) {
	var occ = 0;
	if ( wppaCanPushState ) {
		if ( event.state ) {
			occ = event.state.occur;
			switch ( event.state.type ) {
				case 'html':
					// Restore wppa container content
					jQuery( '#wppa-container-'+occ ).html( event.state.html );
					break;
				case 'slide':
					// Go to specified slide without the didgoto switch to avoid a stackpush here
					_wppaGoto( occ, event.state.slide );
					break;
			}
		}
		else if ( wppaUpdateAddressLine ) {
		/**/
			occ = wppaFirstOccur;
			// Restore first modified occurrences content
			jQuery( '#wppa-container-'+occ ).html( wppaStartHtml[occ] );
			// Now we are back to the initial page
			wppaFirstOccur = 0;
			// If a photo number given goto that photo
			if ( occ == 0 ) {	// Find current occur if not yet known
				var url = document.location.href;
				var urls = url.split( "&wppa-occur=" );
				occ = parseInt( urls[1] );
			}
			var url = document.location.href;
			var urls = url.split( "&wppa-photo=" );
			var photo = parseInt( urls[1] );
			if ( photo > 0 ) {
				var idx = 0;
				while ( idx < _wppaId[occ].length ) {
					if ( _wppaId[occ][idx] == photo ) break;
					idx++;
				}
				if ( idx < _wppaId[occ].length ) _wppaGoto( occ, idx );
			}
	/*	*/
		}
		// If it is a slideshow, stop it
		if ( document.getElementById( 'theslide0-'+occ ) ) {
			_wppaStop( occ );
		}
	}
	if ( typeof( wppaQRUpdate ) != 'undefined' ) wppaQRUpdate( document.location.href );
//	wppaQRData = document.location.href; //????
};

// Push history stack for a slideshow
function wppaPushStateSlide( mocc, slide, url ) {

	if ( ! wppaIsMini[mocc] ) {	// Not from a widget
		if ( wppaCanPushState && wppaUpdateAddressLine ) {
			if ( url != '' ) {
				try {
					history.pushState( {page: wppaHis, occur: mocc, type: 'slide', slide: slide}, "---", url );
					wppaConsoleLog( 'Slide history stack updated' );
				}
				catch( err ) {
					wppaConsoleLog( 'Slide history stack update failed' );
				}
			}
		}
	}
}

// Filter enables the use of <script> tags inside a script
function wppaRepairScriptTags( text ) {
var temp;
var newtext;

	// Just to be sure we do not run into undefined error
	if ( typeof( text ) == 'undefined' ) return '';

	temp = text.split( '[script' );
	if ( temp.length == 1 ) return text;

	newtext = temp[0];
	var idx = 0;
	while ( temp.length > idx ) {
		newtext += '<script';
		idx++;
		newtext += temp[idx];
	}

	temp = newtext.split( '[/script' );

	newtext = temp[0];
	idx = 0;
	while ( temp.length > idx ) {
		newtext += '</script';
		idx++;
		newtext += temp[idx];
	}

	return newtext;
}

// Filter enables the use of a <br> tag while they are removed with strip_tags
function wppaRepairBrTags( text ) {
var newtext;

	// Just to be sure we do not run into undefined error
	if ( typeof(text) == 'undefined' ) return '';

	newtext = text.replace( '[br /]', '<br />' );
	return newtext;
}

// Replace text that is too long by ellipses
function wppaTrimAlt( text ) {
var newtext;

	// Just to be sure we do not run into undefined error
	if ( typeof(text) == 'undefined' ) return '';

	if ( text.length > 13 ) {
		newtext = text.substr( 0,10 ) + '...';
	}
	else newtext = text;
	return newtext;
}

// Initialize FaceBook sdk
var wppaFbInitBusy = false;
function wppaFbInit() {
	if ( ! wppaFbInitBusy ) {
		if ( typeof( FB ) != 'undefined' ) {
			wppaFbInitBusy = true;				// set busy
			setTimeout( '_wppaFbInit()', 10 ); 	// do it async over 10 ms
		}
		else {
			wppaConsoleLog( 'Fb wait' );
			setTimeout( 'wppaFbInit()', 200 );
		}
	}
	else {
		wppaConsoleLog( 'Fb Init busy' );
	}
}
function _wppaFbInit() {
	FB.init( {status : true, xfbml : true } );
	wppaFbInitBusy = false;
}

// Insert ( an emoticon ) in a comment text
function wppaInsertAtCursor( elm, value ) {
    //IE support
    if ( document.selection ) {
        elm.focus();
        sel = document.selection.createRange();
        sel.text = value;
    }
    //MOZILLA and others
    else if ( elm.selectionStart || elm.selectionStart == '0' ) {
        var startPos = elm.selectionStart;
        var endPos = elm.selectionEnd;
        elm.value = elm.value.substring( 0, startPos )
            + value
            + elm.value.substring( endPos, elm.value.length );
        elm.selectionStart = startPos + value.length;
        elm.selectionEnd = startPos + value.length;
    } else {
        elm.value += value;
    }
}

// Initialize Google Maps
function wppaGeoInit( mocc, lat, lon ) {
	var myLatLng = new google.maps.LatLng( lat, lon );
	var mapOptions = {
		disableDefaultUI: false,
		panControl: false,
		zoomControl: true,
		mapTypeControl: true,
		scaleControl: true,
		streetViewControl: true,
		overviewMapControl: true,
		zoom: 10,
		center: myLatLng,
//			mapTypeId: google.maps.MapTypeId.TERRAIN,
//			mapTypeControlOptions: {
//				mapTypeIds: [ google.maps.MapTypeId.TERRAIN, google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.HYBRID ],
//				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
//			},
	};
	var map = new google.maps.Map( document.getElementById( "map-canvas-"+mocc ), mapOptions );
	var marker = new google.maps.Marker( {
		position: myLatLng,
		map: map,
		title:""
	});

	google.maps.event.addListener( map, "center_changed", function() {
		// 1 second after the center of the map has changed, pan back to the
		// marker.
		window.setTimeout(function() {
		  map.panTo( marker.getPosition() );
		}, 1000 );
	});
}

// Encode funny chars
function wppaEncode( xtext ) {
	var text, result;

	if ( typeof( xtext )=='undefined' ) return;

	text = xtext;
	result = text.replace( /#/g, '||HASH||' );
	text = result;
	result = text.replace( /&/g, '||AMP||' );
	text = result;
//	result = text.replace( /+/g, '||PLUS||' );
	var temp = text.split( '+' );
	var idx = 0;
	result = '';
	while ( idx < temp.length ) {
		result += temp[idx];
		idx++;
		if ( idx < temp.length ) result += '||PLUS||';
	}

	return result;
}

// Compute photo id out of an url
function wppaUrlToId( url ) {
	var temp = url.split( '/wppa/' );		// if '/wppa/' found, a wppa image
	if ( temp.length == 1 ) {
		temp = url.split( '/upload/' );	// if '/upload/' found, a cloudinary image
	}
	if ( temp.length == 1 ) {
		return 0;	// Still nothing, not a wppa image or ahires image, return 0
	}
	// Find image id
	temp = temp[1];
	temp = temp.split( '.' );
	temp = temp[0].replace( '/', '' );
	temp = temp.replace( '/', '' );
	temp = temp.replace( '/', '' );
	temp = temp.replace( '/', '' );
	temp = temp.replace( '/', '' );

	return temp;
}

// Opens/closes selection boxes in supersearch html
function wppaSuperSearchSelect( mocc, go ) {

	// Init
	jQuery( '#wppa-ss-albumopt-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-albumcat-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-albumname-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-albumtext-'+mocc ).css('display', 'none');
	jQuery( '#wppa-ss-photoopt-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-photoname-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-photoowner-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-phototag-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-phototext-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-photoexif-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-photoiptc-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-exifopts-'+mocc ).css('display', 'none');
		jQuery( '#wppa-ss-iptcopts-'+mocc ).css('display', 'none');

	jQuery( '#wppa-ss-spinner-'+mocc ).css('display', 'none');
	jQuery( '#wppa-ss-button-'+mocc ).css('display', 'none');

	var s1 = jQuery( '#wppa-ss-pa-'+mocc ).val();
	var s2 = '';
	var s3 = '';
	var data = '';
	switch ( s1 ) {
		case 'a':
			jQuery( '#wppa-ss-albumopt-'+mocc ).css('display', '');
			s2 = jQuery( '#wppa-ss-albumopt-'+mocc ).val();
			switch ( s2 ) {
				case 'c':
					jQuery( '#wppa-ss-albumcat-'+mocc ).css('display', '');
					var set = jQuery( '.wppa-ss-albumcat-'+mocc );
					data = '';
					var i;
					for ( i = 0; i < set.length; i++ ) {
						if ( jQuery( set[i] ).attr( 'selected' ) == 'selected' ) {
							data += '.' + jQuery( set[i] ).val();
						}
					}
					data = data.substr( 1 );

					if ( data != '' ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 'n':
					jQuery( '#wppa-ss-albumname-'+mocc ).css('display', '');
					data = jQuery( '#wppa-ss-albumname-'+mocc ).val();
					if ( data != null ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 't':
					jQuery( '#wppa-ss-albumtext-'+mocc ).css('display', '');
					var set = jQuery( '.wppa-ss-albumtext-'+mocc );
					data = '';
					var i;
					for ( i = 0; i < set.length; i++ ) {
						if ( jQuery( set[i] ).attr( 'selected' ) == 'selected' ) {
							data += '.' + jQuery( set[i] ).val();
						}
					}
					data = data.substr( 1 );

					if ( data != '' ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
			}
			break;

		case 'p':
			jQuery( '#wppa-ss-photoopt-'+mocc ).css('display', '');
			s2 = jQuery( '#wppa-ss-photoopt-'+mocc ).val();
			switch ( s2 ) {
				case 'n':
					jQuery( '#wppa-ss-photoname-'+mocc ).css('display', '');
					data = jQuery( '#wppa-ss-photoname-'+mocc ).val();
					if ( data != null ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 'o':
					jQuery( '#wppa-ss-photoowner-'+mocc ).css('display', '');
					data = jQuery( '#wppa-ss-photoowner-'+mocc ).val();
					if ( data != null ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 'g':
					jQuery( '#wppa-ss-phototag-'+mocc ).css('display', '');
					var set = jQuery( '.wppa-ss-phototag-'+mocc );
					data = '';
					var i;
					for ( i=0; i<set.length; i++ ) {
						if ( jQuery( set[i] ).attr( 'selected' ) == 'selected' ) {
							data += '.' + jQuery( set[i] ).val();
						}
					}
					data = data.substr( 1 );

					if ( data != '' ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 't':
					jQuery( '#wppa-ss-phototext-'+mocc ).css('display', '');
					var set = jQuery( '.wppa-ss-phototext-'+mocc );
					data = '';
					var i;
					for ( i=0; i<set.length; i++ ) {
						if ( jQuery( set[i] ).attr( 'selected' ) == 'selected' ) {
							data += '.' + jQuery( set[i] ).val();
						}
					}
					data = data.substr( 1 );

					if ( data != '' ) {
						jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
					}
					break;
				case 'i':
					jQuery( '#wppa-ss-photoiptc-'+mocc ).css('display', '');
					s3 = jQuery( '#wppa-ss-photoiptc-'+mocc ).val();
					if ( s3 ) {
						if ( s3.length > 2 ) {
							s3 = s3.replace( '#', 'H' );	// Replace # by H
						}
						if ( s3 != '' ) {
							jQuery( '#wppa-ss-iptcopts-'+mocc ).css('display', '');
							if ( wppaLastIptc != s3 ) {
								wppaAjaxGetSsIptcList( mocc, s3, 'wppa-ss-iptcopts-'+mocc );
								wppaLastIptc = s3;
							}
							else {
								data = jQuery( '#wppa-ss-iptcopts-'+mocc ).val();
								if ( data != null && data != '' ) {
									jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
								}
							}
						}
					}
					break;
				case 'e':
					jQuery( '#wppa-ss-photoexif-'+mocc ).css('display', '');
					s3 = jQuery( '#wppa-ss-photoexif-'+mocc ).val();
					if ( s3 ) {
						if ( s3.length > 2 ) {
							s3 = s3.replace( '#', 'H' );	// Replace # by H
						}
						if ( s3 != '' ) {
							jQuery( '#wppa-ss-exifopts-'+mocc ).css('display', '');
							if ( wppaLastExif != s3 ) {
								wppaAjaxGetSsExifList( mocc, s3, 'wppa-ss-exifopts-'+mocc );
								wppaLastExif = s3;
							}
							else {
								data = jQuery( '#wppa-ss-exifopts-'+mocc ).val();
								if ( data != null && data != '' ) {
									jQuery( '#wppa-ss-button-'+mocc ).css('display', '');
								}
							}
						}
					}
					break;
			}
			break;
	}

	// Find results
	if ( go ) {
		var url = jQuery( '#wppa-ss-pageurl-'+mocc ).val();
		if ( url.indexOf( '?' ) == -1 ) {
			url += '?';
		}
		else {
			url += '&';
		}
		url += 'occur=1&wppa-supersearch='+s1+','+s2+','+s3+','+data;
		document.location.href = url;
	}
}

// Supersearch function get iptc list
// These functions may not be moved to wppa-ajax-front.js because they MUST be loaded in the header!
function wppaAjaxGetSsIptcList( mocc, s3, selid ) {

	jQuery.ajax( { 	url: 		wppaAjaxUrl,
					data: 		'action=wppa' +
								'&wppa-action=getssiptclist' +
								'&tag=' + s3 +
								'&moccur=' + mocc,
					async: 		true,
					type: 		'GET',
					timeout: 	10000,
					beforeSend: function( xhr ) {
									jQuery( '#wppa-ss-spinner-'+mocc ).css('display', '');
								},
					success: 	function( result, status, xhr ) {
									jQuery( '#'+selid ).html( result );
									jQuery( '#wppa-ss-iptcopts-'+mocc ).css('display', '');
									wppaSuperSearchSelect( mocc );
									setTimeout( 'wppaSetIptcExifSize( ".wppa-iptclist-'+mocc+'", "#'+selid+'" )', 10 );
								},
					error: 		function( xhr, status, error ) {
									wppaConsoleLog( 'wppaAjaxGetSsIptcList failed. Error = ' + error + ', status = ' + status, 'force' );
								},
					complete: 	function( xhr, status, newurl ) {
									jQuery( '#wppa-ss-spinner-'+mocc ).css('display', 'none');
								}
				} );

}

// Supersearch function get exif list
function wppaAjaxGetSsExifList( mocc, s3, selid ) {

	jQuery.ajax( { 	url: 		wppaAjaxUrl,
					data: 		'action=wppa' +
								'&wppa-action=getssexiflist' +
								'&tag=' + s3 +
								'&moccur=' + mocc,
					async: 		true,
					type: 		'GET',
					timeout: 	10000,
					beforeSend: function( xhr ) {
									jQuery( '#wppa-ss-spinner-'+mocc ).css('display', '');
								},
					success: 	function( result, status, xhr ) {
									jQuery( '#'+selid ).html( result );
									jQuery( '#wppa-ss-exifopts-'+mocc ).css('display', '');
									wppaSuperSearchSelect( mocc );
									setTimeout( 'wppaSetIptcExifSize( ".wppa-exiflist-'+mocc+'", "#'+selid+'" )', 10 );
								},
					error: 		function( xhr, status, error ) {
									wppaConsoleLog( 'wppaAjaxGetSsExifList failed. Error = ' + error + ', status = ' + status, 'force' );
								},
					complete: 	function( xhr, status, newurl ) {
									jQuery( '#wppa-ss-spinner-'+mocc ).css('display', 'none');
								}
				} );

}

// Supersearch function set size of exif/iptc itemlist
function wppaSetIptcExifSize( clas, selid ) {
	var t = jQuery( clas );
	var n = t.length;
	if ( n > 6 ) n = 6;
	if ( n < 2 ) n = 2;
	jQuery( selid ).attr( 'size', n );
}

function wppaUpdateSearchRoot( text, root ) {
	var items = jQuery( ".wppa-search-root" );
	var i = 0;
	while ( i < items.length ) {
		jQuery( items[i] ).html( text );
		i++;
	}
	items = jQuery( ".wppa-rootbox" );
	i = 0;
	while ( i < items.length ) {
		if ( root ) {
			jQuery( items[i] ).removeAttr( 'checked' );
			jQuery( items[i] ).removeAttr( 'disabled' );
		}
		else {
			jQuery( items[i] ).attr( 'checked', 'checked' );
			jQuery( items[i] ).attr( 'disabled', 'disabled' );
		}
		i++;
	}
	items = jQuery( ".wppa-search-root-id" );
	i = 0;
	while ( i < items.length ) {
		jQuery( items[i] ).attr( 'value', root );
		i++;
	}
}

function wppaSubboxChange( elm ) {
	if ( jQuery( elm ).attr( 'checked' ) == 'checked' ) {
		var items = jQuery( ".wppa-rootbox" );
		var i = 0;
		while ( i < items.length ) {
			jQuery( items[i] ).attr( 'checked', 'checked' );
			i++;
		}
	}
}

function wppaClearSubsearch() {
	var items = jQuery( ".wppa-display-searchstring" );
	var i = 0;
	while ( i < items.length ) {
		jQuery( items[i] ).html( '' );
		i++;
	}
	items = jQuery( ".wppa-search-sub-box" );
	i = 0;
	while ( i < items.length ) {
		jQuery( items[i] ).attr( 'disabled', 'disabled' );
		i++;
	}
}

function wppaEnableSubsearch() {
	var items = jQuery( ".wppa-search-sub-box" );
	var i = 0;
	while ( i < items.length ) {
		jQuery( items[i] ).removeAttr( 'disabled' );
		i++;
	}
}

// Say we're in
wppaConsoleLog( 'wppa.js version '+wppaJsVersion+' loaded.', 'force' );
