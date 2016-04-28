=== WP Photo Album Plus ===
Contributors: opajaap
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=OpaJaap@OpaJaap.nl&item_name=WP-Photo-Album-Plus&item_number=Support-Open-Source&currency_code=USD&lc=US
Tags: photo, album, gallery, slideshow, video, audio, lightbox, iptc, exif, cloudinary, fotomoto
Version: 6.4.09
Stable tag: 6.4.08
Author: J.N. Breetvelt
Author URI: http://www.opajaap.nl/
Requires at least: 3.9
Tested up to: 4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is designed to easily manage and display your photos, photo albums, slideshows and videos in a single as well as in a network WP site.

== Description ==

This plugin is designed to easily manage and display your photo albums and slideshows within your WordPress site.

* You can create various albums that contain photos as well as sub albums at the same time.
* You can mix photos and videos throughout the system.
* There is no limitation to the number of albums and photos.
* There is no limitation to the nesting depth of sub-albums.
* You have full control over the display sizes of the photos.
* You can specify the way the albums are ordered.
* You can specify the way the photos are ordered within the albums, both on a system-wide as well as an per album basis.
* The visitor of your site can run a slideshow from the photos in an album by a single mouseclick.
* The visitor can see an overview of thumbnail images of the photos in album.
* The visitor can browse through the photos in each album you decide to publish.
* Individual thumbnails and slides can be linked to off site urls.
* You can add a Photo of the day Sidebar Widget that displays a photo which can be changed every hour, day or week.
* You can add a Search Sidebar Widget which enables the visitors to search albums and photos for certain words in names and descriptions.
* You can enable a rating system and a supporting Top Ten Photos Sidebar Widget that can hold a configurable number of high rated photos.
* You can enable a comment system that allows visitors to enter comments on individual photos.
* You can add a recent comments on photos Widget.
* Apart from the full-size slideshows you can add a Sidebar Widget that displays a mini slideshow.
* There is a widget to display a number of most recently uploaded photos. It can be configured systemwide and/or on an album basis.
* There is a General Purpose widget that is a text widget wherein you can use wppa+ script commands.
* There is an album widget that displays thumbnail images that link to album contents.
* There is a QR code widget that will be updated when the content of the page changes.
* There is a tag cloud widget and a multi tag widget to quickly get a selection of photos with (a) certain tag(s).
* There is an upload widget that allows for frontend uploads even when no wppa+ display is on the page.
* Almost all appearance settings can be done in the settings admin page. No php, html or css knowledge is required to customize the appearence of the photo display.
* International language support for static text: Currently included foreign languages files: Dutch, Japanese, French(outdated), Spanish, German.
* International language support for dynamic text: Album and photo names and descriptions fully support the qTranslate multilanguage rules.
* Contains embedded lightbox support but also supports lightbox 3.
* You can add watermarks to the photos.
* The plugin supports IPTC and EXIF data.
* Supports WP supercache. The cache will be cleared whenever required for wppa+.
* Supports Cube Points. You can assign points to comments and votes.
* There is an easy way to import existing NextGen galleries into WPPA+ albums.

Plugin Admin Features:

You can find the plugin admin section under Menu Photo Albums on the admin screen.

* Photo Albums: Create and manage Albums.
* Upload photos: To upload photos to an album you created.
* Import photos: To bulk import photos to an album that are previously been ftp'd.
* Settings: To control the various settings to customize your needs.
* Sidebar Widget: To specify the behaviour for an optional sidebar photo of the day widget.
* Help & Info: Much information about how to...

Translations:

