;(function($){
	function get_dimension(element, attribute) {
		var value = parseFloat(element.getAttribute(attribute));
		return isFinite(value) && value >= 0 ? value : null;
	}

	function apply_dimensions(context) {
		var $context = $(context);

		$context.find('.ufyt_main-video-placeholder, .uyoutube-player').each(function () {
			var width = get_dimension(this, 'data-width');
			var height = get_dimension(this, 'data-height');
			if (width !== null) $(this).css('width', width);
			if (height !== null) $(this).css('height', height);
		});

		$context.find('.uyoutube-gallery-item, .uyoutube-thumb-holder').each(function () {
			var width = get_dimension(this, 'data-thumb-width');
			if (width !== null) $(this).css('width', width);
		});

		$context.find('.uyoutube-thumb-mask').each(function () {
			var width = get_dimension(this, 'data-thumb-width');
			var height = get_dimension(this, 'data-thumb-height');
			if (width !== null) $(this).css('width', width);
			if (height !== null) $(this).css('height', height);
		});

		$context.find('.uyoutube-thumb').each(function () {
			var width = get_dimension(this, 'data-thumb-width');
			var offset = get_dimension(this, 'data-thumb-offset');
			if (width !== null) $(this).css('width', width);
			if (offset !== null) $(this).css('margin-top', -offset);
		});
	}

	/*
	 * Replace main video placeholder image+button with actual main video.
	 */
	function replace_ph_with_video(ph) {
		if (ph.length === 0) return;

		var video_id = ph.data('video-id') || 'none';
		var loop = ph.data('loop');
		var width = ph.data('width') || parseInt(ph.width(), 10);
		var height = ph.data('height') || parseInt(ph.height(), 10);

		if (loop) loop = '&' + loop;

		ph.after('<iframe src="https://www.youtube.com/embed/'+video_id+'?modestbranding=1'+loop+'&autoplay=true" width="'+width+'" height="'+height+'"></iframe>');
		ph.remove();
	}

	/**
	 * Take data from list item and load video in player.
	 * @param {Object} - jQuery object of selected list item
	 */
	function load_video_from_list(list_item) {
		var el = list_item.parents('.upfront-youtube-container');
		var video_id = list_item.data('video-id') || 'none';

		// Make sure iframe is present and initialized properly
		if (el.find('iframe').length === 0) {
			replace_ph_with_video(el.find('.ufyt_main-video-placeholder'));
		}

		// Setup ifram and title for selected video
		var videoUrl = 'https://www.youtube.com/embed/' + video_id + '?modestbranding=1';
		el.find('iframe').attr('src', videoUrl);
		el.find('.ufyt_main-video-title').html(list_item.find('h4').html());

		// Handle hidding active video thumbnail
		if(el.data('first-hidden')) {
			list_item.siblings().css('display', 'inline-block');
			list_item.hide();
		}
	}

	$('.uyoutube-gallery-item, .uyoutube-list-item').on('click', function(event) {
		load_video_from_list($(this));
	});

	$('.ufyt_main-video-placeholder').on('click', function(event) {
		replace_ph_with_video($(this));
	});

	apply_dimensions(document);
})(jQuery);
