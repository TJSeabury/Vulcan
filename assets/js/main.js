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
	let d = document;
	
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
	
	/*
	* Rectify the height of mk-header that has been changed by custom css.
	* For unknown reasons Jupiter's scripts do not account for the changes,
	* so we'll handle them here asychronously.
	*/
	let rectifyHeaderHeight = new Promise( 
		( resolve, reject ) =>
		{
			let headerElements = 
			[
				d.querySelector('.mk-header-holder'),
				d.querySelector('.mk-header-padding-wrapper')
			];
			let now = performance.now(),
				then = now;
			for(;;)
			{
				now = performance.now();
				if ( now - then < 5000 )
				{
					if (
						headerElements[0] &&
						headerElements[1]
					)
					{
						return resolve( 
							{
								'holder': headerElements[0],
								'padding': headerElements[1]
							} 
						);
					}
					else
					{
						headerElements = 
						[
							d.querySelector('.mk-header-holder'),
							d.querySelector('.mk-header-padding-wrapper')
						];
					}
					
				}
				else
				{
					return reject( 'Timed out: could not query elements.' );
				}
			}
		}
	);
	rectifyHeaderHeight.then( 
		header =>
		{
			header.padding.setAttribute( 
				'style',
				( 
					'padding-top: ' +
					parseInt( header.holder.offsetHeight ) +
					'px !important' 
				) 
			);
		}
	);
	
	/*
	* Handle sticky header
	*/
	const header = d.querySelector('.mk-header');
	const headerHolder = header.querySelector('.mk-header-holder');
	const headerToolbar = headerHolder.querySelector('.mk-header-toolbar');
	header.style.transition = 'all 333ms ease';
	headerToolbar.style.overflow = 'hidden';
	headerToolbar.style.zIndex = '-10010';
	let htToggleButton = (
		( h, ht ) =>
		{
			let b = d.createElement('div');
			b.classList.add('difd_htToggleButton');
			b.innerHTML = '<div class="difd_iconWrapper"><div class="difd_vLine"></div><div class="difd_hLine"></div></div>';
			let icoWarpper = b.querySelector('.difd_iconWrapper');
			icoWarpper.addEventListener(
				'click',
				() =>
				{
					if ( ! h.classList.contains('difd_toolbar_revealed') )
					{
						h.classList.add('difd_toolbar_revealed');
						ht.style.marginTop = ('0px');
					}
					else
					{
						h.classList.remove('difd_toolbar_revealed');
						ht.style.marginTop = (-(ht.offsetHeight) + 'px');
					}
				},
				{
					passive: true
				}
			);
			return b;
		}
	)( header, headerToolbar );
	header.querySelector('.mk-header-inner').appendChild( htToggleButton );
	
	let observer = new MutationObserver( 
		mutations => 
		{
			for ( let m = 0, mu = mutations[m]; m < mutations.length; ++m )
			{
				let toolbarRevealed = ( -1 === difClassLists( mu.oldValue.split( /\s+/ ), [].slice.call(mu.target.classList) ).indexOf('difd_toolbar_revealed') );
				if ( 'attributes' === mu.type && toolbarRevealed )
				{
					if ( header.classList.contains('a-sticky') )
					{
						new Promise( 
							resolve =>
							{
								headerToolbar.style.transition = 'margin 333ms ease';
								headerToolbar.style.marginTop = -(headerToolbar.offsetHeight) + 'px';
								setTimeout( 
									() =>
									{
										resolve( headerToolbar );
									}, 
									333
								);
							}
						)
						.then(
							h =>
							{
								h.style.position = 'absolute';
								h.style.top = '100%';
								h.style.backgroundColor = '#004f6b';
							}
						);
					}
					else
					{
						new Promise( 
							resolve =>
							{
								headerToolbar.style.transition = 'none';
								headerToolbar.style.backgroundColor = '#e7e7e7';
								if ( header.classList.contains('difd_toolbar_revealed') )
								{
									header.classList.remove('difd_toolbar_revealed');
									setTimeout( 
										() =>
										{
											headerToolbar.style.marginTop = -(headerToolbar.offsetHeight) + 'px';
										}, 
										0
									);
									
								}
								setTimeout( 
									() =>
									{
										headerToolbar.style.marginTop = -(headerToolbar.offsetHeight) + 'px';
										headerToolbar.style.position = 'relative';
										headerToolbar.style.top = '0%';
									}, 
									1
								);
								setTimeout( 
									() =>
									{
										resolve( headerToolbar );
									}, 
									2
								);
							}
						)
						.then(
							h =>
							{
								h.style.transition = 'margin 333ms ease';
								h.style.marginTop = '0px';
							}
						);
					}
				}
			}
		}
	);
	observer.observe(
		header,
		{
			attributes: true,
			attributeOldValue: true
		}
	);
	
	function difClassLists( a1, a2 )
	{
		let uc = [];
		for ( const cl of a1 )
		{
			if ( -1 === a2.indexOf( cl ) )
			{
				uc.push( cl );
			}
		}
		for ( const cl of a2 )
		{
			if ( -1 === a1.indexOf( cl ) )
			{
				uc.push( cl );
			}
		}
		return uc;
	}
	
	
	/*
	* Parse and inject shapes
	*/
	window.addEventListener(
		'load',
		() =>
		{
			let pageSectionsToBeEnhanced = d.querySelectorAll('[class*="difd_add-triangle-"]');
			for ( const ps of pageSectionsToBeEnhanced )
			{
				[].forEach.call( 
					ps.classList, 
					e => 
					{
						if ( e.includes('difd_add-triangle-') )
						{
							let triangle = d.createElement('figure');
							triangle.classList.add('triangle');
							
							let offset = !1;
							
							let scale = !1;
							let transformX = !1;
							let transformY = !1;
							
							let styles = 
								'position: absolute;' +
								'width: 0;' +
								'height: 0;' +
								'margin: 0;' +
								'border-style: solid;' +
								'padding: 0;';
							
							if ( e.includes('offset') )
							{
								offset = e.match( /(offset_)(.*)(_)/ )[2];
							}
							
							if ( e.includes('scale') )
							{
								scale = e.match( /(scale_)(.*)(_)/ )[2];
							}
							
							if ( e.includes('transformX') )
							{
								transformX = e.match( /(transformX_)(.*)(_)/ )[2];
							}
							
							if ( e.includes('transformY') )
							{
								transformY = e.match( /(transformY_)(.*)(_)/ )[2];
							}
							
							if ( e.includes('transform') )
							{
								let transforms = 
									( scale ? ' scale(' + scale + ')' : '' ) +
									( transformX ? ' transformX(' + transformX + ')' : '' ) +
									( transformY ? ' transformY(' + transformY + ')' : '' );
								let origins = '';
								if ( transforms )
								{
									styles += 'transform:' + transforms + ';';
								}
								if ( e.includes('top') )
								{
									origins += ' top';
								}
								else if ( e.includes('bottom') )
								{
									origins += ' bottom';
								}
								else
								{
									origins += ' center';
								}
								if ( e.includes('left') )
								{
									origins += ' left';
								}
								else if ( e.includes('right') )
								{
									origins += ' right';
								}
								else
								{
									origins += ' center';
								}
								styles += 'transform-origin:' + origins + ';';
							}

							if ( ! e.includes('invert') )
							{
								styles += 'border-bottom-width: calc( var(--halfViewportHeight) + 128px );';
								styles += 'border-bottom-color: transparent;';
							}
							else
							{
								styles += 'border-top-width: calc( var(--halfViewportHeight) + 128px );';
								styles += 'border-top-color: transparent;';
							}

							if ( e.includes('top') )
							{
								styles += 'top: ' + ( offset ? offset : '0px' ) + ';';
							}

							if ( e.includes('middle') )
							{
								styles += 'top: calc( 50% - ( var(--halfViewportHeight) / 2 + 64px - ' + ( offset ? offset : '0px' ) + ' ) );';
							}

							if ( e.includes('bottom') )
							{
								styles += 'bottom: ' + ( offset ? offset : '0px' ) + ';';
							}

							if ( e.includes('left') )
							{
								styles += 'left: 0;';
								styles += 'border-left-width: var(--sideMarginWidth);';

								if ( e.includes('orange') )
								{
									styles += 'border-left-color: var(--themeOrange);';
								}

								if ( e.includes('cyan') )
								{
									styles += 'border-left-color: var(--themeCyan);';
								}
							}

							if ( e.includes('right') )
							{
								styles += 'right: 0;';
								styles += 'border-right-width: var(--sideMarginWidth);';

								if ( e.includes('orange') )
								{
									styles += 'border-right-color: var(--themeOrange);';
								}

								if ( e.includes('cyan') )
								{
									styles += 'border-right-color: var(--themeCyan);';
								}
							}

							triangle.setAttribute( 'style', styles );

							ps.appendChild(triangle);
						}
					} 
				);
			}
		}
	);
	
	window.addEventListener(
		'load',
		() =>
		{
			/*
			* Sticky footer
			*/
			var limit = Math.max(
				document.body.scrollHeight,
				document.body.offsetHeight,
				document.documentElement.clientHeight,
				document.documentElement.scrollHeight,
				document.documentElement.offsetHeight
			);
			let footerCopyright = document.querySelector('.mk-footer-copyright');
			let cl = null;
			let footerHeight = 0;
			if ( footerCopyright )
			{
				cl = footerCopyright.classList;
				let stickyFooterWrapper = footerCopyright.querySelector('#difd_footer-menu-wrapper');
				if ( stickyFooterWrapper )
				{
					footerHeight = (
						wrap =>
						{
							let height = 0;
							let menu = wrap.querySelector('.difd_footer-menu');
							let content = wrap.querySelector('.difd_footer-menu-static-content');
							if ( menu && content )
							{
								menu = parseInt( menu.offsetHeight );
								content = parseInt( content.offsetHeight );
								height = menu + content;
								return height;
							}
							else
							{
								return parseInt( wrap.offsetHeight );
							}
						}
					)( stickyFooterWrapper );
					footerCopyright.style.setProperty( 'padding-top', footerHeight + 'px', 'important' );
				}
			}
			window.addEventListener(
				'scroll',
				() =>
				{
					if ( ( window.innerHeight + window.scrollY ) >= limit ) {
						if ( ! cl.contains('stuck-bottom') )
						{
							cl.add('stuck-bottom');
						}
					}
					else
					{
						if ( cl.contains('stuck-bottom') )
						{
							cl.remove('stuck-bottom');
						}
					}
				}
			);
			
			/*
			* Trigger scroll handlers to update if the window loads with a non-zero scroll value.
			*/
			if ( 0 < window.scrollY )
			{
				window.dispatchEvent( new Event('scroll') );
			}
			
		}
	);
    
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











