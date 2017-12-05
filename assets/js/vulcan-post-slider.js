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
    constructor ( container, content, interval, speed )
    {
        this.container = container;
        this.content = content;
		this.controls = null;
        this.interval = interval;
        this.speed = speed;
        this.shifting = false;
        this.loop = null;
        this.slides = null;
        this.sidx = 0;

        if ( !this.container.classList.contains('VulcanPostSlider') )
        {
            this.container.classList.add('VulcanPostSlider');
        }
        
        this.slides = ( 
			() =>
			{
				let sa = [];
				for ( let slide of Array.from( this.content.children ) )
				{
					sa.push( slide );
				}
				return sa;
			}
        )();
		
		/*
		* init slide styles
		*/
		this.slides.forEach(
			e =>
			{
				if ( '0' === e.getAttribute( 'data-slide-index' ) )
				{
					e.classList.add('active');
				}
				e.style.setProperty( 'transition', 'all ' + this.speed + 'ms ease-out' );
				e.style.setProperty( 'animation-duration', this.speed + 'ms' );
			}
		);

        this.controls = ( 
			() =>
			{
				let ca = [];
				for ( const control of Array.from( this.container.getElementsByClassName('slide-controls')[0].children ) )
				{
					control.addEventListener(
						'click',
						() =>
						{
							this.goToSlide(	parseInt( control.getAttribute('data-slide-index') ) );
						}
					);
					ca.push( control );
				}
				return ca;
			}
        )();
		
		this.setActiveControlStyles();
		
        this.loop = setInterval( 
			() =>
			{
				if ( ! this.shifting )
				{
					this.advance();
				}
			}, 
			this.interval
		);
		
        return this;
    }
	
	setActiveControlStyles()
	{
		let idx = 0;
		for ( const control of this.controls )
		{
			if ( control.classList.contains('active') )
			{
				control.classList.remove('active');
			}
			if ( idx === this.sidx )
			{
				control.classList.add('active');
			}
			++idx;
		}
	}
	
    advance()
    {
		if ( true === this.shifting )
		{
			return;
		}
        this.shifting = true;
        let newSlideIndex = this.getNextIndex();
        let currentSlide = this.slides[this.sidx];
        let newSlide = this.slides[newSlideIndex];
		this.doAnimation(
			currentSlide,
			newSlide,
			() =>
			{
				this.updateIndex();
				this.setActiveControlStyles();
				this.shifting = false;
				this.resetInterval();
			}
		);
    }

    retreat()
    {
		if ( true === this.shifting )
		{
			return;
		}
        this.shifting = true;
        let previousSlideIndex = this.getNextIndex( true );
        let currentSlide = this.slides[this.sidx];
        let newSlide = this.slides[previousSlideIndex];
        this.doAnimation(
			currentSlide,
			newSlide,
			() =>
			{
				this.updateIndex( true );
				this.setActiveControlStyles();
				this.shifting = false;
				this.resetInterval();
			}
		);
    }
	
	goToSlide( ixNext )
	{
		if ( ixNext === this.sidx )
		{
			return;
		}
		if ( true === this.shifting )
		{
			return;
		}
		this.shifting = true;
		let currentSlide = this.slides[this.sidx];
		let newSlide = this.slides[ixNext];
		this.doAnimation(
			currentSlide,
			newSlide,
			() =>
			{
				this.sidx = ixNext;
				this.setActiveControlStyles();
				this.shifting = false;
				this.resetInterval();
			}
		);
	}
	
	doAnimation( cs, ns, callback )
	{
		ns.classList.add('slide-fade-in');
		setTimeout(
			() =>
			{
				ns.classList.add('active');
				cs.classList.remove('active');
				ns.classList.remove('slide-fade-in');
			},
			this.speed - 1
		);
		setTimeout(
			() =>
			{
				if ( 'function' === typeof callback )
				{
					callback();
				}
			},
			this.speed
		);
	}
	
	getNextIndex ( reverse = false )
	{
		if ( false === reverse )
		{
			if ( this.slides.length - 1 === this.sidx )
			{
				return 0;
			}
			else
			{
				return this.sidx + 1;
			}
		}
		else
		{
			if ( 0 === this.sidx )
			{
				 return this.slides.length - 1;
			}
			else
			{
				 return this.sidx - 1;
			}
		}
	}
	
	updateIndex ( reverse = false )
	{
		if ( false === reverse )
		{
			if ( this.slides.length - 1 > this.sidx )
			{
				this.sidx++;
			}
			else
			{
				this.sidx = 0;
			}
		}
		else
		{
			if ( 0 < this.sidx )
			{
				this.sidx--;
			}
			else
			{
				this.sidx = this.slides.length - 1;
			}
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
		let container = (
			() =>
			{
				let c = slider;
				while ( ! c.classList.contains('mk-page-section-wrapper') || c === document.body )
				{
					c = c.parentNode;
				}
				return c;
			}
		)();
		container.style.setProperty( 'z-index', '1001' );
		
	}
);














