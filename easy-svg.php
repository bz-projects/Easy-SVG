<?php
/*
Plugin Name:  Easy SVG Support
Plugin URI:   https://wordpress.org/plugins/easy-svg/
Description:  Add SVG support for WordPress.
Version:      4.2.1
Author:       Benjamin Zekavica
Author URI:   https://www.benjamin-zekavica.de
Requires PHP: 8.0
Requires at least: 6.0
Text Domain:  easy-svg
Domain Path:  /languages
License:      GPL3

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

© 2017 - 2026 by Benjamin Zekavica. All rights reserved.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Helper: Load Composer dependencies.
$composer_package = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $composer_package ) ) {
    require $composer_package;
}

// SVG Sanitizer: Using enshrined\svgSanitize\Sanitizer.
use enshrined\svgSanitize\Sanitizer;
$sanitizer = new Sanitizer();

/**
 * SVG Sanitizer Allowed Tags Class.
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
 * SVG Sanitizer Allowed Attributes Class.
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
 * Check and sanitize SVG file content.
 *
 * @param string $file Path to the file.
 * @return bool Returns true if file was sanitized successfully.
 */
function esw_svg_file_checker( $file ) {
    global $sanitizer;

    $sanitizer->setAllowedTags( new esw_svg_tags() );
    $sanitizer->setAllowedAttrs( new esw_svg_attributes() );

    $unclean = file_get_contents( $file );

    if ( false === $unclean ) {
        return false;
    }

    $clean = $sanitizer->sanitize( $unclean );

    if ( false === $clean ) {
        return false;
    }

    // Save cleaned file.
    file_put_contents( $file, $clean );

    return true;
}

/**
 * Filter and sanitize uploaded SVG files using trusted file detection.
 *
 * This function does NOT rely on the user-controlled MIME header.
 * It uses wp_check_filetype_and_ext() and the file extension to reliably
 * detect SVG uploads and sanitize them. Inconsistent SVG uploads are rejected.
 *
 * @param array $file Array containing file details before upload.
 * @return array Modified file array or error message if invalid.
 */
function esw_svg_upload_filter_check_init( $file ) {

    // Bail if required keys are missing.
    if ( empty( $file['tmp_name'] ) || empty( $file['name'] ) ) {
        return $file;
    }

    // Server-side detection of extension and mime type.
    $checked = wp_check_filetype_and_ext(
        $file['tmp_name'],
        $file['name'],
        get_allowed_mime_types()
    );

    $ext  = isset( $checked['ext'] )  ? $checked['ext']  : '';
    $type = isset( $checked['type'] ) ? $checked['type'] : '';

    // Fallback: check extension using pathinfo.
    $pathinfo_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

    // Case 1: Genuine SVG (extension and mime type both match).
    if ( 'svg' === $ext && 'image/svg+xml' === $type ) {

        // Normalize mime type.
        $file['type'] = 'image/svg+xml';

        // Sanitize SVG content before it is stored.
        if ( ! esw_svg_file_checker( $file['tmp_name'] ) ) {
            $file['error'] = __( 'Sorry, please check your SVG file.', 'easy-svg' );
        }

        return $file;
    }

    // Case 2: File has .svg extension but mime type is not a valid SVG mime type -> reject.
    if ( 'svg' === $pathinfo_ext && 'image/svg+xml' !== $type ) {
        $file['error'] = __( 'Sorry, this SVG file is not allowed for security reasons.', 'easy-svg' );
        return $file;
    }

    // All other files pass through unchanged.
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'esw_svg_upload_filter_check_init' );

/**
 * Add support for SVG file uploads by modifying MIME types.
 *
 * @param array $mimes File type associations.
 * @return array Modified MIME types with SVG support.
 */
if ( ! function_exists( 'esw_add_support' ) ) {
    function esw_add_support( $mimes ) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
    add_filter( 'upload_mimes', 'esw_add_support' );
}

/**
 * Validate uploaded image files and ensure proper file extension and MIME type.
 *
 * @param array  $checked  File check results.
 * @param string $file     Path to the uploaded file.
 * @param string $filename The file name.
 * @param array  $mimes    Allowed MIME types.
 * @return array Checked results including extension, type, and filename.
 */
