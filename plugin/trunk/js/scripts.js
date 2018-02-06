/*  =======================================
    Plugin Name: Easy SVG Support 
    Author: Benjamin Zekavica
    Compatible on version WordPress 4.9.3
    Release 2018
    ======================================= */ 


//create a mutation observer to look for added 'attachments' in the media uploader
var observer = new MutationObserver(function(mutations){

  // look through all mutations that just occured
  for (var i=0; i < mutations.length; i++){

    // look through all added nodes of this mutation
    for (var j=0; j < mutations[i].addedNodes.length; j++){

        //get the applicable element
        element = $(mutations[i].addedNodes[j]); 

        //execute only if we have a class
        if(element.attr('class')){

            elementClass = element.attr('class');
            //find all 'attachments'
            if (element.attr('class').indexOf('attachment') != -1){

                //find attachment inner (which contains subtype info)
                attachmentPreview = element.children('.attachment-preview');

                if(attachmentPreview.length != 0){

                    //only run for SVG elements
                    if(attachmentPreview.attr('class').indexOf('subtype-svg+xml') != -1){

                        //bind an inner function to element so we have access to it. 
                        var handler = function(element){

                            //do a WP AJAX call to get the URL 
                            $.AJAX({

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

observer.observe(document.body, {
  childList: true,
  subtree: true
});