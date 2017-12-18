/* 
* Controls for the Component: Vulcan Filtered Post Category
* © Copyright 2017, Tyler Seabury, All Rights reserved.
* @author Tyler Seabury, tylerseabury@gmail.com
* @authorURI https://github.com/TJSeabury/
* @version 1.0
*/
class VulcanFilteredCategoryControls
{
    constructor ( component = null )
	{
		this.controls = [];
		this.items = {};
		
		for ( const item of Array.from( component.getElementsByClassName('category-posts-wrapper')[0].children ) )
		{
			if ( item.classList.contains('filtered-post-category-item') )
			{
				item.tags = JSON.parse( item.getAttribute('data-tags') );
				item.setAttribute( 'visible', '' );
				item.visFlags = {};
				for ( const tag of item.tags )
				{
					if ( undefined === item.visFlags[tag] )
					{
						item.visFlags[tag] = true;
					}
					if ( undefined === this.items[tag] )
					{
						this.items[tag] = new Array(
							item
						);
					}
					else
					{
						this.items[tag].push( item );
					}
				}
			}
		}
		
		for ( const control of Array.from( component.getElementsByClassName('filtered-post-category-controls')[0].children ) )
		{
			if ( control.classList.contains('filtered-post-category-control') )
			{
				control.state = true;
				control.tag = control.querySelector('input').getAttribute('value');
				control.addEventListener(
					'change',
					ev =>
					{
						if ( false === control.state )
						{
							this.reveal( 
								control.tag,
								() =>
								{
									control.state = true;
								}
							);
						}
						else
						{
							this.obscure(
								control.tag,
								() =>
								{
									control.state = false;
								}
							);
						}
						ev.stopPropagation();
					},
					false
				);
				this.controls.push( control );
			}
		}
	}
	
	reveal ( tag, callback )
	{
		for ( const item of this.items[tag] )
		{
			if ( false === item.visFlags[tag] )
			{
				item.visFlags[tag] = true;
			}
			if ( true === this.sumFlags( item ) )
			{
				item.setAttribute( 'visible', '' );
			}
		}
		if ( 'function' === typeof callback )
		{
			callback();
		}
	}
	
	obscure ( tag, callback )
	{
		for ( const item of this.items[tag] )
		{
			if ( true === item.visFlags[tag] )
			{
				item.visFlags[tag] = false;
			}
			if ( false === this.sumFlags( item ) )
			{
				item.removeAttribute( 'visible' );
			}
		}
		if ( 'function' === typeof callback )
		{
			callback();
		}
	}
	
	sumFlags( item )
	{
		let flagSum = null;
		for ( let flag in item.visFlags )
		{
			if ( null === flagSum )
			{
				flagSum = item.visFlags[flag];
			}
			else
			{
				flagSum = flagSum || item.visFlags[flag];
			}
		}
		return flagSum;
	}
    
}

window.addEventListener(
	'load',
	() =>
	{
		'use strict';
		const component_nodes = document.querySelectorAll('.vulcan-filtered-post-category');
		let components = [];
		Array.from( component_nodes ).forEach(
			c_node =>
			{
				components.push( new VulcanFilteredCategoryControls( c_node ) );
			}
		);
	}
);














