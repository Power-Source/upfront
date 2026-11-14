jQuery(function($){
	// Update "preload" attribute to make videos show up and autoplay videos that are
	// set to autoplay.
	$('.uinsert-video-insert video').attr('preload', 'auto').each( function() {
		if ($(this).attr('data-autoplay-video') === "true") {
			this.muted = true;
			$(this).attr('muted', '').attr('playsinline', '');
			$(this).attr('autoplay', 'true');
		}
	});
});
