<?php

if(!App::$install) {

	// Get the UID of the channel owner
	$uid = get_theme_uid();

	if($uid) {
		// Load the owners pconfig
		load_pconfig($uid, 'redbasic');

		$custom_bs = get_pconfig($uid, 'redbasic', 'bootstrap');
		$nav_bg = get_pconfig($uid, 'redbasic', 'nav_bg');
		$nav_bg_dark = get_pconfig($uid, 'redbasic', 'nav_bg_dark');
		$narrow_navbar = get_pconfig($uid,'redbasic','narrow_navbar');
		$link_color = get_pconfig($uid, 'redbasic', 'link_color');
		$link_color_dark = get_pconfig($uid, 'redbasic', 'link_color_dark');
		$link_hover_color = get_pconfig($uid, 'redbasic', 'link_hover_color');
		$link_hover_color_dark = get_pconfig($uid, 'redbasic', 'link_hover_color_dark');
		$bgcolor = get_pconfig($uid, 'redbasic', 'background_color');
		$bgcolor_dark = get_pconfig($uid, 'redbasic', 'background_color_dark');
		$schema = get_pconfig($uid,'redbasic','schema');
		$background_image = get_pconfig($uid, 'redbasic', 'background_image');
		$background_image_dark = get_pconfig($uid, 'redbasic', 'background_image_dark');
		$font_size = get_pconfig($uid, 'redbasic', 'font_size');
		$radius = get_pconfig($uid, 'redbasic', 'radius');
		$converse_width = get_pconfig($uid,'redbasic','converse_width');
		$top_photo = get_pconfig($uid,'redbasic','top_photo');
		$reply_photo = get_pconfig($uid,'redbasic','reply_photo');
	}
}

$site_bs_path = 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css';

if (file_exists('view/theme/redbasic/css/bootstrap-custom.css')) {
	// Compiled custom site bootstrap from sass
	// Run this script from the document root for an example:
	// php vendor/bin/pscss view/theme/redbasic/css/sample.scss view/theme/redbasic/css/bootstrap-custom.css

	$site_bs_path = 'view/theme/redbasic/css/bootstrap-custom.css';
}

$bs = $custom_bs ?: file_get_contents($site_bs_path);

if (file_exists('view/css/bootstrap-red.css')) {
	$bs = $bs . file_get_contents('view/css/bootstrap-red.css');
}

if(file_exists('view/theme/redbasic/schema/' . $schema . '.css')) {
	$schemecss = file_get_contents('view/theme/redbasic/schema/' . $schema . '.css');
}

// Now load the scheme.  If a value is changed above, we'll keep the settings
// If not, we'll keep those defined by the schema
// Setting $schema to '' wasn't working for some reason, so we'll check it's
// not --- like the mobile theme does instead.

// Allow layouts to over-ride the schema
if (isset($_REQUEST['schema']) && preg_match('/^[\w_-]+$/i', $_REQUEST['schema'])) {
  $schema = $_REQUEST['schema'];
}

if (($schema) && ($schema != '---')) {

	// Check it exists, because this setting gets distributed to clones
	if(file_exists('view/theme/redbasic/schema/' . $schema . '.php')) {
		$schemefile = 'view/theme/redbasic/schema/' . $schema . '.php';
		require_once ($schemefile);
	}

	if(file_exists('view/theme/redbasic/schema/' . $schema . '.css')) {
		$schemecss = file_get_contents('view/theme/redbasic/schema/' . $schema . '.css');
	}

}

// Allow admins to set a default schema for the hub.
// default.php and default.css MUST be symlinks to existing schema files in view/theme/redbasic/schema
if ((!$schema) || ($schema == '---')) {

	if(file_exists('view/theme/redbasic/schema/default.php')) {
		$schemefile = 'view/theme/redbasic/schema/default.php';
		require_once ($schemefile);
	}

	$schemecss = '';
	if(file_exists('view/theme/redbasic/schema/default.css')) {
		$schemecss = file_get_contents('view/theme/redbasic/schema/default.css');
	}

}

//Set some defaults - we have to do this after pulling owner settings, and we have to check for each setting
//individually.  If we don't, we'll have problems if a user has set one, but not all options.
$nav_bg = $nav_bg ?: 'rgba(248, 249, 250, 1)';
$nav_bg_dark = $nav_bg_dark ?: 'rgba(43, 48, 53, 1)';
$bgcolor = $bgcolor ?: '#fff';
$bgcolor_dark = $bgcolor_dark ?: '#212529';
$background_image = $background_image ?: '';
$background_image_dark = $background_image_dark ?: '';
$font_size = $font_size ?: '0.875rem';
$converse_width = $converse_width ?: '52'; //unit: rem
$top_photo = $top_photo ?: '2.9rem';
$reply_photo = $reply_photo ?: '2.9rem';

// Apply the settings
if(file_exists('view/theme/redbasic/css/style.css')) {

	$x = file_get_contents('view/theme/redbasic/css/style.css');

	if($narrow_navbar && file_exists('view/theme/redbasic/css/narrow_navbar.css')) {
		$x .= file_get_contents('view/theme/redbasic/css/narrow_navbar.css');
	}

	if($schemecss) {
		$x .= $schemecss;
	}

	$left_aside_width = 21; //unit: rem
	$right_aside_width = 21; //unit: rem

	$main_width = $left_aside_width + $right_aside_width + intval($converse_width);

	// prevent main_width smaller than 768px
	$main_width = (($main_width < 30) ? 30 : $main_width);

	$options = array (
		'$nav_bg' => $nav_bg,
		'$nav_bg_dark' => $nav_bg_dark,
		'$bgcolor' => $bgcolor,
		'$bgcolor_dark' => $bgcolor_dark,
		'$background_image' => $background_image,
		'$background_image_dark' => $background_image_dark,
		'$font_size' => $font_size,
		'$converse_width' => $converse_width,
		'$top_photo' => $top_photo,
		'$reply_photo' => $reply_photo,
		'$main_width' => $main_width,
		'$left_aside_width' => $left_aside_width,
		'$right_aside_width' => $right_aside_width
	);


	$o = strtr($x, $options);

	header('Cache-Control: max-age=2592000');

	echo $bs . $o;
}

// Set the schema to the default schema in derived themes. See the documentation for creating derived themes how to override this.

if(local_channel() && App::$channel && App::$channel['channel_theme'] != 'redbasic')
	set_pconfig(local_channel(), 'redbasic', 'schema', '---');
