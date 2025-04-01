<?php
/*
Plugin Name: Easy SVG Support
Plugin URI:  https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress.
Version:     3.9
Author:      Benjamin Zekavica
Requires PHP: 8.0
Requires at least: 6.0
Author URI:  https://www.benjamin-zekavica.de
Text Domain: easy-svg
Domain Path: /languages
License:     GPL3

Easy SVG is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Easy SVG is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Easy SVG. If not, see license.txt .

Copyright by:
© 2017 - 2025 by Benjamin Zekavica. All rights reserved.

Imprint:
Benjamin Zekavica

E-Mail: info@benjamin-zekavica.de
Web: www.benjamin-zekavica.de

I don't give support by Mail. Please write in the
community forum for questions and problems.
*/

if ( !defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly

// Helper: Load Composer dependencies
$composer_package =  __DIR__ .'/vendor/autoload.php'; 

// Load Composer
if( file_exists( $composer_package ) ) {
    require( $composer_package );
}

// SVG Sanitizer: Using enshrined\svgSanitize\Sanitizer
use enshrined\svgSanitize\Sanitizer;
$sanitizer = new Sanitizer();

/**
 * SVG Sanitizer Class
 * 
 * Custom class to filter allowed SVG tags using WordPress filters.
 */
class esw_svg_tags extends \enshrined\svgSanitize\data\AllowedTags {

    /**
     * Returns allowed SVG tags.
     * 
     * @return array
     */
    public static function getTags() {
        return apply_filters( 'esw_svg_allowed_tags', parent::getTags() );
    }
}

/**
 * SVG Attribute Sanitizer Class
 * 
 * Custom class to filter allowed SVG attributes using WordPress filters.
 */
class esw_svg_attributes extends \enshrined\svgSanitize\data\AllowedAttributes {

    /**
     * Returns allowed SVG attributes.
     * 
     * @return array
     */
    public static function getAttributes() {
        return apply_filters( 'esw_svg_allowed_attributes', parent::getAttributes() );
    }
}

/**
 * Function to check and sanitize SVG file content.
 * 
 * @param string $file Path to the file.
 * @return bool Returns true if file was sanitized successfully.
 */
function esw_svg_file_checker( $file ){

    global $sanitizer;

    $sanitizer->setAllowedTags( new esw_svg_tags() );
    $sanitizer->setAllowedAttrs( new esw_svg_attributes() );

    $unclean = file_get_contents( $file );

    if ( $unclean === false ) {
        return false;
    }

    $clean = $sanitizer->sanitize( $unclean );
    if ( $clean === false ) {
        return false;
    }

    // Save cleaned file
    file_put_contents( $file, $clean );

    return true;
}

/**
 * Filters and sanitizes uploaded SVG files.
 * 
 * @param array $upload Array containing file details.
 * @return array Modified upload array or error message if invalid.
 */
function esw_svg_upload_filter_check_init( $upload ){

    if ( $upload['type'] === 'image/svg+xml' ) {
        if ( ! esw_svg_file_checker( $upload['tmp_name'] ) ) {
            $upload['error'] = __( "Sorry, please check your file", 'easy-svg' );
        }
    }

    return $upload;
}
add_filter( 'wp_handle_upload_prefilter', 'esw_svg_upload_filter_check_init' );

/**
 * Add support for SVG file uploads by modifying MIME types.
 * 
 * @param array $svg_editing File type associations.
 * @return array Modified MIME types with SVG support.
 */
if( !function_exists('esw_add_support') ){
    function esw_add_support ( $svg_editing ){

        $svg_editing['svg'] = 'image/svg+xml';
        return $svg_editing;
    }
    add_filter( 'upload_mimes', 'esw_add_support' );
}

/**
 * Validate uploaded SVG files and ensure proper file extension and MIME type.
 * 
 * @param array $checked File check results.
 * @param string $file Path to the uploaded file.
 * @param string $filename The file name.
 * @param array $mimes Allowed MIME types.
 * @return array Checked results including extension, type, and filename.
 */
if( !function_exists('esw_upload_check') ){

    function esw_upload_check($checked, $file, $filename, $mimes){

        if(!$checked['type']){
            $esw_upload_check = wp_check_filetype( $filename, $mimes );
            $ext              = $esw_upload_check['ext'];
            $type             = $esw_upload_check['type'];
            $proper_filename  = $filename;

            if($type && 0 === strpos($type, 'image/') && $ext !== 'svg'){
               $ext = $type = false;
            }

            $checked = compact('ext','type','proper_filename');
        }

        return $checked;
    }
    add_filter('wp_check_filetype_and_ext', 'esw_upload_check', 10, 4);
}

/**
 * Load plugin text domain for localization.
 */
if( !function_exists( 'esw_multiligual_textdomain' ) ) {
    function esw_multiligual_textdomain() {
        load_plugin_textdomain( 'easy-svg' , false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
    }
    add_action( 'plugins_loaded', 'esw_multiligual_textdomain' );
}

/**
 * Get SVG file URL in the backend via AJAX.
 */
if( !function_exists( 'esw_display_svg_files_backend' ) ) {
    
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
}

/**
 * Display SVG files properly in the media library.
 * 
 * @param array $response File response array.
 * @param object $attachment Attachment object.
 * @param array $meta File metadata.
 * @return array Modified response with SVG dimensions.
 */
if( !function_exists( 'esw_display_svg_media' ) ) {
    
    function esw_display_svg_media($response, $attachment, $meta){
        if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')){
            try {
                $path = get_attached_file($attachment->ID);
                if(@file_exists($path)){
                    $svg = new SimpleXMLElement(@file_get_contents($path));
                    $src = $response['url'];
                    $width = (int) $svg['width'];
                    $height = (int) $svg['height'];
                    $response['image'] = compact( 'src', 'width', 'height' );
                    $response['thumb'] = compact( 'src', 'width', 'height' );

                    $response['sizes']['full'] = array(
                        'height' => $height,
                        'width'  => $width,
                        'url'    => $src,
                        'orientation' => $height > $width ? 'portrait' : 'landscape',
                    );
                }
            } catch(Exception $e) {}
        }

        return $response;
    }
    add_filter('wp_prepare_attachment_for_js', 'esw_display_svg_media', 10, 3);
}

/**
 * Add styles for SVG files in the media library and Gutenberg editor.
 */
if( !function_exists( 'esw_svg_styles' ) ) {
    function esw_svg_styles() {
        echo "<style>
                /* Media Library SVG styles */
                table.media .column-title .media-icon img[src*='.svg']{
                    width: 100%;
                    height: auto;
                }
    
                /* Gutenberg editor SVG styles */
                .components-responsive-wrapper__content[src*='.svg'] {
                    position: relative;
                }
            </style>";
    }
    add_action('admin_head', 'esw_svg_styles');
}