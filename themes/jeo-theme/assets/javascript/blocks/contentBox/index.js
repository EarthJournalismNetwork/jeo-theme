import { RichText, InnerBlocks } from "@wordpress/block-editor";

import { __ } from "@wordpress/i18n";
import { Button, SelectControl, TextControl } from "@wordpress/components";
//const {  } = wp.editor;

wp.blocks.registerBlockType("jeo-theme/content-box", {
	title: "Content Box",
	icon: "format-aside",
	category: "common",
	supports: {
		align: false,
	},
	attributes: {
		
	},

	edit: (props) => {
		const {
			className,
			isSelected,
			attributes: {
			},
			setAttributes,
		} = props;
		
		const TEMPLATE =  [ 
				[ 'core/paragraph', { placeholder: 'Insert the text of the content box here' } ],
		];
		  
		return (
			<>
				<div className="content-box">
					<div>
						<InnerBlocks
							allowedBlocks={[ 'core/paragraph' ]}
							template={TEMPLATE}
							templateLock="all"
						/>
					</div>
				</div>
			</>
		);
	},

	save: (props) => {
		const {
			className,
			isSelected,
			attributes: {
			  title,
			},
			setAttributes,
		  } = props;
	  

		return (
			<>	
				<div className="content-box">
					<InnerBlocks.Content/>
				</div>
			</>
		);
	},
});

// [mc4wp_form id="65"]
