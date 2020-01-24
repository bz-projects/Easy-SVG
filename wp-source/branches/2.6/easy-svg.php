<?php
/*
Plugin Name: Easy SVG Support
Plugin URI:  https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress.
Version:     2.6
Author:      Benjamin Zekavica
Author URI:  http://www.benjamin-zekavica.de
Text Domain: easy-svg
Domain Path: /languages
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


Copyright by:
(c) 2017 - 2019 by Benjamin Zekavica. All rights reserved.

Imprint:
Benjamin Zekavica
Oranienstrasse 12
52066 Aachen

E-Mail: info@benjamin-zekavica.de
Web: www.benjamin-zekavica.de

I don't give support by Mail. Please write in the
community forum for questions and problems.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/* =====================================
   Upload SVG Support
======================================== */

function esw_add_support ( $svg_editing ){

  $svg_editing['svg'] = 'image/svg+xml';

  // Echo the svg file
  return $svg_editing;
}
add_filter( 'upload_mimes', 'esw_add_support' );


/* ============================================
   Uploading SVG Files into the Media Libary
===============================================*/

function esw_upload_check($checked, $file, $filename, $mimes){

 if(!$checked['type']){

     $esw_upload_check = wp_check_filetype( $filename, $mimes );
     $ext              = $esw_upload_check['ext'];
     $type             = $esw_upload_check['type'];
     $proper_filename  = $filename;

     if($type && 0 === strpos($type, 'image/') && $ext !== 'svg'){
        $ext = $type = false;
     }

     // Check the filename
     $checked = compact('ext','type','proper_filename');
 }

 return $checked;

}
add_filter('wp_check_filetype_and_ext', 'esw_upload_check', 10, 4);


/*========================================
    Load Text Domain for languages files
=======================================  */

if(! function_exists( 'esw_multiligual_textdomain' ) ) {
    function esw_multiligual_textdomain() {
        load_plugin_textdomain( 'easy-svg' , false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
    }
    add_action( 'plugins_loaded', 'esw_multiligual_textdomain' );
}

/* ========================================
    Display SVG Files in Backend
=======================================  */

function esw_display_svg_files_backend(){

    $url = '';
    $attachmentID = isset($_REQUEST['attachmentID']) ? $_REQUEST['attachmentID'] : '';

    if($attachmentID){
        $url = wp_get_attachment_url($attachmentID);
    }
    echo $url;
    
    die();
}
add_action('wp_AJAX_svg_get_attachment_url', 'esw_display_svg_files_backend');

/* ========================================
     Media Libary  Display SVG
=======================================  */

function esw_display_svg_media($response, $attachment, $meta){
    if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')){
        try {
            
            $path = get_attached_file($attachment->ID);

            if(@file_exists($path)){
                $svg                = new SimpleXMLElement(@file_get_contents($path));
                $src                = $response['url'];
                $width              = (int) $svg['width'];
                $height             = (int) $svg['height'];
                $response['image']  = compact( 'src', 'width', 'height' );
                $response['thumb']  = compact( 'src', 'width', 'height' );

                $response['sizes']['full'] = array(
                    'height'        => $height,
                    'width'         => $width,
                    'url'           => $src,
                    'orientation'   => $height > $width ? 'portrait' : 'landscape',
                );
            }
        }
        catch(Exception $e){}
    }

    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'esw_display_svg_media', 10, 3);

/* ========================================
   Load CSS in Admin Header Styles
=======================================  */

function esw_svg_styles() {
  echo '<style>
            table.media .column-title .media-icon img[src*=".svg"]{
                width: 100%;
                height: auto;
            }
        </style>';
}
add_action('admin_head', 'esw_svg_styles');