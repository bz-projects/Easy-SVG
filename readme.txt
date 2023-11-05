=== Easy SVG Support ===
Author URI: https://www.benjamin-zekavica.de
Plugin URI: https://wordpress.org/plugins/easy-svg/
Contributors: Benjamin_Zekavica
Tags: svg, svg support, upload svg, svg media, easy upload, easy-svg, easy svg, files, upload, icons, upload limit
Requires at least: 4.9
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 3.5
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

This Plugin allows you to upload SVG Files into your Media library.

== Description ==

= Direct Upload SVG Files into WordPress  =

EASY SVG Support is a Plugin which allows you to upload SVG Files into your Media library. This plugin was created for persons, who don’t need much options for SVG.

= Features of the plugin include: =

* Uploading SVG Support for WordPress
* Easy installation
* Display SVG Files in the Media Libary
* SVG Sanitize Files direcly 
* SVG Sanitize – Custom Hooks for Tags and Attributes
* Updated for the new WordPress Gutenberg Editor
* Support for PHP 8.2


= Documentation & Support =

Got a problem or need help with Easy SVG Support? Than you can write me an e-mail:

info@benjamin-zekavica.de or you can ask your question in the forums section.

== Installation ==

1. Activate the plugin.
2. Go to the Media Libary and Upload your SVG Files.
3. Upload now your SVG Files.
4. Go to the Page or ACF and choose your File and save changes.


== Frequently Asked Questions ==

= SVG Sanitize – Allow Tags & Attributes Hooks =

**Hook: esw_svg_allowed_tags**



        // XML TAGS
        add_filter( 'esw_svg_allowed_tags', function ($tags) {
            $tags[] = 'p';
            $tags[] = 'info';
            
            return $tags;
        } );


**Hook: esw_svg_allowed_attributes**

        // XML attributes
        add_filter( 'esw_svg_allowed_attributes', function ( $attributes ) {
            $attributes[] = 'src';
            
            return $attributes;
        } );

= Do you need a Source Code? =

Please check out my repository on Github:

[GitHub Repository](https://github.com/bz-projects/Easy-SVG)

== Screenshots ==
1. Easy SVG Support in Gutenberg
2. Upload direct into your WordPress Media


== Changelog ==

= 3.5: 5th of November, 2023 =
* Support for new WordPress version 6.4
* Support Gutenberg Version
* Updated Translation
* Better Support for PHP 8.2

= 3.4: 19th of June, 2023 =
* Support for new WordPress version 6.3
* Support Gutenberg Version
* Updated SVG svg-sanitize
* Better Support for PHP 8.2

= 3.3.1: 13th of March, 2023 = 
* Support for new WordPress version 6.2
* Support Gutenberg Version 

= 3.3.0: 29th of May, 2022 =

* Support for new WordPress version 6.0
* Support Gutenberg Version 
* SVG Sanitize Files direcly 
* Security Update
* New & updated POT-File for Translation
* SVG Sanitize – Custom Hooks for Tags and Attributes


= 3.2.0: 26th of January, 2022 =

* Support for new WordPress version 5.9
* Support Gutenberg Version 

= 3.1.0: 21th of July, 2021 =

* Support for new WordPress version 5.8
* Support Gutenberg Version 


= 3.0.0: 26th of May, 2021 =

* Support for new WordPress version

= 2.9.1: 28th of January, 2021 =

* Add PHP 8.0 support
* Support for new version of the Gutenberg Editor
* Support for new WordPress version

= 2.9: 28th of October, 2020 =

* Security Fixes
* Support for new WordPress version

= 2.8: 02th of July, 2020 =

* Security Fixes
* Updated Language files
* Support for new WordPress version

= 2.7: 24th of January, 2020 =

* Add Support for WordPress 5.3.2
* Gutenberg Editor Post Image Size  
* Security Fixes

= 2.6: 21th of September, 2019 =

* Add Support for WordPress 5.2.3
* Fixes

= 2.5.1: 12th of June, 2019 =

* Add Support for WordPress 5.2.1

= 2.5: 31th of March, 2019 =

* Add SVG Performance Update
* Add Security Update 
* Add Support for WordPress 5.1.1
* Add PHP 7.3 Support Update
* Remove external CSS Stylesheet -> Better Backend Performance (Write Less CSS Code in Style Tag into the Header)
* Some Changes and Fixes

= 2.4: 12th of December, 2018 =

* Higher Code Quality
* Security Update 
* Full Gutenberg Support in Backend


= 2.3: 8th of August, 2018 =

* (NEW Full WordPress 5.0 Support inc. Gutenberg Support
* (NEW) Now you can see all SVG Files in the Backend (ACF Support) and for Galleries 
* (REMOVE) Removing JavaScript File (The Plugin is now faster and easier)
* (CHANGE) Edit Language Files 


= 2.2.2: 27th of May, 2018 =

* Add correction of the new versions number


= 2.0.3: 27th of Febuary, 2018 =

* Add better security with index.php

= 2.0.2: 10th of Febuary, 2018 =

* Add new Versions Number

= 2.0.1: 10th of Febuary, 2018 =

* Add JQuery Function (Please Update now!)


= 2.0: 10th of Febuary, 2018 =

* Add better Security SVG Support(XML)
* Add better Code Quality and more code comments for WordPress Developers
* Add A Dashboard Widget to remember you for Easy SVG
* Display SVG Files into WordPress Media Libary
* Add Translation Files (Template)
* New Translation FIles for (EN, US, DE, DE Formal, HR)
* Add JavaScript to Backend
* Add CSS for Display SVG Files into the Backend

= 1.1: 29th of November, 2017 =

* Add a smole Alert message for users.

= 1.0.1: 28th of November, 2017 =

* Edit new Text


= 1.0.0: 28th of November, 2017 =

* Initial Release

== Upgrade Notice ==
This plugin can use on beginning versions of WordPress 4.0 to 4.9