if ( ! function_exists( 'esw_upload_check' ) ) {

    function esw_upload_check( $checked, $file, $filename, $mimes ) {

        if ( empty( $checked['type'] ) ) {
            $esw_upload_check = wp_check_filetype( $filename, $mimes );
            $ext              = $esw_upload_check['ext'];
            $type             = $esw_upload_check['type'];
            $proper_filename  = $filename;

            // Only allow valid image types and avoid mismatched image extensions.
            if ( $type && 0 === strpos( $type, 'image/' ) && 'svg' !== $ext ) {
                $ext  = false;
                $type = false;
            }

            $checked = compact( 'ext', 'type', 'proper_filename' );
        }

        return $checked;
    }
    add_filter( 'wp_check_filetype_and_ext', 'esw_upload_check', 10, 4 );
}

/**
 * Get SVG file URL in the backend via AJAX.
 *
 * Expected request parameters:
 * - nonce        (generated via wp_create_nonce( 'esw_svg_nonce' ))
 * - attachmentID (integer ID of the attachment)
 *
 * The hook name remains for backward compatibility with existing JS.
 */
if ( ! function_exists( 'esw_display_svg_files_backend' ) ) {

    function esw_display_svg_files_backend() {

        // Capability check: only allow users who can upload files.
        if ( ! current_user_can( 'upload_files' ) ) {
            wp_send_json_error(
                array(
                    'message' => __( 'You are not allowed to access this resource.', 'easy-svg' ),
                ),
                403
            );
        }

        // Nonce verification: expects "nonce" field in the request.
        check_ajax_referer( 'esw_svg_nonce', 'nonce' );

        // Use POST for AJAX requests.
        $attachment_id = isset( $_POST['attachmentID'] ) ? absint( $_POST['attachmentID'] ) : 0;

        if ( ! $attachment_id ) {
            wp_send_json_error(
                array(
                    'message' => __( 'Invalid attachment ID.', 'easy-svg' ),
                )
            );
        }

        $url = wp_get_attachment_url( $attachment_id );

        if ( ! $url ) {
            wp_send_json_error(
                array(
                    'message' => __( 'Attachment not found.', 'easy-svg' ),
                )
            );
        }

        wp_send_json_success(
            array(
                'url' => esc_url_raw( $url ),
            )
        );
    }

    // Note: Non-standard hook name kept for backwards compatibility.
    add_action( 'wp_AJAX_svg_get_attachment_url', 'esw_display_svg_files_backend' );
}

/**
 * Display SVG files properly in the media library.
 *
 * @param array  $response   File response array.
 * @param object $attachment Attachment object.
 * @param array  $meta       File metadata.
 * @return array Modified response with SVG dimensions.
 */
if ( ! function_exists( 'esw_display_svg_media' ) ) {

    function esw_display_svg_media( $response, $attachment, $meta ) {

        if (
            isset( $response['type'], $response['subtype'] ) &&
            'image' === $response['type'] &&
            'svg+xml' === $response['subtype'] &&
            class_exists( 'SimpleXMLElement' )
        ) {
            try {
                $path = get_attached_file( $attachment->ID );
                if ( file_exists( $path ) ) {
                    $svg    = new SimpleXMLElement( file_get_contents( $path ) );
                    $src    = $response['url'];
                    $width  = (int) $svg['width'];
                    $height = (int) $svg['height'];

                    $response['image'] = compact( 'src', 'width', 'height' );
                    $response['thumb'] = compact( 'src', 'width', 'height' );

                    $response['sizes']['full'] = array(
                        'height'      => $height,
                        'width'       => $width,
                        'url'         => $src,
                        'orientation' => ( $height > $width ) ? 'portrait' : 'landscape',
                    );
                }
            } catch ( Exception $e ) {
                // Fail silently, keep default response if SVG parsing fails.
            }
        }

        return $response;
    }
    add_filter( 'wp_prepare_attachment_for_js', 'esw_display_svg_media', 10, 3 );
}

/**
 * Add styles for SVG files in the media library and Gutenberg editor.
 */
if ( ! function_exists( 'esw_svg_styles' ) ) {
    function esw_svg_styles() {
        echo "<style>
                /* Media Library SVG styles */
                table.media .column-title .media-icon img[src*='.svg'] {
                    width: 100%;
                    height: auto;
                }

                /* Gutenberg editor SVG styles */
                .components-responsive-wrapper__content[src*='.svg'] {
                    position: relative;
                }
            </style>";
    }
    add_action( 'admin_head', 'esw_svg_styles' );
}