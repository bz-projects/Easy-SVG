<?php
/*
Plugin Name: Easy SVG Support 
Plugin URI: https://wordpress.org/plugins/easy-svg/
Description: Add SVG Support for WordPress
Version:     2.0
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
Oranienstraße 12
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
    				  	<?php _e( 'Thank you for using this plugin!' , 'easy-svg' ) ; ?> 
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
   
 
/* ======================================== 
   Load Text Domain for languages files 
   =======================================  */ 


   if ( ! function_exists( 'esw_multiligual_textdomain' ) ) :

      function esw_multiligual_textdomain() {

        load_plugin_textdomain( 'easy-svg' , false, dirname( plugin_basename( __FILE__ ) ).'/languages' );

      }
      add_action( 'plugins_loaded', 'esw_multiligual_textdomain' );
    endif;


/* ======================================== 
   SVG Add Javascript to Backend  
   =======================================  */ 

add_action('wp_AJAX_svg_get_attachment_url', 'get_attachment_url_media_library');


/* ====================================================
	Writing the function of the add_action hook 
   =================================================== */

function get_attachment_url_media_library(){

    $url = '';
    $attachmentID = isset($_REQUEST['attachmentID']) ? $_REQUEST['attachmentID'] : '';

        /* ====================================================
      		Ask the ID and get the ID 
       	   ==================================================== */

	    if($attachmentID){
	        $url = wp_get_attachment_url($attachmentID);
	    }

	   /* ====================================================
      		Echo the ID into the backend 
       	  ==================================================== */    

    	echo $url;

       /* ====================================================
      		Die the function
       	  ==================================================== */    

    die();
}


/* ====================================================
	Enque Javascript
   =================================================== */

add_action( 'admin_enqueue_scripts', 'esw_enque_scripts' );

function esw_enque_scripts($hook) {
    if( 'index.php' != $hook ) {
   	
   	/* ====================================================
		Only applies to dashboard panel
	  ==================================================== */ 
	  
	  return;

    }
        
	wp_enqueue_script( 'ajax-script', plugins_url( '/js/scripts.js', __FILE__ ), array('jquery') );

 	/* ====================================================
		in JavaScript, object properties are accessed as 
		ajax_object.ajax_url, ajax_object.we_value
		==================================================== */ 

		wp_localize_script( 'ajax-script', 'ajax_object',
		    array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 
		    	   'we_value' => 1234 
		    ) 
		);
}

  /* ====================================================
	  Add Ajax Action
	 ==================================================== */ 
	
   add_action( 'wp_ajax_my_action', 'esw_action' );

   
 /*  ====================================================
	  Define the function
	 ==================================================== */ 
   
 
	function esw_action() {
		global $wpdb;
		$whatever = intval( $_POST['whatever'] );
		$whatever += 10;
		    echo $whatever;
		wp_die();
	}


  
 /*  ====================================================
     Options Menu 
   ==================================================== */ 

  add_action("admin_menu", "esw_options_menu");


 /* ============================
      Create Options Page
    ============================ */ 

  function esw_options_menu() {

      $menu = add_menu_page( 
           'SVG Options',
           'SVG Options',
           'administrator',
           'easy-svg-support',
           'svg_settings', 
           'dashicons-svg', 77);


      add_action( 'admin_print_styles-' . $menu, 'cake_css' );

  }


add_action('admin_head', 'esw_custom_favicon');

function esw_custom_favicon() {
    echo '
        <style>
          .dashicons-svg{
              background-image: url("'.plugins_url().'/easy-svg/img/icon.png");
              background-repeat: no-repeat;
              background-position: center; 
              background-size: 72%;
              transiton: all .3s; 
              -webkit-transiton: all .3s; 
          }


         .wp-badge.svg-support-lable {
             background: url("'.plugins_url().'/easy-svg/img/backend-icon-lable.png") center 25px no-repeat #e57d31;
              background-size: 6em;
              padding-right: 1em;
              padding-left: 1em;
          }

        </style>
    ';
 }
  
/* ============================
     Options Page Markup 
   ============================ */ 


function svg_settings() {  ?>

    <div class="wrap about-wrap full-width-layout">

      <!-- Add Header Section  -->

        <h1>
           <?php _e('Welcome to Easy SVG Support for WordPress' , 'easy-svg'); ?>
        </h1>

        <p class="about-text">
          <?php
           _e('WordPress Easy SVG Plugin was created for you! Easy to install, easy to optimize, easy to config all svg uploads. Our big feature is to look your SVG File in the Media Libary. Here on the optionspage you can add more uploadlimit size. Best Regards Benjamin Zekavica.' , 'easy-svg'); ?>
        </p>
        <div class="wp-badge svg-support-lable">
            <?php _e('Easy SVG Support Version 2.0', 'easy-svg'); ?><br />
        </div>

        <!-- Add options for memory-->


        <h3>
          <?php _e('Add Upload Maximum File Size for SVG', 'easy-svg'); ?>
        </h3>

    </div>



<?php 

 }