/*
* Main scripts
* © Copyright 2017-2018, DIF Design, All Rights reserved.
* @author Tyler Seabury, tylerseabury@gmail.com
* @author DIF Design
* @authorURL https://github.com/TJSeabury/
* @authorURL http://marketmentors.com/
* @version 1.0
*/
window.addEventListener( 'MarketMentorsCoreReady', function main () {
    'use strict';

    let D = MarketMentorsCoreUtilities;
    let $ = jQuery;
    let d = document;
    let h = d.documentElement;
    let w = window;

    /*
    * Tags each page with a class based on the Wordpress fed page title.
    * Useful for targeting specific pages with styles.
    */
    document.body.classList.add( document.title.replace( /\W/g, '' ) );

    if ( /iPad|iPhone|iPod/.test( navigator.userAgent ) && !window.MSStream ) {
        h.classList.add( 'IOS' );
    }

    if ( 0 !== D.getIEVersion() ) {
        h.classList.add( 'IE' );
    }

    /*
    * Sets the height of elements to the viewport height minus the headers.
    * @use Add class 'difFullHeight' to elements to set their height.
    */
    const argsFull = ['.difFullHeight', 1.0, ['#wpadminbar', '.mk-header'], 666];
    const argsHalf = ['.difFullHeight', 0.5, ['#wpadminbar', '.mk-header'], 666];
    const mobile = ( D.W > 1023 ) ? false : true;
    D.heightSetter( ...argsFull, mobile );
    D.heightSetter( ...argsHalf, mobile );

    /*
    * Override Juptier preloader so that it fires an event on loading complete.
    */
    w.addEventListener(
        'load',
        () => {
            w.MK.ui.preloader.hide = function hide () {
                window.dispatchEvent( new CustomEvent( 'mk-preloader-complete' ) );
                $( this.dom.overlay ).fadeOut(
                    600,
                    "easeInOutExpo",
                    function () {
                        $( 'body' ).removeClass( 'loading' );
                    }
                );
            };
        }
    );



    /* --------------------------------------------------- */


    // code . . . 


    /* --------------------------------------------------- */

    /*
    * Smooth scroll to anchor.
    * @use Add class 'difScrollToAnchor' to any link to enable smooth scrolling to it's target.
    */
    const links = document.querySelectorAll( '.difScrollToAnchor' );
    [].forEach.call( links, e => {
        e.addEventListener( 'click', ev => {
            ev.preventDefault();
            const sY = getScrollAmount( document.querySelector( e.href.replace( window.location.protocol + '//' + window.location.host + window.location.pathname, '' ) ) );
            $( "html, body" ).animate( { scrollTop: sY + 'px' }, 666 );
        } );
    } );
    function getScrollAmount ( t ) {
        const wpHeader = document.querySelector( '#wpadminbar' ).offsetHeight || 0;
        const fusionHeader = document.querySelector( '.fusion-header-wrapper' ).offsetHeight || 0;
        return t.getBoundingClientRect().top - wpHeader - fusionHeader;
    }

    /*
    * Generic prevent Default action.
    * Simply prevent the default behavior of clicks, nothing more.
    * @use Add class 'jsNoDefault' to any element to prevent actions, i.e., clicks on links.
    */
    const noDefaults = document.getElementsByClassName( 'jsNoDefault' );
    for ( let i = 0; i < noDefaults.length; ++i ) {
        if ( noDefaults[i] instanceof HTMLElement ) {
            noDefaults[i].addEventListener( 'click', e => {
                e.preventDefault();
            } );
        }
    }
} );






