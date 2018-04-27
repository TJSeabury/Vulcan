/**
 * Vulcan Slider
 */
($=>{'use strict';window.addEventListener('load',()=>{
	 
	 const box = document.querySelector('.fl-lightbox-content');

	 let observer = new MutationObserver(
		 mutations =>
		 {
			 for ( let m = 0; m < mutations.length; ++m )
			 {
				 if ( mutations[m].type === 'childList' )
				 {
					 $( '.vulcan-range input[type="range"]' ).on(
						 'input',
						 function(e)
						 {
							 const that = $(this);
							 const $output = that.parent().find('output');
							 $output.text( that.val() );
						 }
					 );
				 }
			 }
		 }
	 );
	 observer.observe(
		 box,
		 {
			 attributes: true,
			 childList: true,
			 characterData: false,
			 subtree: false
		 }
	 );
	
});})(jQuery);

