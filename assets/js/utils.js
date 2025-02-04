/*
 * MarketMentors Core frontend library
 * © Copyright 2017-2018, Tyler Seabury, All Rights reserved.
 * @author Tyler Seabury, tylerseabury@gmail.com
 * @author Market Mentors
 * @authorURL https://github.com/TJSeabury/
 * @version 0.7.0
 */


let MarketMentorsCoreUtilities = new MarketMentorsCOREUTILITIES();
MarketMentorsCoreUtilities.announceReady();

function MarketMentorsCOREUTILITIES () {
  'use strict';

  let self = this;

  const html = document.documentElement;

  this._rootPathname = wpMeta.siteURL || '/';

  /*
 * Handy-Dandy nifty properties
 */
  window.addEventListener( 'resize', () => {
    this.W = document.body.clientWidth;
    this.H = window.innerHeight;
    html.style.setProperty( '--window-width', this.W + 'px' );
    html.style.setProperty( '--window-height', this.H + 'px' );
  } );
  window.dispatchEvent( new Event( 'resize' ) );

  /*
  * Returns the version number of IE or 0 for another browser.
  */
  this.getIEVersion = function () {
    const sAgent = window.navigator.userAgent;
    const Idx = sAgent.indexOf( 'MSIE' );
    if ( Idx > 0 ) {
      return parseInt( sAgent.substring( Idx + 5, sAgent.indexOf( '.', Idx ) ) );
    }
    else if ( !!navigator.userAgent.match( /Trident\/7\./ ) ) {
      return 11;
    }
    else {
      return 0;
    }
  };

  /*
  * Helper method to more easily create elements.
  * TODO - Needs to be fleshed out.
  */
  this.makeElement = function ( element = 'div', classNames = [], content = '', attr = {} ) {
    let r = null;
    if ( element ) {
      try {
        r = document.createElement( element );
      }
      catch ( e ) {
        console.error( 'Not a valid HTML Element tag name.' );
        throw e;
      }
    }
    if ( classNames && classNames.constructor === Array ) {
      for ( let c = 0; c < classNames.length; ++c ) {
        try {
          r.classList.add( classNames[c] );
        }
        catch ( e ) {
          throw e;
        }
      }
    }
    else {
      r.classList.add( classNames );
    }
    if ( content && ( typeof content === 'string' || content instanceof String ) ) {
      r.innerHTML = content;
    }
    if ( attr && attr.constructor === Object ) {
      try {
        switch ( r.tagName ) {
          case 'A':
            r.setAttribute( 'href', attr.href );
            r.setAttribute( 'target', attr.target );
            break;
          case 'IMG':
            r.setAttribute( 'src', attr.src );
            r.setAttribute( 'alt', attr.alt );
            break;
          default:
            break;
        }
      }
      catch ( e ) {
        throw e;
      }
    }
    return r;
  };

  /*
  * Polyfill for prepend
  * Source: https://github.com/jserz/js_piece/blob/master/DOM/ParentNode/prepend()/prepend().md
  */
  ( function ( arr ) {
    arr.forEach( function ( item ) {
      if ( item.hasOwnProperty( 'prepend' ) ) {
        return;
      }
      Object.defineProperty( item, 'prepend', {
        configurable: true,
        enumerable: true,
        writable: true,
        value: function prepend () {
          var argArr = Array.prototype.slice.call( arguments ),
            docFrag = document.createDocumentFragment();

          argArr.forEach( function ( argItem ) {
            var isNode = argItem instanceof Node;
            docFrag.appendChild( isNode ? argItem : document.createTextNode( String( argItem ) ) );
          } );

          this.insertBefore( docFrag, this.firstChild );
        }
      } );
    } );
  } )( [Element.prototype, Document.prototype, DocumentFragment.prototype] );

  /* 
  * Polyfill for focusin and focusout.
  */
  polyfill();
  function polyfill () {
    let w = window,
      d = w.document;
    if ( w.onfocusin === undefined ) {
      d.addEventListener( 'focus', addPolyfill, true );
      d.addEventListener( 'blur', addPolyfill, true );
      d.addEventListener( 'focusin', removePolyfill, true );
      d.addEventListener( 'focusout', removePolyfill, true );
    }
    function addPolyfill ( e ) {
      let type = e.type === 'focus' ? 'focusin' : 'focusout',
        event = new window.CustomEvent( type, { bubbles: true, cancelable: false } );
      event.c1Generated = true;
      e.target.dispatchEvent( event );
    }

    function removePolyfill ( e ) {
      if ( !e.c1Generated ) {
        d.removeEventListener( 'focus', addPolyfill, true );
        d.removeEventListener( 'blur', addPolyfill, true );
        d.removeEventListener( 'focusin', removePolyfill, true );
        d.removeEventListener( 'focusout', removePolyfill, true );
      }
      setTimeout( function () {
        d.removeEventListener( 'focusin', removePolyfill, true );
        d.removeEventListener( 'focusout', removePolyfill, true );
      } );
    }
  }

  /*
  * Validate links.
  */
  this.validateLinks = function () {
    return new Promise( ( resolve, reject ) => {
      let links = document.getElementsByTagName( 'a' ),
        valid = [],
        broken = [];
      for ( let a = 0; a < links.length; ++a ) {
        if ( links[a].getAttribute( 'href' ) !== undefined ) {
          if ( links[a].getAttribute( 'href' ).replace( links[a].baseURI, '' ) === '#' ) {
            broken.push( links[a] );
          } else {
            valid.push( links[a] );
            // and follow link to see if valid, but I'll write that later because
            // that functionality will require get requests and DOM parsing, and I
            // don't feel like doing all that right now.
          }
        } else {
          broken.push( links[a] );
        }
      }
      resolve( valid );
      reject( broken );
    } );
  };

  /*
   * Handles form ui animations
   *
   * !! TODO !!
   *
   * This is usefull but needs to be more generalized.
   */
  window.addEventListener( 'load', () => {
    let fields = document.getElementsByClassName( 'dif_movingLabel' );

    function focusedState ( element ) {
      element.addEventListener( 'focusin', ( e ) => {
        let a = e.target.parentElement.previousElementSibling;
        a.style.transform = 'translateY(-50%)';
        a.style.opacity = 0.3;
      }, true );
    }

    function blurredState ( element ) {
      element.addEventListener( 'focusout', ( e ) => {
        if ( e.target.value === '' ) {
          let a = e.target.parentElement.previousElementSibling;
          a.style.transform = 'translateY(0%)';
          a.style.opacity = 1.0;
        }
      }, true );
    }

    for ( let f = 0; f < fields.length; ++f ) {
      focusedState( fields[f] );
      blurredState( fields[f] );
    }
  } );

  /*
   * Set the height of a tagged element.
   * @param {HTMLElement Array} elements - The elements to set the height of.
   * @param {Number} fraction - A number to modify the height being applied to the element.
   * @param {HTMLElement Array} subtration - Elements to subtract the height of from the final height applied to the element.
   * @param {Number} timeout - Time in miliseconds to wait for asynchronus elements in the DOM.
   */
  this.heightSetter = function ( elements, fraction, subtraction, timeout, once ) {
    let fullHeightElements = document.querySelectorAll( elements );
    let subtractionElements = [];
    let heightHasBeenSet = false;
    for ( let s = 0; s < subtraction.length; ++s ) {
      subtractionElements[s] = document.querySelector( subtraction[s] );
    }
    if ( checkNotExists( subtractionElements ) ) {
      let observer = new MutationObserver( mutations => {
        for ( let m = 0; m < mutations.length; ++m ) {
          if ( mutations[m].type === 'childList' ) {
            for ( let s = 0; s < subtraction.length; ++s ) {
              subtractionElements[s] = document.querySelector( subtraction[s] );
            }
            if ( !checkNotExists( subtractionElements ) && !heightHasBeenSet ) {
              doHeightSet();
              heightHasBeenSet = true;
              observer.disconnect();
            }
          }
        }
      } );
      observer.observe( document.body, {
        attributes: true,
        childList: true,
        characterData: true,
        subtree: true
      } );
      setTimeout( () => {
        if ( !heightHasBeenSet ) {
          observer.disconnect();
          for ( let s = 0; s < subtraction.length; ++s ) {
            subtractionElements[s] = ( document.querySelector( subtraction[s] ) ) ? document.querySelector( subtraction[s] ) : false;
          }
          doHeightSet();
          heightHasBeenSet = true;
        }
      }, timeout );
    }
    else {
      doHeightSet();
    }
    function checkNotExists ( els ) {
      let notExist = false;
      for ( let el = 0; el < els.length; ++el ) {
        if ( !els[el] ) {
          notExist = true;
        }
      }
      return notExist;
    }
    function doHeightSet () {
      let handler;
      window.addEventListener( 'resize', handler = () => {
        let h = self.H;
        let s = 0;
        for ( let su = 0; su < subtractionElements.length; ++su ) {
          s += ( subtractionElements[su] ) ? subtractionElements[su].offsetHeight : 0;
        }
        h *= fraction;
        h -= s;
        for ( let e = 0; e < fullHeightElements.length; ++e ) {
          fullHeightElements[e].style.minHeight = h + 'px';
        }
      } );
      window.dispatchEvent( new Event( 'resize' ) );
      if ( once ) {
        setTimeout(
          () => {
            window.removeEventListener( 'resize', handler );
          },
          100
        );
      }
    }
  };

  /*
  * Allows shortcodes to be executed asynchronously.
  * Returns the generated content in a promise.
  */
  /* !! currently broken !! */
  /*this.doShortcode = function ( shortcode )
  {
      return new Promise( ( resolve, reject ) =>
      {
          let r = new XMLHttpRequest();
          r.open( 'POST', self._rootPathname + '/' + 'wp-admin/admin-ajax.php' + '/' );
          r.onload = () => {
              if ( r.readyState === r.DONE && ( r.status >= 200 && r.status <= 300 ) ) {
                  console.log(r.response);
                  resolve( r.response );
              } else {
                  reject( {
                      status: this.status,
                      statusText: r.statusText
                  } );
              }
          };
          r.onerror = () => {
              reject({status: this.status, statusText: r.statusText});
          };
          r.send( JSON.stringify({
              'action': 'do_shortcode',
              'fn': 'js_do_shortcode',
              'shortcode': shortcode
          }) );
      	
      } );
  };*/

  /*
   *
   * !! TODO !!
   *
   * Cross-browser implementation of element.addEventListener()
   * @param {function Array} fu -
   * @param {String} target -
   * @param {String} event -
   * @Use listen(function, 'target', 'event');
   */
  this.listen = function ( fu, target = 'window', event = 'load' ) {
    let listeners = new Promise( ( resolve, reject ) => {
      let validFunctions = [],
        invalidFunctions = [];
      if ( Array.isArray( fu ) ) {
        for ( let i = 0; i < fu.length; ++i ) {
          if ( typeof fu[i] === 'function' ) {
            validFunctions.push( fu[i] );
          } else {
            invalidFunctions.push( fu[i] );
          }
        }
      } else if ( typeof fu === 'function' ) {
        validFunctions.push( fu );
      }
      else {
        invalidFunctions.push( fu );
      }
      resolve( validFunctions );
      reject( invalidFunctions );
    } );
    listeners.then( ( valid ) => {
      for ( let i = 0; i < valid.length; ++i ) {
        if ( window.addEventListener ) { // W3C DOM
          target.addEventListener( event, valid[i], false );
        } else if ( window.attachEvent ) { // IE DOM
          target.attachEvent( 'onload', valid[i] );
        }
      }
    } ).catch( ( rejected ) => {
      for ( let i = 0; i < rejected; ++i ) {
        console.error( rejected[i] + " is not a function!" );
      }
    } );
  };

  /*
   *
   * !! TODO !!
   *
   * Basically just shitty jQuery.
   * @param {String} target - Takes a css selector or an array of css selectors.
   * @todo - Finish and make it actually work.
   */
  this.get = function ( target = ['document'] ) {
    let gotten = [];
    try {
      if ( Array.isArray( target ) ) {
        target.forEach( ( query ) => {
          let firstChar = String( query.charAt( 0 ) );
          if ( firstChar === '#' ) {
            query = query.replace( '#', '' );
            [].forEach.call( document.getElementById( query ), ( el ) => {
              gotten.push( el );
            } );
          } else if ( firstChar === '.' ) {
            query = query.replace( '.', '' );
            [].forEach.call( document.getElementsByClassName( query ), ( el ) => {
              console.log( 'is class' );
              gotten.push( el );
            } );
          } else {
            if ( query === 'document' || query === 'doc' ) {
              gotten.push( document.documentElement );
            } else if ( query === 'body' ) {
              gotten.push( document.body );
            } else {
              [].forEach.call( document.getElementsByTagName( query ), ( el ) => {
                gotten.push( el );
              } );
            }
          }
        } );
      }
    } catch ( e ) {
      console.error( 'DIFCOREUTILITIES.get() failed: ' + e );
    }
    return ( gotten.length > 0 ? gotten : null );
  };

  /*
   *
   * !! TODO !!
   *
   * Directly manipulate stylesheets instead on inline styles.
   * @todo - this needs to be modified to get existing stylesheets
   * @research - is this actually faster, better, harder, or stronger? use cases?
   */
  this.mutate = function ( element, rules ) {
    let styleElement = document.createElement( 'style' ),
      styleSheet;
    document.head.appendChild( styleElement );
    styleSheet = styleElement.sheet;
    for ( let i = 0, rl = rules.length; i < rl; i++ ) {
      let j = 1,
        rule = rules[i],
        selector = rules[i][0],
        propStr = '';
      // If the second argument of a rule is an array of arrays, correct our variables.
      if ( Object.prototype.toString.call( rule[1][0] ) === '[object Array]' ) {
        rule = rule[1];
        j = 0;
      }
      for ( let pl = rule.length; j < pl; j++ ) {
        let prop = rule[j];
        propStr += prop[0] + ':' + prop[1] + ( prop[2] ? ' !important' : '' ) + ';\n';
      }
      styleSheet.insertRule( selector + '{' + propStr + '}', styleSheet.cssRules.length );
    }
  };

  /*
   * Simple XHR GET that returns a promise to allow chaining and,
   * properly handle asynchronus behavior of XHR.
   * @param {String} source - The source URL.
   */
  this.pull = function ( source ) {
    return new Promise( ( resolve, reject ) => {
      let r = new XMLHttpRequest();
      r.responseType = "document";
      r.open( "get", source );
      r.onload = () => {
        if ( r.readyState === r.DONE && ( r.status >= 200 && r.status <= 300 ) ) {
          resolve( r.response );
        } else {
          reject( { status: this.status, statusText: r.statusText } );
        }
      };
      r.onerror = () => {
        reject( { status: this.status, statusText: r.statusText } );
      };
      r.send( null );
    } );
  };


  /*
   * Rounds a number to the specified decimal place obviously.
   * This doesn't support rounding whole numbers. Use Math.round() for that.
   * @param {float} rnum - The decimal number.
   * @param {int} rlength - The decimal places to round to.
   */
  this.round = function ( rnum, rlength ) {
    return Math.round( rnum * Math.pow( 10, rlength ) ) / Math.pow( 10, rlength );
  };

  this.getOpposite = function ( angle, adjacent ) {
    return adjacent * Math.tan( angle * Math.PI / 180 );
  };

  /*
   * Uses canvas.measureText to compute and return the width of the given text of given font in pixels.
   * @param {String} text - The text to be rendered.
   * @param {String} font  - The css font descriptor that text is to be rendered with (e.g. "bold 14px verdana").
   */
  this.getTextWidth = function ( text, font ) {
    let canvas = canvas || document.createElement( 'canvas' );
    let context = canvas.getContext( "2d" );
    context.font = font;
    let metrics = context.measureText( text );
    return metrics.width;
  };

  /*
   * Strips all the classes, except classes specified to be ignored,
   * from the element and all it's children if recursive is enabled.
   * @param {Element} el - The element to be stripped of classes.
   * @param {String Array} ignores - The prefixes of classes, or just classes, to be ignored.
   * @param {boolean} recursive - True to also strip classes from the element's children.
   */
  this.stripClasses = function ( el, ignores = [], recursive = true ) {
    if ( el.classList.length > 0 ) {
      [].forEach.call( el.classList, ( className ) => {
        className = String( className );
        if ( ignores.length > 0 ) {
          // where cn is the nth class of classList
          for ( let cn = 0; cn < el.classList.length; ++cn ) {
            let ignore = false;
            // where ig is the nth String of ignores
            for ( let ig = 0; ig < ignores.length; ++ig ) {
              if ( String( el.classList[cn] ).includes( ignores[ig] ) ) {
                ignore = true;
              }
            }
            if ( !ignore ) {
              el.classList.remove( className );
            }
          }
        } else {
          el.classList.remove( className );
        }
      } );
    }
    if ( el.hasChildNodes && recursive ) {
      [].forEach.call( el.children, ( child ) => {
        this.stripClasses( child, ignores, recursive );
      } );
    }
  };

  function scrollToElement ( target, duration, callback = null ) {
    let scrollUp = false,
      scrollTop = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0,
      targetTop = target.getBoundingClientRect().top + scrollTop,
      distance = ( -1 * parseInt( scrollTop - targetTop ) ),
      angle = 0,
      speed = 0.05;
    const offset = 0.5,
      baseAlpha = 0.5;
    let targetEl = target,
      targetYOffset = 0;
    while ( targetEl ) {
      targetYOffset += ( targetEl.offsetTop );
      targetEl = targetEl.offsetParent;
    }
    if ( scrollTop > targetTop ) {
      scrollUp = true;
    }
    let then = performance.now();
    render();
    function render ( now ) {
      let alpha;
      if ( scrollUp ) {
        alpha = self.round( ( baseAlpha + Math.sin( angle ) * offset ), 2 );
      } else {
        alpha = self.round( ( baseAlpha - Math.sin( angle ) * offset ), 2 );
      }
      console.log( scrollTop, targetTop, distance, targetYOffset );
      //window
      angle += speed;
      then = now;
      //requestAnimationFrame(render);
      if ( callback && typeof callback === 'function' ) {
        callback();
      }
    }
  }

  this.animate = function ( css, duration, smoothing ) {

    // parse css object

    // get starting css

    // generate linear frames

    // apply smoothing function

    // test for initial fps

    // map frames to duration and fps

    // begin rendering loop on element

    // {

    // check fps

    // maybe drop frames

    // apply css frame

    // }

    function test ( totalValue, totalTime ) {
      let output = [0];
      let value = 0;
      let fps = 1000 / 120;
      let startTime = performance.now();
      let now = startTime;
      let elapsedTime = 0;
      let then = 0;
      let deltaTime = 0;
      while ( value < totalValue ) {
        if ( elapsedTime >= totalTime * 1.5 ) {
          output.push( totalValue );
          break;
        }
        deltaTime = elapsedTime - then;
        if ( deltaTime >= fps ) {
          then = elapsedTime;
          value = cosMutation(
            value,
            totalValue,
            totalTime,
            elapsedTime
          );
          if ( value > totalValue - 0.33 ) {
            value = totalValue;
          }
          output.push( value );
        }
        now = performance.now();
        elapsedTime = now - startTime;
      }
      console.log( 'elapsed time: ' + elapsedTime, output );

      function cosMutation ( v, tv, t, et ) {
        return -( tv / 2 ) * Math.cos( ( v + et ) / ( t / Math.PI ) ) + ( tv / 2 );
      }
    }

    function cubicMutation ( x, b ) {
      return ( ( Math.cbrt( x - ( b / 2 ) ) * 13.5721 ) + ( b / 2 ) );
    }

    function linearTransform ( n, a, b ) {
      return ( n - a[0] ) * ( b[1] - b[0] ) / ( a[1] - a[0] ) + b[0];
    }

    let values = ( function ( x1, x2 ) {
      let arr = [];
      for ( let i = x1; i <= x2; ++i ) {
        arr.push( i );
      }
      return arr;
    } )( 0, 1000 );

    let newValues = values.map(
      ( x, i, a ) => {
        return linearTransform(
          cubicTransform( x ),
          [
            a[0],
            cubicTransform( a[a.length - 1] )
          ],
          [
            0,
            1
          ]
        );
      }
    );



  };



  /*
  * MD5 (Message-Digest Algorithm)
  */
  this.MD5 = function md5 ( string ) {
    function RotateLeft ( lValue, iShiftBits ) {
      return ( lValue << iShiftBits ) | ( lValue >>> ( 32 - iShiftBits ) );
    }
    function AddUnsigned ( lX, lY ) {
      let lX4, lY4, lX8, lY8, lResult;
      lX8 = ( lX & 0x80000000 );
      lY8 = ( lY & 0x80000000 );
      lX4 = ( lX & 0x40000000 );
      lY4 = ( lY & 0x40000000 );
      lResult = ( lX & 0x3FFFFFFF ) + ( lY & 0x3FFFFFFF );
      if ( lX4 & lY4 ) {
        return ( lResult ^ 0x80000000 ^ lX8 ^ lY8 );
      }
      if ( lX4 | lY4 ) {
        if ( lResult & 0x40000000 ) {
          return ( lResult ^ 0xC0000000 ^ lX8 ^ lY8 );
        }
        else {
          return ( lResult ^ 0x40000000 ^ lX8 ^ lY8 );
        }
      }
      else {
        return ( lResult ^ lX8 ^ lY8 );
      }
    }
    function F ( x, y, z ) {
      return ( x & y ) | ( ( ~x ) & z );
    }
    function G ( x, y, z ) {
      return ( x & z ) | ( y & ( ~z ) );
    }
    function H ( x, y, z ) {
      return ( x ^ y ^ z );
    }
    function I ( x, y, z ) {
      return ( y ^ ( x | ( ~z ) ) );
    }
    function FF ( a, b, c, d, x, s, ac ) {
      a = AddUnsigned( a, AddUnsigned( AddUnsigned( F( b, c, d ), x ), ac ) );
      return AddUnsigned( RotateLeft( a, s ), b );
    }
    function GG ( a, b, c, d, x, s, ac ) {
      a = AddUnsigned( a, AddUnsigned( AddUnsigned( G( b, c, d ), x ), ac ) );
      return AddUnsigned( RotateLeft( a, s ), b );
    }
    function HH ( a, b, c, d, x, s, ac ) {
      a = AddUnsigned( a, AddUnsigned( AddUnsigned( H( b, c, d ), x ), ac ) );
      return AddUnsigned( RotateLeft( a, s ), b );
    }
    function II ( a, b, c, d, x, s, ac ) {
      a = AddUnsigned( a, AddUnsigned( AddUnsigned( I( b, c, d ), x ), ac ) );
      return AddUnsigned( RotateLeft( a, s ), b );
    }
    function ConvertToWordArray ( string ) {
      let lWordCount;
      let lMessageLength = string.length;
      let lNumberOfWords_temp1 = lMessageLength + 8;
      let lNumberOfWords_temp2 = ( lNumberOfWords_temp1 - ( lNumberOfWords_temp1 % 64 ) ) / 64;
      let lNumberOfWords = ( lNumberOfWords_temp2 + 1 ) * 16;
      let lWordArray = Array( lNumberOfWords - 1 );
      let lBytePosition = 0;
      let lByteCount = 0;
      while ( lByteCount < lMessageLength ) {
        lWordCount = ( lByteCount - ( lByteCount % 4 ) ) / 4;
        lBytePosition = ( lByteCount % 4 ) * 8;
        lWordArray[lWordCount] = ( lWordArray[lWordCount] | ( string.charCodeAt( lByteCount ) << lBytePosition ) );
        lByteCount++;
      }
      lWordCount = ( lByteCount - ( lByteCount % 4 ) ) / 4;
      lBytePosition = ( lByteCount % 4 ) * 8;
      lWordArray[lWordCount] = lWordArray[lWordCount] | ( 0x80 << lBytePosition );
      lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
      lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
      return lWordArray;
    };
    function WordToHex ( lValue ) {
      let WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
      for ( lCount = 0; lCount <= 3; lCount++ ) {
        lByte = ( lValue >>> ( lCount * 8 ) ) & 255;
        WordToHexValue_temp = "0" + lByte.toString( 16 );
        WordToHexValue = WordToHexValue + WordToHexValue_temp.substr( WordToHexValue_temp.length - 2, 2 );
      }
      return WordToHexValue;
    };
    function Utf8Encode ( string ) {
      string = string.replace( /\r\n/g, "\n" );
      let utftext = "";
      for ( let n = 0; n < string.length; n++ ) {
        let c = string.charCodeAt( n );
        if ( c < 128 ) {
          utftext += String.fromCharCode( c );
        }
        else if ( ( c > 127 ) && ( c < 2048 ) ) {
          utftext += String.fromCharCode( ( c >> 6 ) | 192 );
          utftext += String.fromCharCode( ( c & 63 ) | 128 );
        }
        else {
          utftext += String.fromCharCode( ( c >> 12 ) | 224 );
          utftext += String.fromCharCode( ( ( c >> 6 ) & 63 ) | 128 );
          utftext += String.fromCharCode( ( c & 63 ) | 128 );
        }
      }
      return utftext;
    };
    let x = Array();
    let k, AA, BB, CC, DD, a, b, c, d;
    let S11 = 7, S12 = 12, S13 = 17, S14 = 22;
    let S21 = 5, S22 = 9, S23 = 14, S24 = 20;
    let S31 = 4, S32 = 11, S33 = 16, S34 = 23;
    let S41 = 6, S42 = 10, S43 = 15, S44 = 21;
    string = Utf8Encode( string );
    x = ConvertToWordArray( string );
    a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
    for ( k = 0; k < x.length; k += 16 ) {
      AA = a; BB = b; CC = c; DD = d;
      a = FF( a, b, c, d, x[k + 0], S11, 0xD76AA478 );
      d = FF( d, a, b, c, x[k + 1], S12, 0xE8C7B756 );
      c = FF( c, d, a, b, x[k + 2], S13, 0x242070DB );
      b = FF( b, c, d, a, x[k + 3], S14, 0xC1BDCEEE );
      a = FF( a, b, c, d, x[k + 4], S11, 0xF57C0FAF );
      d = FF( d, a, b, c, x[k + 5], S12, 0x4787C62A );
      c = FF( c, d, a, b, x[k + 6], S13, 0xA8304613 );
      b = FF( b, c, d, a, x[k + 7], S14, 0xFD469501 );
      a = FF( a, b, c, d, x[k + 8], S11, 0x698098D8 );
      d = FF( d, a, b, c, x[k + 9], S12, 0x8B44F7AF );
      c = FF( c, d, a, b, x[k + 10], S13, 0xFFFF5BB1 );
      b = FF( b, c, d, a, x[k + 11], S14, 0x895CD7BE );
      a = FF( a, b, c, d, x[k + 12], S11, 0x6B901122 );
      d = FF( d, a, b, c, x[k + 13], S12, 0xFD987193 );
      c = FF( c, d, a, b, x[k + 14], S13, 0xA679438E );
      b = FF( b, c, d, a, x[k + 15], S14, 0x49B40821 );
      a = GG( a, b, c, d, x[k + 1], S21, 0xF61E2562 );
      d = GG( d, a, b, c, x[k + 6], S22, 0xC040B340 );
      c = GG( c, d, a, b, x[k + 11], S23, 0x265E5A51 );
      b = GG( b, c, d, a, x[k + 0], S24, 0xE9B6C7AA );
      a = GG( a, b, c, d, x[k + 5], S21, 0xD62F105D );
      d = GG( d, a, b, c, x[k + 10], S22, 0x2441453 );
      c = GG( c, d, a, b, x[k + 15], S23, 0xD8A1E681 );
      b = GG( b, c, d, a, x[k + 4], S24, 0xE7D3FBC8 );
      a = GG( a, b, c, d, x[k + 9], S21, 0x21E1CDE6 );
      d = GG( d, a, b, c, x[k + 14], S22, 0xC33707D6 );
      c = GG( c, d, a, b, x[k + 3], S23, 0xF4D50D87 );
      b = GG( b, c, d, a, x[k + 8], S24, 0x455A14ED );
      a = GG( a, b, c, d, x[k + 13], S21, 0xA9E3E905 );
      d = GG( d, a, b, c, x[k + 2], S22, 0xFCEFA3F8 );
      c = GG( c, d, a, b, x[k + 7], S23, 0x676F02D9 );
      b = GG( b, c, d, a, x[k + 12], S24, 0x8D2A4C8A );
      a = HH( a, b, c, d, x[k + 5], S31, 0xFFFA3942 );
      d = HH( d, a, b, c, x[k + 8], S32, 0x8771F681 );
      c = HH( c, d, a, b, x[k + 11], S33, 0x6D9D6122 );
      b = HH( b, c, d, a, x[k + 14], S34, 0xFDE5380C );
      a = HH( a, b, c, d, x[k + 1], S31, 0xA4BEEA44 );
      d = HH( d, a, b, c, x[k + 4], S32, 0x4BDECFA9 );
      c = HH( c, d, a, b, x[k + 7], S33, 0xF6BB4B60 );
      b = HH( b, c, d, a, x[k + 10], S34, 0xBEBFBC70 );
      a = HH( a, b, c, d, x[k + 13], S31, 0x289B7EC6 );
      d = HH( d, a, b, c, x[k + 0], S32, 0xEAA127FA );
      c = HH( c, d, a, b, x[k + 3], S33, 0xD4EF3085 );
      b = HH( b, c, d, a, x[k + 6], S34, 0x4881D05 );
      a = HH( a, b, c, d, x[k + 9], S31, 0xD9D4D039 );
      d = HH( d, a, b, c, x[k + 12], S32, 0xE6DB99E5 );
      c = HH( c, d, a, b, x[k + 15], S33, 0x1FA27CF8 );
      b = HH( b, c, d, a, x[k + 2], S34, 0xC4AC5665 );
      a = II( a, b, c, d, x[k + 0], S41, 0xF4292244 );
      d = II( d, a, b, c, x[k + 7], S42, 0x432AFF97 );
      c = II( c, d, a, b, x[k + 14], S43, 0xAB9423A7 );
      b = II( b, c, d, a, x[k + 5], S44, 0xFC93A039 );
      a = II( a, b, c, d, x[k + 12], S41, 0x655B59C3 );
      d = II( d, a, b, c, x[k + 3], S42, 0x8F0CCC92 );
      c = II( c, d, a, b, x[k + 10], S43, 0xFFEFF47D );
      b = II( b, c, d, a, x[k + 1], S44, 0x85845DD1 );
      a = II( a, b, c, d, x[k + 8], S41, 0x6FA87E4F );
      d = II( d, a, b, c, x[k + 15], S42, 0xFE2CE6E0 );
      c = II( c, d, a, b, x[k + 6], S43, 0xA3014314 );
      b = II( b, c, d, a, x[k + 13], S44, 0x4E0811A1 );
      a = II( a, b, c, d, x[k + 4], S41, 0xF7537E82 );
      d = II( d, a, b, c, x[k + 11], S42, 0xBD3AF235 );
      c = II( c, d, a, b, x[k + 2], S43, 0x2AD7D2BB );
      b = II( b, c, d, a, x[k + 9], S44, 0xEB86D391 );
      a = AddUnsigned( a, AA );
      b = AddUnsigned( b, BB );
      c = AddUnsigned( c, CC );
      d = AddUnsigned( d, DD );
    }
    let temp = WordToHex( a ) + WordToHex( b ) + WordToHex( c ) + WordToHex( d );
    return temp.toLowerCase();
  };




  this.shellSort = function ( array ) {
    let length = array.length,
      h = 1;
    while ( h < length / 3 ) {
      h = 3 * h + 1;
    }
    while ( h > 0 ) {
      for ( let i = h; i < length; i++ ) {
        for ( let j = i; j > 0 && array[j] < array[j - h]; j -= h ) {
          self.swap( array, j, j - h );
        }
      }
      //decreasing h
      h = --h / 3;
    }
    return array;
  };

  /*
   * Simple JS animation for elements, needs a lot of work.
   * All the these animations should be roled into one object.
   */
  this.slide = function ( el, initialOffset, finalOffset, callback = null ) {
    let slideIn = false;
    if ( finalOffset > initialOffset ) {
      slideIn = true;
    }
    el.style.zIndex = 1000010;
    el.style.transform = 'translateX(' + ( parseFloat( initialOffset ) * -100 ) + '%)';
    let angle = 0,
      speed = 0.05,
      baseOffset = 0.5;
    render();
    function render () {
      let offset;
      if ( slideIn ) {
        offset = self.round( ( baseOffset + Math.sin( angle ) ), 2 );
        if ( offset >= finalOffset - 0.01 ) {
          offset = finalOffset;
        }
      } else {
        offset = self.round( ( baseOffset - Math.sin( angle ) ), 2 );
        if ( offset <= finalOffset + 0.01 ) {
          offset = finalOffset;
        }
      }
      el.style.transform = 'translateX(' + ( parseFloat( offset ) * -100 ) + '%)';
      angle += speed;
      if ( offset !== finalOffset ) {
        requestAnimationFrame( render );
      } else {
        if ( callback && typeof callback === 'function' ) {
          callback();
        }
      }
    }
  };

  /*
   * Simple JS animation for elements, needs a lot of work.
   * All the these animations should be roled into one object.
   */
  this.fade = function ( el, initialAlpha, finalAlpha, callback = null ) {
    let fadeIn = false;
    if ( finalAlpha > initialAlpha ) {
      fadeIn = true;
    }
    el.style.zIndex = 1000010;
    el.style.opacity = parseFloat( initialAlpha );
    let angle = 0,
      speed = 0.05,
      offset = 0.5,
      baseAlpha = 0.5;
    render();
    function render () {
      let alpha;
      if ( fadeIn ) {
        alpha = self.round( ( baseAlpha + Math.sin( angle ) * offset ), 2 );
        if ( alpha >= finalAlpha - 0.01 ) {
          alpha = finalAlpha;
        }
      } else {
        alpha = self.round( ( baseAlpha - Math.sin( angle ) * offset ), 2 );
        if ( alpha <= finalAlpha + 0.01 ) {
          alpha = finalAlpha;
        }
      }
      el.style.opacity = alpha;
      angle += speed;
      if ( alpha !== finalAlpha ) {
        requestAnimationFrame( render );
      } else {
        if ( callback && typeof callback === 'function' ) {
          callback();
        }
      }
    }
  };

  /*
   * Sets a cookie.
   */
  this.setCookie = function ( cookieName, value, lifespan ) {
    let deathDate = new Date();
    deathDate.setDate( deathDate.getDate() + lifespan );
    let cookieValue = encodeURIComponent( value ) + ( ( lifespan === null ) ? '' : ( '; expires=' + deathDate.toUTCString() ) );
    document.cookie = cookieName + '=' + cookieValue;
  };
  /*
   * All your cookie are belong to us.
   */
  this.getCookie = function ( cookieName ) {
    let retrievedCookieName,
      retrievedCookieValue,
      cookies = document.cookie.split( ';' );
    for ( let i = 0; i < cookies.length; ++i ) {
      retrievedCookieName = cookies[i].substr( 0, cookies[i].indexOf( '=' ) );
      retrievedCookieValue = cookies[i].substr( cookies[i].indexOf( '=' ) + 1 );
      retrievedCookieName = retrievedCookieName.replace( /^\s+|\s+$/g, '' );
      if ( retrievedCookieName === cookieName ) {
        return decodeURIComponent( retrievedCookieValue );
      }
    }
  };

  /*
   * Announce loaded and ready.
   */
  this.announceReady = function () {
    window._MarketMentorsCoreReady = new CustomEvent( 'MarketMentorsCoreReady', { bubbles: true } );
    console.log( 'Market Mentors CoreUtilities > Ready' );
    window.dispatchEvent( window._MarketMentorsCoreReady );
    window.dispatchEvent( new Event( 'resize' ) );
  };

}