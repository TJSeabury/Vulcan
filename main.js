/*
* Main scripts
* © Copyright 2017, DIF Design, All Rights reserved.
* @author Tyler Seabury, tylerseabury@gmail.com
* @author DIF Design
* @authorURL https://github.com/TJSeabury/
* @authorURL http://difdesign.com/
* @version 1.0
*/
window.addEventListener('DIFDesignCoreReady', function main() {
	'use strict';
	
	let D = DIFDesignCoreUtilities;
	let $ = jQuery;
	
	/*
    * Tags each page with a class based on the Wordpress fed page title.
    * Useful for targeting specific pages with styles.
	*/
	document.body.classList.add( document.title.replace( /\W/g, '' ) );
    
    /*
    * Sets the height of elements to the viewport height minus the headers.
    * @use Add class 'difFullHeight' to elements to set their height.
    */
	D.heightSetter( '.difFullHeight', 1.0, ['#wpadminbar', '.mk-header'], 666 );
	
	
	let wpadminbar = document.getElementById('wpadminbar');
	let header = document.querySelector('.mk-header');
	let rHeader = header.querySelector('.mk-header-holder');
    
    /* --------------------------------------------------- */
    

    // code . . . 
    

    /* --------------------------------------------------- */
	
	/*
    * Smooth scroll to anchor.
    * @use Add class 'difScrollToAnchor' to any link to enable smooth scrolling to it's target.
	*/
	const links = document.querySelectorAll('.difScrollToAnchor');
	[].forEach.call( links, e => 
	{
		e.addEventListener( 'click', ev => 
		{
			ev.preventDefault();
			const sY = getScrollAmount( document.querySelector( e.href.replace( window.location.protocol + '//' + window.location.host + window.location.pathname, '' ) ) );
			$("html, body").animate({ scrollTop: sY +'px' }, 666);
		} );
	} );
	function getScrollAmount(t)
	{
		const wpHeader = document.querySelector('#wpadminbar').offsetHeight || 0;
		const fusionHeader = document.querySelector('.fusion-header-wrapper').offsetHeight || 0;
		return t.getBoundingClientRect().top - wpHeader - fusionHeader;
	}
	
	/*
	* Generic prevent Default action.
    * Simply prevent the default behavior of clicks, nothing more.
    * @use Add class 'jsNoDefault' to any element to prevent actions, i.e., clicks on links.
	*/
	const noDefaults = document.getElementsByClassName( 'jsNoDefault' );
	for ( let i = 0; i < noDefaults.length; ++i )
	{
		if ( noDefaults[i] instanceof HTMLElement )
		{
			noDefaults[i].addEventListener( 'click', e =>
			{
				e.preventDefault();
			} );
		}
	}
});