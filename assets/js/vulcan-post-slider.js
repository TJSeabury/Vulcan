/* 
* An html based slider.
* © Copyright 2017, Tyler Seabury, All Rights reserved.
* @author Tyler Seabury, tylerseabury@gmail.com
* @authorURI https://github.com/TJSeabury/
* @version 1.4
*/
class VulcanPostSlider
{
    /* 
    * @param {HTML Element} container - An html element to inject the slider into.
    * @param {Object Array} content - The images and html content.
    * @param {Integer} interval - Time between slide transitions in miliseconds.
    * @param {Integer} speed - Slide transition time in miliseconds.
    */
    constructor( container, content, interval, speed )
    {
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
        this.preload = [];

        if ( !this.container.classList.contains('VulcanPostSlider') )
        {
            this.container.classList.add('VulcanPostSlider');
        }

        this.view = ( 
			() =>
			{
				let v = document.createElement('div');
				v.classList.add('view');
				return v;
			}
        )();
		
		this.overlay = ( 
			() =>
			{
				let o = document.createElement('div');
				let shape = document.createElement('figure');
				shape.classList.add('triangle', 'right', 'cyan');
				o.appendChild(shape);
				o.classList.add('overlay');
				return o;
			}
        )();
        
        this.slides = ( 
			() =>
			{
				let sa = [];
				for ( const slide of Array.from( this.content.children ) )
				{
					console.log( slide );
					sa.push( slide );
				}
				return sa;
			}
        )();

        this.controls = ( 
			() =>
			{
				const c = container.getElementsByClassName('slide-controls')[0];
				c.classList.add('controls');
				function createControl( s, that )
				{
					let g = document.createElement( 'span' );
					g.classList.add( 'goto' );
					g.addEventListener( 
						'click', 
						function handleClick()
						{
							if ( ! that.shifting )
							{
								that.goToSlide( 
									that.activeSlideIndex,
									parseInt( s.getAttribute('data-dhs-index') )
								);
								that.resetInterval();
							}
						}
					);
					return g;
				}
				for( const slide of this.slides )
				{
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
		this.setActiveControlStyles();
        this.container.appendChild( this.view );
		this.container.appendChild( this.overlay );
        this.container.appendChild( this.controls );
        this.loop = setInterval( 
			() =>
			{
				if ( !this.shifting )
				{
					this.advance();
				}
			}, 
			this.interval
		);
        this.view.style.transition = this.speed + 'ms ease all';
        return this;
    }
	
	setActiveControlStyles()
	{
		let ix = 0;
		for ( const control of this.controlsArray )
		{
			if ( control.classList.contains('active') )
			{
				control.classList.remove('active');
			}
			if ( this.activeSlideIndex === ix++ )
			{
				control.classList.add('active');
			}
		}
	}
	
    advance()
    {
        this.shifting = true;
        let nsi = ( this.activeSlideIndex === this.slides.length - 1 ) ? 0 : this.activeSlideIndex + 1;
        let cs = this.activeSlide;
        let ns = this.slides.slice( nsi, nsi + 1 )[0];
		ns.img.setAttribute(
			'style',
			'opacity:0;'+
			'transform:translateX(4%);'+
			'transition:all ' + this.speed + 'ms ease;'
		);
		ns.caption.setAttribute(
			'style',
			'opacity:0;'+
			'transform:translateY(4%);'+
			'transition:all ' + this.speed + 'ms ease;'
		);
        ns = this.view.appendChild( ns );
        setTimeout( 
			() =>
			{
				cs.img.setAttribute(
					'style',
					'opacity:0;'+
					'transform:translateX(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
				cs.caption.setAttribute(
					'style',
					'opacity:0;'+
					'transform:translateY(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
			}, 
			1
		);
		setTimeout( 
			() =>
			{
				ns.img.setAttribute(
					'style',
					'opacity:1;'+
					'transform:translateX(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
				ns.caption.setAttribute(
					'style',
					'opacity:1;'+
					'transform:translateY(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
			}, 
			this.speed
		);
        setTimeout( 
			() =>
			{
				this.view.removeChild(cs);
				this.activeSlide = ns;
				if ( this.activeSlideIndex < this.slides.length - 1 )
				{
					this.activeSlideIndex++;
				}
				else
				{
					this.activeSlideIndex = 0;
				}
				this.setActiveControlStyles();
				this.shifting = false;
			}, 
			this.speed
		);
    }

    retreat()
    {
        this.shifting = true;
        let psi = ( this.activeSlideIndex === 0 ) ? this.slides.length - 1 : this.activeSlideIndex - 1;
        let cs = this.activeSlide;
        let ns = this.slides.slice( psi, psi + 1 )[0];
		ns.img.setAttribute(
			'style',
			'opacity:0;'+
			'transform:translateX(4%);'+
			'transition:all ' + this.speed + 'ms ease;'
		);
		ns.caption.setAttribute(
			'style',
			'opacity:0;'+
			'transform:translateY(4%);'+
			'transition:all ' + this.speed + 'ms ease;'
		);
        ns = this.view.appendChild( ns );
        setTimeout( 
			() =>
			{
				cs.img.setAttribute(
					'style',
					'opacity:0;'+
					'transform:translateX(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
				cs.caption.setAttribute(
					'style',
					'opacity:0;'+
					'transform:translateY(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
			}, 
			1
		);
		setTimeout( 
			() =>
			{
				ns.img.setAttribute(
					'style',
					'opacity:1;'+
					'transform:translateX(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
				ns.caption.setAttribute(
					'style',
					'opacity:1;'+
					'transform:translateY(0%);'+
					'transition:all ' + this.speed + 'ms ease;'
				);
			}, 
			this.speed
		);
        setTimeout( 
			() =>
			{
				this.view.removeChild(cs);
				this.activeSlide = ns;
				if ( this.activeSlideIndex > 0 )
				{
					this.activeSlideIndex--;
				}
				else
				{
					this.activeSlideIndex = this.slides.length - 1;
				}
				this.setActiveControlStyles();
				this.shifting = false;
			}, 
			this.speed
		);
    }
	
	goToSlide( ixCurr, ixNext )
	{
		if ( ixNext === ixCurr )
		{
			return;
		}
		else if ( ixNext > ixCurr )
		{
			this.activeSlideIndex = --ixNext;
			this.advance();
		}
		else if ( ixNext < ixCurr )
		{
			this.activeSlideIndex = ++ixNext;
			this.retreat();
		}
	}

    resetInterval()
    {
        clearInterval( this.loop );
        this.loop = setInterval( 
			() =>
			{
				if ( !this.shifting )
				{
					this.advance();
				}
			}, 
			this.interval
		);
    }
    
}


let vulcanPostSlider = null;
window.addEventListener(
	'load',
	() =>
	{
		'use strict';
		const slider = document.getElementById('Vulcan_Post_Slider');
		vulcanPostSlider = new VulcanPostSlider( 
			slider,
			slider.getElementsByClassName('vulcan-slides-wrapper')[0],
			6900,
			666 
		);
	}
);