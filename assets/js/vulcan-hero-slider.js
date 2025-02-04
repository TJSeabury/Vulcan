/* 
* An html based slider.
* © Copyright 2017, Tyler Seabury, All Rights reserved.
* @author Tyler Seabury, tylerseabury@gmail.com
* @authorURI https://github.com/TJSeabury/
* @version 1.2
*/
class HeroSlider {
  /* 
  * @param {HTML Element} container - An html element to inject the slider into.
  * @param {Object Array} content - The images and html content.
  * @param {Integer} interval - Time between slide transitions in miliseconds.
  * @param {Integer} speed - Slide transition time in miliseconds.
  */
  constructor( container, content, interval, speed ) {
    this.container = container;
    this.content = content;
    this.controlsArray = [];
    this.interval = interval;
    this.speed = speed;
    this.shifting = false;
    this.loop = null;
    this.slides = null;
    this.activeSlide = null;
    this.activeSlideIndex = 0;
    this.preload = (
      () => {
        let p = document.createElement( 'div' );
        p.setAttribute(
          'style',
          'position: absolute;' +
          'top: 0;' +
          'right: 0;' +
          'bottom: 0;' +
          'left: 0;' +
          'opacity: 0;'
        );
        return p;
      }
    )();

    if ( !this.container.classList.contains( 'HeroSlider' ) ) {
      this.container.classList.add( 'HeroSlider' );
    }

    this.view = (
      () => {
        let v = document.createElement( 'div' );
        v.classList.add( 'view' );
        return v;
      }
    )();

    this.overlay = (
      () => {
        let o = document.createElement( 'div' );
        o.classList.add( 'overlay' );
        o.classList.add( 'difd_add-triangle-invert-bottom-right-cyan-offset_-64px_' );
        o.shapeEvent = new CustomEvent(
          'newshape',
          {
            detail:
            {
              emitter: o
            }
          }
        );
        return o;
      }
    )();

    this.slides = (
      () => {
        let sa = [];
        let i = 0;
        for ( const src of this.content ) {
          let s = document.createElement( 'figure' );
          s.classList.add( 'slide' );
          s.setAttribute( 'data-dhs-index', i++ );
          s.setAttribute( 'style', 'transition:' + this.speed + ';' );
          if ( src ) {
            s.style.backgroundImage = 'url(' + src + ')';
            let pre = document.createElement( 'img' );
            pre.src = src;
            this.preload.appendChild( pre );
          }
          sa.push( s );
        }
        return sa;
      }
    )();

    this.controls = (
      () => {
        const c = document.createElement( 'div' );
        c.classList.add( 'controls' );
        function createControl ( s, that ) {
          let g = document.createElement( 'span' );
          g.classList.add( 'goto' );
          g.addEventListener(
            'click',
            function handleClick () {
              if ( !that.shifting ) {
                that.goToSlide(
                  that.activeSlideIndex,
                  parseInt( s.getAttribute( 'data-dhs-index' ) )
                );
                that.resetInterval();
              }
            }
          );
          return g;
        }
        for ( const slide of this.slides ) {
          const g = createControl( slide, this );
          this.controlsArray.push( g );
          c.appendChild( g );
        }
        return c;
      }
    )();

    this.activeSlide = this.view.appendChild(
      this.slides.slice(
        this.activeSlideIndex,
        this.activeSlideIndex + 1
      )[0]
    );
    this.container.appendChild( this.preload );
    this.setActiveControlStyles();
    this.container.appendChild( this.view );
    this.container.appendChild( this.overlay );
    this.container.removeChild( this.preload );
    if ( this.slides.length > 1 ) {
      this.container.appendChild( this.controls );
      this.loop = setInterval(
        () => {
          if ( !this.shifting ) {
            this.advance();
          }
        },
        this.interval
      );
    }
    this.view.style.transition = this.speed + 'ms ease all';
    return this;
  }

  setActiveControlStyles () {
    let ix = 0;
    for ( const control of this.controlsArray ) {
      if ( control.classList.contains( 'active' ) ) {
        control.classList.remove( 'active' );
      }
      if ( this.activeSlideIndex === ix++ ) {
        control.classList.add( 'active' );
      }
    }
  }

  advance () {
    this.shifting = true;
    let nsi = ( this.activeSlideIndex === this.slides.length - 1 ) ? 0 : this.activeSlideIndex + 1;
    let cs = this.activeSlide;
    let ns = this.slides.slice( nsi, nsi + 1 )[0];
    ns.style.transform = 'translateX(100%)';
    ns = this.view.appendChild( ns );
    setTimeout(
      () => {
        cs.style.transform = 'translateX(-100%)';
        ns.style.transform = 'translateX(0%)';
      },
      100
    );
    setTimeout(
      () => {
        this.view.removeChild( cs );
        this.activeSlide = ns;
        if ( this.activeSlideIndex < this.slides.length - 1 ) {
          this.activeSlideIndex++;
        }
        else {
          this.activeSlideIndex = 0;
        }
        this.setActiveControlStyles();
        this.shifting = false;
      },
      this.speed + 200
    );
  }

  retreat () {
    this.shifting = true;
    let psi = ( this.activeSlideIndex === 0 ) ? this.slides.length - 1 : this.activeSlideIndex - 1;
    let cs = this.activeSlide;
    let ns = (
      () => {
        let s = this.slides.slice( psi, psi + 1 )[0];
        s.style.transform = 'translateX(-100%)';
        return s;
      }
    )();
    ns = this.view.appendChild( ns );
    setTimeout(
      () => {
        cs.style.transform = 'translateX(100%)';
        ns.style.transform = 'translateX(0%)';
      },
      100
    );
    setTimeout(
      () => {
        this.view.removeChild( cs );
        this.activeSlide = ns;
        if ( this.activeSlideIndex > 0 ) {
          this.activeSlideIndex--;
        }
        else {
          this.activeSlideIndex = this.slides.length - 1;
        }
        this.setActiveControlStyles();
        this.shifting = false;
      },
      this.speed + 200
    );
  }

  goToSlide ( ixCurr, ixNext ) {
    if ( ixNext === ixCurr ) {
      return;
    }
    else if ( ixNext > ixCurr ) {
      this.activeSlideIndex = --ixNext;
      this.advance();
    }
    else if ( ixNext < ixCurr ) {
      this.activeSlideIndex = ++ixNext;
      this.retreat();
    }
  }

  resetInterval () {
    clearInterval( this.loop );
    this.loop = setInterval(
      () => {
        if ( !this.shifting ) {
          this.advance();
        }
      },
      this.interval
    );
  }

}


let heroSlider = null;
window.addEventListener(
  'load',
  () => {
    'use strict';
    const slider = document.getElementById( 'marketmentors-hero-slider' );
    if ( !slider ) {
      return;
    }
    if ( null === JSON.parse( slider.getAttribute( 'data-dhs-slides' ) ) ) {
      return;
    }
    heroSlider = new HeroSlider(
      slider,
      JSON.parse( slider.getAttribute( 'data-dhs-slides' ) ),
      6900,
      666
    );
    window.dispatchEvent( heroSlider.overlay.shapeEvent );
  }
);