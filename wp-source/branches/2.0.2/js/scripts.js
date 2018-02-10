/*
Plugin Name: Easy SVG Support 
Description: Add SVG Support for WordPress.
Version:     2.0.2
Author:      Benjamin Zekavica

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


/* Add Function with AJAX to display the thumbnail */ 

var observer = new MutationObserver(function(mutations){

  for (var i=0; i < mutations.length; i++){
    for (var j=0; j < mutations[i].addedNodes.length; j++){
        element = $(mutations[i].addedNodes[j]); 
        if(element.attr('class')){

            elementClass = element.attr('class');
            if (element.attr('class').indexOf('attachment') != -1){

                attachmentPreview = element.children('.attachment-preview');
                if(attachmentPreview.length != 0){

                    if(attachmentPreview.attr('class').indexOf('subtype-svg+xml') != -1){
                        
                        var handler = function(element){

                            //do a WP AJAX call to get the URL 
                            jQuery.AJAX({

                                url: AJAXurl,
                                data: {
                                    'action'        : 'svg_get_attachment_url',
                                    'attachmentID'  : element.attr('data-id')
                                },
                                success: function(data){
                                    if(data){
                                        //replace the default image with the SVG
                                        element.find('img').attr('src', data);
                                        element.find('.filename').text('SVG Image');
                                    }
                                }
                            });

                        }(element); 

                    }
                }
            }
        }
    }
  }
});
