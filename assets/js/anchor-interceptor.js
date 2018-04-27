/*
* Anchor Interceptor
*/
(
    function( $ )
    {
		'use strict';
        window.addEventListener(
            'load',
            () =>
            {
				// Interceptor constructor must be called with a css selector string 
				// that targets the header and/or nav to be used when calculating 
				// scroll offsets; e.g., '.mk-header.header-style-1'.
                let interceptor = new AnchorInterceptor( '.mk-header' );
				if ( true === interceptor.checkForTarget() )
				{
					interceptor.doAnimate( interceptor.getTarget() );
				}
				window.addEventListener(
					'click',
					function( ev )
					{
						interceptor.clickHandler( ev );
					}
				);
            }
        );

        class AnchorInterceptor
{
	constructor( headerQueryString )
	{
		this.hqs = headerQueryString;
		this.getHeaderHeight();
	}

	getHeaderHeight()
	{
		this.header = (
			function( hqs )
			{
				let header = document.querySelector( hqs );
				if ( undefined !== header || null !== header )
				{
					return header;
				}
			}
		)( this.hqs);
		this.headerOffset = this.header.offsetHeight || 0;
	}

	setTarget( anchorTarget )
	{
		sessionStorage.setItem( 'anchorTarget', anchorTarget );
	}

	getTarget()
	{
		return sessionStorage.getItem( 'anchorTarget' );
	}
	removeTarget()
	{
		if ( sessionStorage.getItem( 'anchorTarget' ) )
		{
			sessionStorage.removeItem( 'anchorTarget' );
		}
	}

	checkForTarget()
	{
		if ( 
			null !== sessionStorage.getItem( 'anchorTarget' ) &&
			'' !== sessionStorage.getItem( 'anchorTarget' )
		)
		{
			return true;
		}
		return false;
	}

	getLinkFromTarget( target ) {
		while ( target.localName !== 'a' ) {
			if ( ! target.localName )
			{
				return false;
			}
			target = target.parentNode;
		} 
		return target;
	}

	getStripURL( link )
	{
		let url = link.href;
		url = url.substring( 0, url.indexOf( '#' ) );
		return url;
	}

	getTargetID( link )
	{
		return link.href.substring( link.href.indexOf( '#' ) + 1 );
	}

	doNavigate( url, newTab )
	{
		if ( newTab )
		{
			window.open( url, '_blank' );	
		}
		else
		{
			window.location.href = url;
		}
	}

	doAnimate( id )
	{
		const anchorTarget = document.getElementById( id );
		if ( undefined === anchorTarget || null === anchorTarget )
		{
			console.error( 'AnchorInterceptor: anchorTarget is ' + anchorTarget );
			return;
		}
		let deltaY = this.getScrollAmount( anchorTarget ) - this.headerOffset;
		if ( 0 === deltaY )
		{
			this.removeTarget();
			return;
		}
		let that = this;
		let html_or_body = ( !document.documentElement.classList.contains('IOS') ? 'html' : 'body' );
		jQuery( html_or_body ).animate(
			{
				scrollTop: deltaY + 'px'
			},
			{
				duration: 666,
				step: function( now, tween )
				{
					that.getHeaderHeight();
					deltaY = that.getScrollAmount( anchorTarget ) - that.headerOffset;
					tween.end = deltaY;
				}
			}
		);
		this.removeTarget();
	}

	getScrollAmount( t )
	{
		let windowOffset = t.getBoundingClientRect().top;
		let elementOffset = window.scrollY + windowOffset;
		return elementOffset;
	}

	clickHandler( event )
	{
		const link = this.getLinkFromTarget( event.target );
		const newTab = link.target === '_blank' ? true : false;
		if ( ! link )
		{
			return;
		}
		if ( ! link.hasAttribute( 'href' ) )
		{
			return;
		}
		event.preventDefault();
		const url = link.href;
		const cleanURL = this.getStripURL( link );
		const id = this.getTargetID( link );
		if ( -1 === url.indexOf('#') )
		{
			this.doNavigate( url, newTab );
		}
		else if ( window.location.href === cleanURL )
		{
			this.doAnimate( id );
		}
		else
		{
			this.setTarget( id );
			this.doNavigate( cleanURL, newTab );
		}
	}
}

    }
)( jQuery );