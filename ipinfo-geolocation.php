<?php
/*
Plugin Name: IPinfo.io Geolocation
Description: Obtains geolocation info for an anonymised version of the end user's IP address from <a href="https://ipinfo.io/">IPinfo.io</a>; stores in a cookie and adds the information as a javascript object on the page.
Author: Zac Ariel
Version: 0.1
Compatibility: WordPress 5.7
*/

// Load Admin module
define( 'GEOLOCATION__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( GEOLOCATION__PLUGIN_DIR . 'ipinfo-geolocation-admin.php' );

// Load Settings
$geolocation_options = get_option( 'ipinfo_geolocation_settings' );

// Load Geolocation (if enabled)
if (isset( $geolocation_options['ipinfo_geolocation_enable'] ) && $geolocation_options['ipinfo_geolocation_enable'] == '1' ) {
  require_once( GEOLOCATION__PLUGIN_DIR . 'ipinfo-geolocation-request.php' );
}