<ul>
<li>Dutch translation by OpaJaap himself (<a href="http://www.opajaap.nl">Opa Jaap's Weblog</a>) (both)</li>
<li>Slovak translation by Branco Radenovich (<a href="http://webhostinggeeks.com/user-reviews/">WebHostingGeeks.com</a>) (frontend)</li>
<li>Polish translation by Maciej Matysiak (both)</li>
<li>Ukranian translation by Michael Yunat (<a href="http://getvoip.com/blog">http://getvoip.com</a>) (both)</li>
<li>Italian translation by Giacomo Mazzullo (<a href="http://gidibao.net">http://gidibao.net</a> & <a href="http://charmingpress.com">http://charmingpress.com</a>) (both)</li>
</ul>

== Installation ==

= Requirements =

* The plugin requires at least wp version 3.1.
* The theme should have a call to wp_head() in its header.php file and wp_footer() in its footer.php file.
* The theme should load enqueued scripts in the header if the scripts are enqueued without the $in_footer switch (like wppa.js and jQuery).
* The theme should not prevent this plugin from loading the jQuery library in its default wp manner, i.e. the library jQuery in safe mode (uses jQuery() and not $()).
* The theme should not use remove_action() or remove_all_actions() when it affects actions added by wppa+.
Most themes comply with these requirements.
However, check these requirements in case of problems with new installations with themes you never had used before with wppa+ or when you modifies your theme.
* The server should have at least 64MB of memory.

= Standard installation when not from the wp plugins page =
* Unzip and upload the wppa plugin folder to wp-content/plugins/
* Make sure that the folder wp-content/uploads/ exists and is writable by the server (CHMOD 755, some systems need CHMOD 777)
* Activate the plugin in WP Admin -> Plugins.
* If, after installation, you are unable to upload photos, check the existance and rights (CHMOD 755, some systems need CHMOD 777) of:
for the single site mode installation: the folders .../wp-content/uploads/wppa/ and .../wp-content/uploads/wppa/thumbs/,
and for the multisite mode installation (example for blog id 92): the folders path: .../wp-content/blogs.dir/92/wppa/ and .../wp-content/blogs.dir/92/wppa/thumbs/.
In rare cases you will need to create them manually. You can see the actual pathnames and urls in the lowest table of the Photo Albums -> Settings page.
* If you upgraded from WP Photo Album (without plus) and you had copied wppa_theme.php and/or wppa_style.css
to your theme directory, you must remove them or replace them with the newly supplied versions. The fullsize will be reset to 640 px.
See Table I-A1 and Table I-B1,2 of the Photo Albums -> Settings admin page.

== Frequently Asked Questions ==

= What do i have to do when converting to multisite? =

* If your WP installation is a new installation and you want to have only one - global - WPPA system, add to wp-config.php:
**define( 'WPPA_MULTISITE_GLOBAL', true );**
* If your WP installation is a new installation and you want to have a separate WPPA system for each sub-site, add to wp-config.php:
**define( 'WPPA_MULTISITE_INDIVIDUAL', true );**
* If your WP installation is older than 3.5 an you want to have only one - global - WPPA system, ad to wp-config.php:
**define( 'WPPA_MULTISITE_GLOBAL', true );**
* If your WP installation is older than 3.5 an you want to have a separate WPPA system for each sub-site, add to wp-config.php:
**define( 'WPPA_MULTISITE_BLOGSDIR', true );**
* If you want to convert your multisite WP installation that is prior to 3.5 to a version later than 3.5 and you want to convert an existing WPPA multisite installation
to the new multisite standards, do the following:
1. Update WP to version 3.5 or later.
1. Upate WPPA+ to version 5.4.7 or later.
1. Perform the network migration utility from the network admin which moves all the files from wp-content/blogs.dir/xx to wp-content/uploads/sites/xx
1. **Add** to wp-config.php: **define( 'WPPA_MULTISITE_INDIVIDUAL', true );**
1. If it is there, **Remove** from wp-config.php: **define( 'WPPA_MULTISITE_BLOGSDIR', true );**

= Which other plugins do you recommand to use with WPPA+, and which not? =

* Recommanded plugins: qTranslate, WP Super Cache, Cube Points, Simple Cart & Buy Now, Google-Maps-GPX-Viewer.
* Plugins that break up WPPA+: My Live Signature.
* Google Analytics for WordPress will break the slideshow in most cases when *Track outbound clicks & downloads:* has been checked in its configuration.

= Which themes have problems with wppa+ ? =

* Photocrati has a problem with the wppa+ embedded lightbox when using page templates with sidebar.

= Are there special requirements for responsive (mobile) themes? =

* Yes! Go to the Photo Albums -> Settings admin page. Enter **auto** in Table I-A1. Lowercase letters, no quotes.
* Do not use size="[any number]", use size="0.80" for 80% with etc.
* If you use the Slideshow widget, set the width also to **auto**, and the vertical alignment to **fit**.
* You may also need to change the thumbnail sizes for widgets in *Table I-F 2,4,6 and 8*. Set to 75 if you want 3 columns in the theme *Responsive*.

= After update, many things seem to go wrong =

* After an update, always clear your browser cache (CTRL+F5) and clear your temp internetfiles, this will ensure the new versions of js files will be loaded.
* And - most important - if you use a server side caching program (like WP Total Cavhe) clear its cache.
* Make sure any minifying plugin (like W3 Total Cache) is also reset to make sure the new version files are used.
* Visit the Photo Albums -> Settings page -> Table VII-A1 and press Do it!
* When upload fails after an upgrade, one or more columns may be added to one of the db tables. In rare cases this may have been failed.
Unfortunately this is hard to determine.
If this happens, make sure (ask your hosting provider) that you have all the rights to modify db tables and run action Table VII-A1 again.

= How does the search widget work? =

* A space between words means AND, a comma between words means OR.
Example: search for 'one two, three four, five' gives a result when either 'one' AND 'two' appears in the same (combination of) name and description.
If it matches the name and description of an album, you get the album, and photo vice versa.
OR this might apply for ('three' AND 'four') OR 'five'.
If you use indexed search, the tokens must be at least 3 characters in length.

= How can i translate the plugin into my language? =

* See the documentation on the WPPA+ Docs & Demos site: http://wppa.opajaap.nl/?page_id=1349

= How do i install a hotfix? =

* See the documentation on the WPPA+ Docs & Demos site: http://wppa.opajaap.nl/?page_id=823

= What to do if i get errors during upload or import photos? =

* It is always the best to downsize your photos to the Full Size before uploading. It is the fastest and safest way to add photos tou your photo albums.
Photos that are way too large take unnessesary long time to download, so your visitors will expierience a slow website.
Therefor the photos should not be larger (in terms of pixelsizes) than the largest size you are going to display them on the screen.
WP-photo-album-plus is capable to downsize the photos for you, but very often this fails because of configuration problems.
Here is explained why:
Modern cameras produce photos of 7 megapixels or even more. To downsize the photos to either an automaticly downsized photo or
even a thumbnail image, the server has to create internally a fullsize fullcolor image of the photo you are uploading/importing.
This will require one byte of memory for each color (Red, Green, Blue) and for every pixel.
So, apart form the memory required for the server's program and the resized image, you will need 21 MB (or even more) of memory just for the intermediate image.
As most hosting providers do not allow you more than 64 MB, you will get 'Out of memory' errormessages when you try to upload large pictures.
You can configure WP to use 128 MB (That would be enough in most cases) by specifying *define('WP_MEMORY_LIMIT', '128M');* in wp-config.php,
but, as explained earlier, this does not help when your hosting provider does not allows the use of that much memory.
If you have control over the server yourself: configure it to allow the use of enough memory.
Oh, just Google on 'picture resizer' and you will find a bunch of free programs that will easily perform the resizing task for you.

== Changelog ==

See for additional information: http://wppa.opajaap.nl/changelog/

= 6.4.09 =

= Bug Fixes =

* Fixed wrong aspect ratios of videos on lightbox. Make sure you set ether the default size values correct in Table I-H or on the individual edit photo pages.
* Auto started video on lightbox is no longer stopped by other running slideshows.
* Links with spaces not worked correctly when pretty links are enabled. Fixed.
* The Upload photo dislog on thumbnail areas of real albums now do no longer display an album selection list, but upload takes place to the album involved.

= New Features =

* Table IV-G9 allows you to select a starting mode for lightbox: normal or one of the fullsceen modes.

= Other Changes =

* The cached options are checked for validity by means of a hash check. This will fix spurious unexplainable changing settings. 
* Fixed spurious error messages.
* Videos on lightbox in a set no longer display the start/stop running slideshow button to avoid confusion with the start video button.
* Opening lightbox now only stops the running slideshow where it is initiated from. Previously all shows were stopped, leading to unwanted behaviour in case of running filmonly as a banner applications.

= 6.4.08 =

= Bug Fixes =

* Mail should work always now.
* Download album link did not work when encryption was enabled. Fixed.
* Links do not work when qTranslate-x is active and pretty links is enabled. Fixed.
* Edit photo link on lightbox did not work if the lightbox was invoked on a different image than a slideshow image. Fixed.
* Fixed an issue with avatars not being displayed in all cases.

= New Features =

* The administrator can download any wppa databasa table as .csv file. See Table XII.

= Other Changes =

* You can set Table IV-F4 (Comment email required) now to 'Required', 'Optional' or 'None'.
* Performance improvements, especially of the settings admin page.

= 6.4.07 =

= Bug Fixes =

* When album names in urls enabled, and the name of one album also appears as a part of the name of an other album, the wrong album may be dsiplayed. Fixed.
* Album names in urls did not work on front page. Fixed.
* When the coverimage is a video and the covertype is imagefactory and there is no link on the coverimage, the image was not shown. Fixed.

= New Features =

* Added 'Medals Only' switch to the TopTen widget activation screen.
* Added 'Medals only' option to the #topten virtual album shortcode attribute: album="#topten,{album},{count},medals"

= Other Changes =

* Reduced pageload for slideonly displays.
* Added linktype slideshow for slideshows. This enables the linking from a small slideonly to a fullsize real slideshow.
This is ment to be used in inline settings only to prevent slideshows to link to them selves.
* You can limit the number of slides in a slideonly display in Table I-B10.
* Added Edit link on lightbox (if enabled in Table VII-D2.1).
* Dramaticly reduced duplicate queries. Thanx to the plugin: Query monitor.
* wp_mail() is now used as opposed to mail(), allowing other plugins to modify mail behaviour.

= 6.4.06 =

= Bug Fixes =

* Links containing tags that contained ampersands ( & ) were broken. Fixed.
* If no albums found at #cat in shortcode and cats include subs was ticked, all albums were selected. Fixed.
* The upload shortcode now also works with virtual album selections e.g.: [wppa type="upload" album="#cat,Mycat1;Mycat2"][/wppa]

= New Features =

* You can specify the target shortcode number ( occurrance ) for the Tagcloud widget in Table VI-C3d.
* You can specify the target shortcode number ( occurrance ) for the Tags filter widget in Table VI-C4d.
* New mechanism: Admins Choice. Enables the creation of zipfiles with selected photos by admin or wppa superusres.
Use the Admins Choice widget to make the files downloadable for users. Table IV-A27 to enable it, or just activate the widget.
* On the album admin -> Edit page, the album categor(y)(ies) can now be copied or added to to all (grand)children albums.
* New feature in album spec in a shortcode: album="#cat,Mycat|#tags,Mytag" results in photos with tag Mytag from albums with category Mycat. Multiple tags and cats are allowed.
Note #cat first, without an s, and #tags last, with an s.

= Other Changes =

* Removed calls to deprecated function get_currentuserinfo();

= 6.4.05 =

= Bug Fixes =

* Lightbox did not work on slideshow when it was set to ligtbox single image and there were no other links to lightbox. Fixed.

= Other Changes =

* Extended shortcode attribute: album="#cat,Category" now supports multiple categories:
album="#cat,Cat1;Cat2;Cat3" meaning all albums with category Cat1, Cat2 or Cat3.
album="#cat,Cat1,Cat2,Cat3" meaning all albums with category Cat1, Cat2 and Cat3.
* The search shortcode extensions introduced in version 6.4.03 are now supported by the shortcode generator.
* On hovering the lightbox image, all navigation buttons will fade-in and on leaving the image fad-out now rather than on the bottons themselves.

= 6.4.04 =

= Bug Fixes =

* Fixed a bug in the rights system.

= New Features =

* Table I-B9: Filmstrip Thumbnail Size. The size of the filmstrip images can now be set independantly.

= 6.4.03 =

= Bug Fixes =

* Under some circumstances the album table did not display where it should. Fixed.
* Use hires files for lightbox did not work on slideshows. Fixed.
* In Table XII, the display for WPPA_ALBUMS was incorrect for multisite installations with WPPA_MULTISITE_GLOBAL set to true. Fixed.
* Under some circumstances an empty alertbox appeared after front-end upload. Fixed.

= New Features =

* Extended search widget and shortcode functionality.
In the widget you can select an album as being a fixed search root ( selection will be done in the given album and its (grand)children only ).
Shortcode equivalence for album 47: [wppa type="search" root="#47"][/wppa].
In the widget you can select a landing page. Shortcode equivalence for page id 765: [wppa type="search" root="#47" landing="765"][/wppa].
In the shortcode you can enter any page/post id for the landing page/post, but make sure the page/post has a wppa shortcode - preferrably with: type="landing" - in its content.
Thes options are not yet supported by the shortcode generator.
* Table IV-A26. To switch off the automatic capitilisation of tags and cats.

= Other Changes =

* Removed Italian language files; they are now provided outside wppa by the polylang system.

= 6.4.02 =

= Bug Fixes =

* Removed scoped styles. They only work in Firefox.
* Fixed incorrect initialisation in Import, resulting in failing to import .csv file if no photo import had been done before.
* All photos from mobile devices should be oriented correctly now. If you want to fix the orientation of existing photos: Tick Table VIII-A11a and run VIII-A11.
* Fixed a php warning if smilies are disabled in wp but not in wppa.
* Comment notifications were not always sent when multiple users should receive them. Fixed.

= Other Changes =

* Table VII-B12: Fe alert. Shows alertbox on successful front-end upload/create. Can now be switched off.

= 6.4.01 =

= Bug Fixes =

* Under some circumstances the multitag widget did not work. Fixed.
* Could not update photo at the backend when sncrypted urls was active. Fixed.
* Move and copy photo did not work when the translated text of 'Please select an album to move the photo to first.' contained quotes as in French. Fixed.
* Filmstrip does not display correctly unless Table IV-A19 is ticked when encrypted urls is active. Fixed.

= Other Changes =

* Album crypt is shown on the album admin page.
* Added option: create 'no hotlinks' .htaccess files to Table IV-A18.

= 6.4.00 =

= New Features =

* Privacy phase one. In Table IV-A6.1 you can switch to encrypted urls. Photo and album identifiers in urls must be their encryption codes rather than their db table ids.
This prevents users from 'guessing' ids of photos or albums that they should not be able to see.
If Table IV-A6.2 is also ticked, unencrypted album and photo ids will be refused.
* Album navigator widget selection added: --- owner/public ---. This shows only the logged in users albums and public albums.
* Topten widget selection of albums: added --- owner/public ---. This shows only the photos from albums owned by the logged in user and from public albums.
* Featen widget selection of albums: added --- owner/public ---. This shows only the photos from albums owned by the logged in user and from public albums.
* Allow HTML tags in Custom data fields. Table IX-B1.1. If On: Only script and style tags are stripped, if off (default): All tags are stripped.

= Other Changes =

* Changed Table IV-A18: Enable photo html access to a choice of 'create .htaccess', 'remove .htaccess' (default) or 'do not change' to allow for a custom .htaccess file in ../uploads/wppa/ and ../wppa/thumbs/.
* Added w#rating to the photo description keywords, displaying the rating in float format.

= 6.3.18 =

= Bug Fixes =

* Photo of the day selection result for Display method Change every ( any time period ), when the Use albums selection is - top rated photos - returned Not found. Fixed.
* Go to fullscreen mode by icon button in lightbox did not work properly under certain circumstances. Fixed.
* On the frontpage the links are no longer 'prettyfied' to avoid 404 errors due to improper redirection.

= Other Changes =

* Photo of the day: Day of year is order #, has now jan 1 = 0, in stead of jan 1 = 366.
* Fixed a missing initialization.
* Added alt="..." for the fullscreen icon button in lightbox.
* Table II-H15 allows you to (dis)-connect frontend upload to the camera on mobile divices.
* Ajaxified breadcrumb links.

= 6.3.17 =

= Bug Fixes =

* Fixed plurals for i18n.
* Fixed a capacity issue in photo of the day.

= New Features =

* You can change album order if odering method is set to order# or order # desc in Table IV-D1 for the generc top-level albums, or in the album admin for the albums sub-albums.
The user must have access to all the (sub)albums to be able to change their sequence order number.

= 6.3.16 =

= Bug Fixes =

* Fixed a possible devide by zero error in wppa-breadcrumb.php
* Minor fixes to lightbox. Added 'q' and 'x' to quit(exit) lightbox from all modes.
* In some sites relative urls for lightbox navigation symbols did not work. Fixed.

= 6.3.15 =

= Bug Fixes =

* The keyboard handler for lightbox processed keystrokes twice. Fixed.
* The spinner on lightbox now behaves as designed.

= Other Changes =

* Removed Chech language files, they are now provided by wp polyglot system.
* Noticable improved (pre)loading algorithm for lightbox sets.

= 6.3.14 =

= Bug Fixes =

* The error messages for wrong setting input disappeared. Fixed.
* The album navigator widget vanished. Fixed.

= New Features =

* Alt thumbsize now also works for masonry style thumbnails, if the album is one real album.
The size is still an approximation, due to the implementation of filling the space.

= Other Changes =

* Changed defaults for Table VI-C11 to none, none.
* Lightbox has been face-lifted. See Table I-G3,4 and Table II-K.
* The recent patch for Windows 10 has been reverted. See https://wordpress.org/support/topic/front-end-uploader-not-working-in-explorer?replies=18

= 6.3.13 =

= Bug Fixes =

* Rating did not work when lightbox keyname is other than wppa. Fixed.
* Files with unsanitized names are now correctly removed in Import dir to album.

= New Features =

* You can select various types of layout for the sub-album links on album covers. Table VI-C11.
* Additional checkbox on the Import Photos screen when the input is set to --- My Depot --- or a subfolder thereof: Remove from depot after failed import.
* Added to the photo of the day settings screen: Change every: day of the year is photo order#. Added offset to all 3 day of ... options.

= Other Changes =

* Changed the default settings of the photo of the day feature, so it always works when there are photos and nothing has been configured.
* Layout changes/fixes to the photo of the day preview images.
* If you do not like the textual New and Modified labels, you can now specify urls to custom images. Defaults to the old new.png. See Table IX-D1.5, 1.8 and 1.9
* jQueryfied ajax and improved errorhandling in maintenance operations ( Table VIII ).

= 6.3.12 =

= Bug Fixes =

* The shortcode [wppa_set] stopped working. Fixed.
* Fixed crash in slideshow when language is French and slideshow contained photos with audio and/or video.

= New Features =

* The New indicator is no longer an image, but created with text/css. There is also an 'Modified' indicator, to indicate recently modified albums and photos.
The text and the colors of the indicaors can be set in Table IX-D1.5 and 1.6. Albums now also have a date/time modified stored in their meta data.
An album is now regarded to be 'modified' when the album metadata is changed or when new photos have been uploaded.
Both New and Modified qualifications of sub(sub)albums propagate upward to their (grand)parents.
New has a higher priority than Modified, i.e. if an item is both new and modified, only the new inicator is shown.
For related settings: See Table IX-D1.x.
* On Dir to album import from directories inside the users depot, you can now prevent deletion of the depot files. See Table IX-H17.

= Other Changes =

* Due to bugs in Windows10/Edge, The frontend upload will not use the Ajax method with the progression bar on the Edge browser.
* Table VI-C11 has an additional checkbox to indicate if you want sub-albums to be displayed also.

= 6.3.11 =

= Bug Fixes =

* Updating custom data fields of photo now updates date/time modified.

= New Features =

* You can decide to use date/time modified rather than dat/time of upload on LasTen widget/shortcode. Table IX-D2.2.
* Table VI-C11 makes it possible to show the sub-album names on album covers, linking to the content or the slideshow of the sub-album.
* New album cover image selection option: Most recent from (grand)children.

= Other Changes =

* The conversion to utf8 of iptc data is now optional ( Table IX-H16 ).
* If all albums have unique names, untick Table IX-H15 to get the old behaviour back of importing dirs to albums. ( No tree structure required ).

= 6.3.10 =

= Bug Fixes =

* The Odd/Even toggle of background colors stopped working a few revs ago. Fixed.
If you have unwanted background colors of album covers and/or thumbnail areas, change Table III-B1 ( make equal to III-B2 to get the previous results ).

= New Features =

* You can now import photos from 'outside' wp-content, even outside the wp install dir, on the same servers filesystem.
In Table IX-B17 you can specify the highest root where to search for imporatble files from.
The wppa source directories are not ment to import from, they are skipped by default; if you really want to import from those locations, tick Table IX-B18.
* You can now add an 'Inverse' checkbox to the Multitag widget / box to invert the selection. Table IX-E10.1.
* New shortcode type: url. Example: [wppa type="url" photo="4711"][/wppa]. Returns the url to the highest resolution file to the photo with id=4711.
Example use in template ( php ): echo '<img src=".do_shortcode('[wppa type="url" photo="4711"][/wppa]')." />';
This is equivalent to echo '<img src=".wppa_get_hires_url(4711)." />';, but you can use 'photo="#potd"' in the shortcode version.
Example use in page content: <img src='[wppa type="url" photo="4711"][/wppa]' />. Note the single quotes in the src attribute of img.

= Other Changes =

* Various minor cosmetic changes.
* The wppa session system is now more stable; logging in will not open a new session anymore. The session id is now only dependant of ip address and user agent.

= 6.3.9 =

= Bug Fixes =

* IPTC data is now converted to utf8 to accomodate for iso characters in iptc data.
* Album names with quotes broke slideshow. Fixed: Search root ( album name ) is now properly escaped.
* Fixed an incompatibility issue of the wppa search widget when used in a Beaver Page Builder page module rather than in a sidebar.
* Fixed a page link and link back problem from slideshow to thumbnails during search when pretty links were enabled.
* Album view count stopped to be bumped. Fixed.

= Other Changes =

* Changed the default value for Table VII-D1.1: Owners only to TRUE.
* It is no longer possible to mis-configure Table VII so that logged out users can edit name and description of albums at the front-end.

= 6.3.8 =

= Bug Fixes =

* The tagcloud widget now uses the font size settings from Table I-F13 and 14, like the shortcode variant already did.
* Phrases in wppa-theme.php will now properly be translated if a language file exists.
* All widgets now reset the 'in widget' switch correctly, preventing unwanted behaviour when widgets are displayed before shortcode initiated displays.
* The search widget/box now behaves as expected also in themes that display widgets before the page content.

= Other Changes =

* Many internal changes to improve stability.
* You can now add (html) text at the top of the search widget / box. Table IX-E15.
* Table IX-E16,17: You can now edit the label texts for root and sub search.
* Widget init no longer uses anonymous functions.
* Cosmetic changes to search widget/box.

= 6.3.7 =

= Bug Fixes =

* Stereo images were not correctly displayed when Fotomoto or Cloudinary was active. Fixed.
* Selecting any stereomode on photo admin page now always recreates thumbnail.
* If download album fails you will see a proper errormessage most of the time.
* The display of tags is now trimmed from comma's when converted from w#tags.

= Other Changes =

* Email notifications are now sent in plain text if the server can not handle emails containing html code.
* All ajax timeouts are now set to 60 seconds.
* Changed the minimum thumbnails for Imagefactory covers from 2 to 1.
* Cosmetic changes to the logfile ( Table VIII-C1 ).

= 6.3.6 =

= Bug Fixes =

* Scripts in the wppa text widget stopped working. Fixed.

= New Features =

* Support for 3d stero images. Enable in Table IV-A24.
* Table IX-A9: To switch off the loading of any language file for wppa+.

= Other Changes =

* Removed swedish and dutch language files, they are now maintained by the wp polyglot team.

= 6.3.5 =

= Bug Fixes =

* In album admin: the state of the wp editor ( Visible or Text ) will no longer change during an update.
* In Import, when importing dirs to albums, when sub dirs of different top dirs had the same name, photos were placed all in one of the sub albums. Fixed.
* Table VIII-B15: Sync Cloudinary Now works as expected. Table IX-K4.4: Update uploads has been removed, run Table VIII-B15 instead.

= Other Changes =

* Album Admin pages show a spinner during load.
* The frontend Ajax spinner is now always at the center of the screen, no longer cenetered on the wppa container.
* Table VII-D5: Comment captcha is now a selection box: none, logged out, all users.

= 6.3.4 =

= Bug Fixes =

* Smiley picker will no longer break ssl.

= Other Changes =

* You now switch off the recently implemented feature to run wppa shortcodes on wppa filter priority in Table IX-A1.3
* Use WP editor ( Table IX-B3 ) Now uses tinyMce. Tanx to xdanaskos.

= 6.3.3 =

= Bug Fixes =

* Fixed names of french language files
* Swipe did not work properly. Fixed.

= 6.3.2 =

= New Features =

* Central slideshow start/stop icons. See Table II-B13.2.

= Other Changes =

* To prevent damage to the html created by wppa, a new way to process shortcodes is implemented.
The expanded shortcode, produced at priority level 11, See Table IX-A1.2, is first saved in memory,
and later inserted into the page/post at a higher priority level, See Table IX-A1.1.
Not for widgets yet.
On templates, no longer use
<strong>do_shortcode( '[wppa ...][/wppa]' );</strong> but use
<strong>apply_filters( 'the_content, '[wppa ...][/wppa]' );</strong>

= 6.3.1 =

= Bug Fixes =

* When history.pushState fails, history.replaceState is attempted. This ensures updating the browser address line properly when Table IV-A7 is ticked.
* Files with uppercase extensions can be imported.

= New Features =

* Status filter on potd admin page.

= Other Changes =

* Changed the language system to comply with the wp standards for WordPress.org to manage translations for this plugin.
These changes imply that the separate plugin wppa-admin-language-pack no longer works;
but the translations will become automaticly updated ( on the wp update page ) in the near future.
* Improved compatibility with qTranslate-x.

= 6.2.12 =

= Bug Fixes =

* # was removed from tags, but also when it was needed for untranslated exif and iptc tags. Fixed.

= New Features =

* Table IV-F1.1: Comments view login. If set, existing comments are only visible for logged in visitors.

= 6.2.11 =

= Bug Fixes =

* If no tags or cats were used, the 'need conversion' message kept re-appearing. Fixed.
* Fixed a potential server error message in import files that occurred when the default upload album was deleted.
* Fixed an error in SuperSearch box when there are no phototags in the system.

= 6.2.10 =

= Bug Fixes =

* Added missing images for fullsize lightbox display buttons.
* The author of last comment given is now shown in the subtext on the comment widget and the last comment in its tooltip.
* Superview photos now shows photos only.

= New Features =

* Checkbox in Table VII-B11: Home after upload. After successfull upload, go to the home page.
* Table II-D4.1 Display Comment count under default Thumbnail.
* After ajax replaces wppa container content, the page will scroll to the right position.
* Table VIII-B10.1. Delete all auto pages.
* Table IX-B16: Confirm create ( album ). Asks if you really want to create an album on album admin page. Default on.

= Other Changes =

* The way photo tags and album cats are stored has changed, and the characters #, @ and & are added to the list of illegal characters in tags and cats.
If you use tags, you will be requested to run the update procedure Fix tags in Table VIII-B16, and Fix cats in Table VIII-B17.
* Improved way to find the origin sitename in notifiction emails.
* All js files are now in subdirectory js/
* Lifted the limitation that having more than approx 20.000 photos in an album or in the results of a search action on systems with 128 Mb caused an Memory exhausted server error.
You can now have 40.000 photos in an album or as a search result ( simple search, supersearch or tag(s) ) on systems with only 32 Mb available server memory without getting the error.
However, it is still recommended to have at least 64Mb memory available.

= 6.2.9 =

= Bug Fixes =

* In certain virtual albums, when the number of photos was less than the photocount treshold value, they did not show up where they should. Fixed.
* Local avatars now also work when the users email address is not required in coments.
* Fixed a capacity problem in search on tags ( multitag and tagcloud widgets ).
* If there is an album selection box in the frontend upload dialog and the submit button is pressed, it is now checked if an album has been selected before the upload is started.

= Other Changes =

* To create albums at the frontend, the pre-existance of any album that the user can upload to, is no longer required.
* The submit button and the ajax progression bar in the frontend upload dialog are moved to the bottom of the box.

= 6.2.8 =

= Bug Fixes =

* Uploader cache was not updated during front-end uploads, causing errors in user photo counts in the uploader widget. Fixed.

= New Features =

* The lightbox overlay now shows a icon link at the upper right corner to switch between fullscreen and normal mode.
You can switch it off in Table II-G20.

= Other Changes =

* Added image urls in Album Admin -> Edit -> Manage Photos.

= 6.2.7 =

= Bug Fixes =

* The fix in 6.2.6 for thumbnail style 'masonry rows' damaged 'masonry columns' style. Fixed.

= 6.2.6 =

= Bug Fixes =

* Local avatars were not found when the login name was different from the users display name. Fixed.
* Latin/ISO characters are now properly recognized in import .csv files.
* Thumbnail style 'masonry rows' now properly works for both responsive and static themes, even in IE and Chrome.
* Mouseover effect did not work correctly on masonry style thumbnails. Fixed.

= Other Changes =

* Roles that have wppa_moderate capability can edit/delete photos at the front-end, like administrators can.

= 6.2.5 =

= Bug Fixes =

* Files of type .pmf with unsanitized filenames were not recognized in Import Photos. Fixed.
* Fixed several potential undefined value warnings.

= New Features =

* Bulk edit can now also change photo owner if you are administrator and Table VII-D10 is ticked.
* Bulk edit has a quick delete link at each photo.

= Other Changes =

* Added calendar based shortcodes to the shorcode generator.

= 6.2.4 =

= New Features =

* Re-upload existing photo on the Photo admin screen and the front-end edit photo screen.
For administrators or for anybody that sees the edit photo screen when Table VII-C8 (Update photofiles restricted) is unchecked.
* Edit photo description can be disabled for non-administrators in Table VII-C7.
* Shortcode attribute reverse="1" for type="calendar", to get the youngest first.
* You can select black or lightgray for the Ugly Browse Buttons in Table II-B13.1

= 6.2.3 =

= Bug Fixes =

* Ajax links stopped working in Chrome and IE. Fixed.

= Other Changes =

* The album='..' attribute now works in the calendar shortcodes.
* Performance improvement in calendar shortcodes with attribute all="1".

= 6.2.2 =

= Bug Fixes =

= New Features =

* New shortcode type calendar. Calendar types: exifdtm, timestamp, modified. Optional argument: all="1" to initially display all.
examples: [wppa type="calendar" calendar="exifdtm"][/wppa], [wppa type="calendar" calendar="modified" all="1"][/wppa].
This feature requires Ajax active ( Table IV-A1.0 ).

= Other Changes =

* Exif date is now editable for administrators.
* Social Media Widget return link can specify the occurrance in Table VI-C10.
* improved performance on synchronisation with Cloudinary.

= 6.2.1 =

= Bug Fixes =

* Fixed a spurious problem in import from remote

= New Features =

* You can select either Home or Landing for the return link from social media shares that are invoked from widgets. Table VI-C10.

= Other Changes =

* Mods to Cloudinary support. Table IX-K4.4 no longer uploads over-aged photos.
* After rotating a photo, the stored dimensions are now properly reset.

= 6.2.0 =

= New Features =

* You can configure a limited use of Cloudinary CDN. In Table IX-K4.7 you can specify a max lifetime. Older photos will NOT be loaded from Cloudinary.
To remove them from Cloudinary, run Table VIII-B15 on a regular basis.

= Other Changes =

* This is a maintenance release for compatibility with PHP5 object constructors.

= 6.1.16 =

= Bug Fixes =

* If Show empty thumbnail area was on ( Table II-D18 ) and the album was an enumeration, the thumbnail page crashed. Fixed.

= New Features =

* Table IV-D2: Default cover photo selection method ( for new alums ), and added option '--- Random from (grand)children ---' on the album admin page.

= Other Changes =

* You can select a text for the close link in frontend upload/create/edit dialogs in Table II-J1.
* The switch Table IV-F10 now works for all non-admin receivers of comment notifications emails.
* If you set Table I-A9 to 0, the pagelink bar shows n/m in the center.

= 6.1.15 =

= Bug Fixes =

* Fixed a bug in a security check on front-end album deletion.
* Fixed high resolution image urls for videos / audios on slideshows.

= New Features =

* WPPA+ Text widget has now an extra checkbox, to set if you want the widget to be seen by logged in users only.
* Table II-D18: Show empty thumbnail area. Check this to see the thumbnail area of empty albums for the upload link in it.
* There are now close links in the front-end upload and album edit / create dialogs.

= Other Changes =

* Improved compatibility with lightbox plugin prettyPhoto. Slideshows work on it ( but still no videos or audios ).
* There are no loger empty titles or titles with only a space on images.
* If the WPPA+ embedded lightbox is used, the subtitles are now transferred to lightbox by data-lbtitle="..",
to prevent hughe tooltip boxes while hovering over the image that links to lightbox while Table II-G1 is unticked.
If you use a non-default lightbox, make sure the liughtbox titles are empty, or Table II-G91 is ticked.

= 6.1.14 =

= Bug Fixes =

* On servers where the function readdir() not properly workes, the import page never showed up. Fixed.
* Many minor fixes for w3c validation.

= Other Changes =

* For browsers that display empty tooltip boxes: there are no longer empty title attributes generated.
* wppa lightbox now uses data-rel="wppa" to meet w3c standards.
* Table II-G19: Overlay show legenda. Regardless of this setting, it will not show up on mobile devices,
but the keyboard handler will be installed to facillitate tablet/laptop converable devices.
* There was a serious performance problem with the new smilies: emoji.
Especially on firefox and using ajax On one of my testsites a slideshow with 15 slides and comments enabled and the smiley picker displayed
used to take 4 seconds to load. Now it takes up to a minute; the browser even does not respond for over 50 seconds.
As a work around for this, i coded my own convert_smilies() function: wppa_convert_smilies(), located in wppa_utils.php,
just for the creation of the html for the smileypicker and the smilies in the comments on photos.
It still uses the emoji images, but by direct coding and not through a character code.

= 6.1.13 =

* Intermediate test version, not released.

= 6.1.12 =

= Bug Fixes =

* Rotating an image will always produce a rotated thumbnail created out of the display file, regardless of setting Table IX-F12.
If you have rotated images and you want to remake all thumbnails and you have source files saved, tick Table IX-F12 to make sure all thumbnails will have the right orientation.
* Thumbnail type *masonry style rows* is now usable on static themes. There still is a problem with Thumbnail type ( Table IV-C3 ) *masonry style rows.*
On static themes: untick Table IV-C6: *Thumb mouseover* to fix the behaviour in Internet Explorer.
On responsive themes, in Internet Explorer and Google Chrome show odd layouts. Do not use *masonry style rows* on responive themes until this issue is fixed.
*masonry style columns* works as expected in all browsers, both in responsive and static themes.
* Layout fix on album cover if album full.

= New Features =

* Topten Widget can have owner and album displayed in the subtitle, album will be a link to the photos album.
* You can now also use keywords for exif and iptc labels in photo descriptions. Use *2#L080* for *Photographer:*, *E#L9003* For *Date time original* etc, where *2#080* and *E#9003* return the photo specific data..
* New settings for lightbox: Table II-G18 and 19 to hide Start/Stop and Fullscreen legenda.

= 6.1.11 =

= Bug Fixes =

* Fixed an error in statistics for logged out visitors.

= New Features =

* Bulk import custom data. see http://wppa.opajaap.nl/using-custom-data-fields/

= 6.1.10 =

= Bug Fixes =

* Supersearch now also works in I.E.
* Fixed breadcrumb for supersearch displays.

= New Features =

* New photo status: private. Private photos are only shown to logged in visitors. This will only work in normal pageviews. A full url to an image file will not be rejected.

= Other Changes =

* Performance improvements in supersearch.
* Added Table IX-13 and E-14 to reduce select box options.
* Exif and Iptc systems now clean up garbage automaticly.
* Removed 'hover to select' and 'click to add/remove' from supersearch selectboxes because i.e. does not support event handlers on option tags.

= 6.1.9 =

= Bug Fixes =

* Fixed a layout issue of Com alt displays on responsive themes.

= New Features =

* The default album cover linktype that will be set at album creation is now settable in Table IX-D18.
* New shortcode: type="supersearch". Related settings: Table VI-C9, Table IX-E13.

= Other Changes =

* Split js files in logical units, prevent loading of not used code.

= 6.1.8 =

= Bug Fixes =

* Custom data is now properly indexed ( 6.1.7.001 )
* Skip empty albums now correctly tests on user role administrator as opposed to capability wppa_admin.
* Thumbnail popup did not work properly on chrome browser on certain themes. Fixed.
* Fixed potential problems with setting options that have leading or trailing spaces.

= 6.1.7 =

= New Features =

* Custom datafields are now imputtable at the front-end upload dialog box. Table II-H10. Tags switch is now Table II-H11.

= Other Changes =

* Cosmetic changes in page link bar.
* Set input field width in seach box to 60%, added class wppa-search-input to input field. This fixes a lay-out issue on theme twentyfifteen.

= 6.1.6 =

= Bug Fixes =

* If no album selected in frontend upload widget/box an alertbox will be displayed.
* Fotomoto 'hide when running' now works.

= New Features =

* Up to 10 custom datafields for photos can be defined. See Table II-J10(.x).
See http://wppa.opajaap.nl/using-custom-data-fields/ for an explanation.

= Other Changes =

* wppa.js is now split into 4 files.
* All front-end ajax actions are now asynchronous using jQuery.ajax().

= 6.1.5 =

= Bug Fixes =

* Selected albums did not show up in album selection lists. Fixed.
* Alt thumbsizes stopped working. Fixed.

= 6.1.4 =

= Bug Fixes =

* Fixed a regression vs 6.1.2: The upload link on an album should only show up if the user hass album access to the album.

= Other Changes =

* Setting Table VII-D1 ( Owner only ) has been split into VII-D1.1 referring to album admin access and VII-D1.2 refering to album upload access.

= 6.1.3 =

= Bug Fixes =

* The message: 'Comment added' stopped to be displayed even if Table IV-F6 was ticked. Fixed.
* Smilies in photo descriptions are now displayed.
* Improved randomness of random selected photos in multiple albums.
* Fixed consistency in random sequence between first and successive pages in paginated displays.
* Improved algorithm to decide when to display the front-end upload and creat album link.
* Fixed an inconsistency in rights on the album admin table.
* Security fix.

= New Features =

* The ability to limit the number of albums for a user based on user role. Table VII-B5a.x

= 6.1.2 =

= Bug Fixes =

* The new wp (4.2) implementation of smileys broke the smileypicker. Fixed.
* The BestOf widget could not handle video's. Fixed.

= 6.1.1 =

= Bug Fixes =

* Poster image files could no be import-updated. Fixed.
* The photo of the day widget could not handle videos. Fixed.

= New Features =

* Audio support. Supported filetypes: .mp3, .wav and .ogg.
* Added filetype .jpeg for photos.

= Other Changes =

* Table II-D ( Visibility: Thumbnails ) has been renumbered.
* When a video plays, a running slideshow will be suspended until the video finishes. Same for videos on running lightbox slideshows.
* Fixed a lay-out issue on horizontal masonry thumbnails.
* The thumbnail subtext is now displayed as title ( hover text ) on masonry style thumbnails.

= 6.0.0 =

= New Features =

* The support of videos. You can mix photos and videos throughout the system including lightbox.
See the <a href="http://wppa.opajaap.nl/video-support/">documentation.</a>

= Other Changes =

* Added link types to various virtual album widgets.

== About and Credits ==

* WP Photo Album Plus is extended with many new features and is maintained by J.N. Breetvelt, ( http://www.opajaap.nl/ ) a.k.a. OpaJaap
* Thanx to R.J. Kaplan for WP Photo Album 1.5.1, the basis of this plugin.

== Licence ==

WP Photo Album is released under the GNU GPL licence. ( http://www.gnu.org/copyleft/gpl.html )