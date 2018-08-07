
(function() {

    // Define Variables 

	var __ = wp.i18n.__; 
	var createElement = wp.element.createElement; 
	var registerBlockType = wp.blocks.registerBlockType; 

    // Register Block

	registerBlockType(
		'esw/svgmodule', {
			title: __( 'Easy SVG' ),  // Name of Block
			icon: 'admin-customizer', // Add Dashicon Icon 
			category: 'common', // Optional Place : common, formatting, layout widgets, embed.

            // Backend            
			edit: function( props ) {
				return createElement(
					'div',{
						className: props.className,  // This add Class Name form RegisterBlockType
                    },
                    'SVG'
				);
			},

            // Frontend            
			save: function( props ) {
				return createElement(
					'p', // Tag type.
					{
						className: props.className,  // Class name is generated using the block's name prefixed with wp-block-, replacing the / namespace separator with a single -.
					},
					'Static block example.' // Block content
				);
			},
		}
	);
})();
