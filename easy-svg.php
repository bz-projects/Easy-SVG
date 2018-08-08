<?php
/*
Plugin Name: Easy SVG Support
Plugin URI: https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress.
Version:     2.3
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
(c) 2017 - 2018 by Benjamin Zekavica. All rights reserved.

Imprint:
Benjamin Zekavica
Oranienstrasse 12
52066 Aachen

E-Mail: info@benjamin-zekavica.de
Web: www.benjamin-zekavica.de

I don't give support by Mail. Please write in the
community forum for questions and problems.


*/

/* ========================================
   Upload SVG Support
   ======================================== */

add_filter( 'upload_mimes', 'esw_add_support' );


function esw_add_support ( $svg_editing ){

  $svg_editing['svg'] = 'image/svg+xml';

  // Echo the svg file
  return $svg_editing;
}


/* ========================================
   Uploading SVG Files into the Media Libary
  ========================================  */

function esw_upload_check($checked, $file, $filename, $mimes){

 if(!$checked['type']){

  	 $esw_upload_check = wp_check_filetype( $filename, $mimes );
  	 $ext = $esw_upload_check['ext'];
  	 $type = $esw_upload_check['type'];
  	 $proper_filename = $filename;

    /* ========================================
        Upload checking filename
       ======================================== */

     if($type && 0 === strpos($type, 'image/') && $ext !== 'svg'){
      	$ext = $type = false;
     }

     // Check the filename
     $checked = compact('ext','type','proper_filename');

 }

 return $checked;

}

add_filter('wp_check_filetype_and_ext', 'esw_upload_check', 10, 4);



/* ========================================
   Register activation hook.
   ======================================== */

register_activation_hook( __FILE__, 'esw_admin_notice_example_activation_hook' );


/* ========================================
   Runs only when the plugin is activated.
   ======================================== */

function esw_admin_notice_example_activation_hook() {

    /* ========================================
        Create transient data
       ======================================== */

    set_transient( 'esw-admin-notice-example', true, 5 );
}


/* Add admin notice */

add_action( 'admin_notices', 'esw_admin_notice_example_notice' );


/* ========================================
   Admin Notice on Activation.
   =======================================  */

function esw_admin_notice_example_notice(){

    /* ==============================================
       Check transient, if available display notice
       ============================================== */

    if( get_transient( 'esw-admin-notice-example' ) ){ ?>

        <div class="updated notice is-dismissible">
            <p>
    				  <strong>
                        <?php _e('Welcome to Version 2.3! New Update is now available. More features and full Gutenberg Support! Enjoy. Read the <a target="_blank" href="https://wordpress.org/plugins/easy-svg/#developers">Changelog</a>' , 'easy-svg' ) ; ?> <br /><br />
    				  	<?php _e('Thank you for using this plugin!' , 'easy-svg' ) ; ?>
    				  </strong><br />
    				    <?php _e( 'Go now to the Media Libary and upload your SVG Files.' , 'easy-svg' ); ?>
    				     <br /><br />
    				     <?php _e( 'Kind Regards', 'easy-svg' ); ?>
    				     <br />
    				    <strong>
    				     Benjamin Zekavica
    				    </strong>
		    	   </p>
        </div>

        <?php

        /* ================================================
           Delete transient, only display this notice once.
           ================================================ */

        delete_transient( 'esw-admin-notice-example' );

    }
}


  // Add Hook for Actived Plugin

    add_action('esw_welcome_message_active_plugin', 'esw_admin_notice_example_notice');

/* ========================================
   Load Text Domain for languages files
   =======================================  */


   if ( ! function_exists( 'esw_multiligual_textdomain' ) ) :

      function esw_multiligual_textdomain() {

        load_plugin_textdomain( 'easy-svg' , false, dirname( plugin_basename( __FILE__ ) ).'/languages' );

      }

      // Add action for the translation file

      add_action( 'plugins_loaded', 'esw_multiligual_textdomain' );

      // Finishing the if loop

    endif;



  /* ========================================
      Add WordPress Widget
     =======================================  */


    add_action('wp_dashboard_setup', 'esw_dashboard_widget');

    function esw_dashboard_widget() {
      global $wp_meta_boxes;
      wp_add_dashboard_widget('custom_help_widget', 'Easy SVG Support', 'esw_support_dashbord_widget_text');
    }


    function esw_support_dashbord_widget_text() {
      _e('Welcome to Version 2.3! New Update is now available. More features and full Gutenberg Support! Enjoy. Read the <a target="_blank" href="https://wordpress.org/plugins/easy-svg/#developers">Changelog</a>' , 'easy-svg' ) ;

      _e( '<h2>Welcome to EASY SVG!</h2><br /> Thank you for your installtion of my custom plugin! Do you want to Upload SVG Files? Than go to the Media Libary and Upload it! <br /><br /> Best Regards <br /> <strong>Benjamin Zekavica<strong>', 'easy-svg' );
      echo "<br /><br />";

      _e( 'Version Number', 'easy-svg' );
      echo "&nbsp;2.3";

    }

    // Add Hook for Messages

    add_action('esw_support_welcome_widget_text', 'esw_support_dashbord_widget_text');

/* ========================================
    Display SVG Files in Backend
   =======================================  */


   // Add JavaScipt to Backend


    function esw_add_javascript_for_backend() {
        wp_enqueue_style( 'esw_echo_svg_css', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.0' );
    }

    add_action( 'admin_enqueue_scripts', 'esw_add_javascript_for_backend' );


   // Echo

    add_action('wp_AJAX_svg_get_attachment_url', 'esw_display_svg_files_backend');

   // Define the function

   function esw_display_svg_files_backend(){

      $url = '';

      $attachmentID = isset($_REQUEST['attachmentID']) ? $_REQUEST['attachmentID'] : '';

      if($attachmentID){
          $url = wp_get_attachment_url($attachmentID);
      }

      echo $url;

      die();
  }


// Media Libary  Display SVG

function esw_display_svg_media($response, $attachment, $meta){
    if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')){
        try {
            $path = get_attached_file($attachment->ID);
            if(@file_exists($path)){
                $svg = new SimpleXMLElement(@file_get_contents($path));
                $src = $response['url'];
                $width = (int) $svg['width'];
                $height = (int) $svg['height'];

                // Media Gallery 
                $response['image'] = compact( 'src', 'width', 'height' );
                $response['thumb'] = compact( 'src', 'width', 'height' );

                // Single Details of Image 
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