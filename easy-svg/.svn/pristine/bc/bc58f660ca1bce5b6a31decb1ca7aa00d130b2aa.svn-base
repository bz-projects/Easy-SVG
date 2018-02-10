<?php
/*
Plugin Name: Easy SVG Support 
Plugin URI: https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress
Version:     1.1
Author:      Benjamin Zekavica
Author URI:  http://www.benjamin-zekavica.de
Text Domain: easy-svg
License:     GPL2

Easy SVG is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Easy SVG is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Easy SVG. If not, see license.txt .
*/

/* Upload SVG Support */

function esw_support ( $svg_editing ){
	$svg_editing['svg'] = 'image/svg+xml';
	return $svg_editing;
}
add_filter( 'upload_mimes', 'esw_support' );


/* Uploading SVG Files into the Media Libary */

function esw_filetype($checked, $file, $filename, $mimes){

 if(!$checked['type']){
	 $esw_filetype = wp_check_filetype( $filename, $mimes );
	 $ext = $esw_filetype['ext'];
	 $type = $esw_filetype['type'];
	 $proper_filename = $filename;

/* Upload checking filename */ 	 

 if($type && 0 === strpos($type, 'image/') && $ext !== 'svg'){
 	$ext = $type = false;
 }
 	$checked = compact('ext','type','proper_filename');
 }
 return $checked;
}
add_filter('wp_check_filetype_and_ext', 'esw_filetype', 10, 4);


/* Add Alert if user actived the plugin */ 


/* Register activation hook. */
register_activation_hook( __FILE__, 'esw_admin_notice_example_activation_hook' );
 
/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */
function esw_admin_notice_example_activation_hook() {
 
    /* Create transient data */
    set_transient( 'esw-admin-notice-example', true, 5 );
}
 
 
/* Add admin notice */
add_action( 'admin_notices', 'esw_admin_notice_example_notice' );
 
 
/**
 * Admin Notice on Activation.
 * @since 0.1.0
 */
function esw_admin_notice_example_notice(){
 
    /* Check transient, if available display notice */
    if( get_transient( 'esw-admin-notice-example' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <p>
			  <strong>
			    Thank you for using this plugin!
			  </strong>
			<br>Go now to the Media Libary and upload your SVG Files.
			<br><br>Kind Regards<br>
			    <strong>Benjamin Zekavica</strong></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'esw-admin-notice-example' );
    }
}

