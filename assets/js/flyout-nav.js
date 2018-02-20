/*
 * Flyout Navigation
 * © Copyright 2017-2018, Tyler Seabury, All Rights reserved.
 * @author Tyler Seabury, tylerseabury@gmail.com
 * @author DIF Design
 * @authorURL https://github.com/TJSeabury/
 * @version 0.9.0
 * 
 * Override mk-responsive-menu with custom slideout menu.
 * 
 */
const overrideMKnav = () =>
{
    /*
    * Remove event listeners
    */
    const tb = d.querySelector( '.mk-header .mk-nav-responsive-link' );
    const tbClone = tb.cloneNode(true);
    tbClone.classList.add( 'dif-nav-responsive-link' );
    tb.parentNode.replaceChild( tbClone, tb );
    
    /*
    * Add logo and close button elements
    */
    const navToggle = d.querySelector( '.mk-header .dif-nav-responsive-link' );
    const nav = d.querySelector( '.mk-header .mk-responsive-wrap' );
    const closeButton = (
        () =>
        {
            let b = d.createElement( 'div' );
            b.classList.add( 'slide-out-menu-close' );
            b.innerHTML = '<div></div><div></div><div></div>';
            return b;
        }
    )();
    const logo = (
        () =>
        {
            let l = d.createElement( 'div' );
            l.innerHTML = '<img class="mk-resposnive-logo " title="" alt="" src="https://pauljrdesigns.com/wp-content/uploads/2018/01/logo.png">';
            l.classList.add( 'slide-out-menu-logo' );
            return l;
        }
    )();
    
    /*
    * Attatch new functionality
    */
    nav.state = false;
    nav.toggleVisibility = () =>
    {
        if ( true === nav.state )
        {
            h.classList.add( 'dif-nav-open' );
            nav.classList.add( 'nav-open' );
        }
        else
        {
            h.classList.remove( 'dif-nav-open' );
            nav.classList.remove( 'nav-open' );
        }
    };
    const toggleNavState = ( e, t = navToggle, n = nav ) =>
    {
        if ( true === n.state )
        {
            n.state = false;
        }
        else
        {
            n.state = true;
        }
        n.toggleVisibility();
    };
    nav.prepend( logo );
    nav.prepend( closeButton );
    closeButton.addEventListener( 'click', toggleNavState );
    navToggle.addEventListener( 'click', toggleNavState );
};
window.addEventListener( 'load', overrideMKnav );