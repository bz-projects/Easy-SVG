<?php
/*
Plugin Name: Easy SVG
Plugin URI: https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress
Version:     1.0
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

 if($type && 0 === strpos($type, 'image/') && $ext !== 'svg'){
 	$ext = $type = false;
 }
 	$checked = compact('ext','type','proper_filename');
 }
 return $checked;
}
add_filter('wp_check_filetype_and_ext', 'esw_filetype', 10, 4);
