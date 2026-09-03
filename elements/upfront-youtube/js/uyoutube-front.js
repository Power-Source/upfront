;(function($){
	function get_safe_youtube_url(value, hostname, path_pattern) {
		var url;
		try {
			url = new URL(value);
		} catch (error) {
			return null;
		}
		if (url.protocol !== 'https:' || url.hostname !== hostname || url.port || url.username || url.password) return null;
		return path_pattern.test(url.pathname) ? url.href : null;
	}

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

	function load_deferred_thumbnails(context) {
		$(context).find('img[data-youtube-src]').each(function () {
			var thumbnail_url = get_safe_youtube_url(
				this.getAttribute('data-youtube-src'),
				'img.youtube.com',
				/^\/vi\/[A-Za-z0-9_-]+\/(?:default|hqdefault|mqdefault|sddefault|maxresdefault)\.jpg$/
			);
			if (thumbnail_url) this.src = thumbnail_url;
			this.removeAttribute('data-youtube-src');
		});
	}

	function prepare_iframe(iframe, data) {
		iframe.width = data.width || '100%';
		iframe.height = data.height || 400;
		iframe.title = data.title || 'YouTube video player';
		iframe.setAttribute('frameborder', '0');
		iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
		iframe.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture' + (data.fullscreen ? '; fullscreen' : ''));
		if (data.fullscreen) iframe.setAttribute('allowfullscreen', 'allowfullscreen');
		return iframe;
	}

	function get_placeholder_data(ph) {
		return {
			width: ph.data('width') || parseInt(ph.width(), 10),
			height: ph.data('height') || parseInt(ph.height(), 10),
			title: ph.data('title') || ph.find('img').attr('title'),
			fullscreen: ph.attr('data-fullscreen') === 'true'
		};
	}

	/*
	 * Replace main video placeholder image+button with actual main video.
	 */
	function replace_ph_with_video(ph) {
		if (ph.length === 0) return;

		var video_id = ph.data('video-id') || 'none';
		var loop = ph.data('loop');
		var iframe = prepare_iframe(document.createElement('iframe'), get_placeholder_data(ph));

		if (loop) loop = '&' + loop;

		iframe.src = 'https://www.youtube-nocookie.com/embed/' + video_id + '?modestbranding=1' + loop + '&autoplay=1';
		ph.after(iframe);
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
		var videoUrl = 'https://www.youtube-nocookie.com/embed/' + video_id + '?modestbranding=1';
		el.find('iframe').attr('src', videoUrl);
		el.find('.ufyt_main-video-title').text(list_item.find('h4').text());

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

	$(document).on('click', '.uyoutube-consent-button', function(event) {
		event.preventDefault();
		var ph = $(this).closest('.uyoutube-consent-placeholder');
		var container = ph.closest('.upfront-youtube-container');
		var iframe = prepare_iframe(document.createElement('iframe'), get_placeholder_data(ph));
		var embed_url = get_safe_youtube_url(
			ph.attr('data-embed-url'),
			'www.youtube-nocookie.com',
			/^\/embed\/[A-Za-z0-9_-]+$/
		);
		if (!embed_url) return;
		iframe.src = embed_url;
		document.cookie = 'upfront_youtube_consent=1; Max-Age=31536000; Path=/; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');
		ph.replaceWith(iframe);
		load_deferred_thumbnails(container);
	});

	document.addEventListener('click', function(event) {
		var button = event.target.closest ? event.target.closest('.psdsgvo-embed-consent-btn') : null;
		if (!button) return;
		var container = $(button).closest('.upfront-youtube-container');
		if (!container.length) return;
		window.setTimeout(function () {
			var iframe = container.find('.uyoutube-main-video iframe').get(0);
			if (iframe) prepare_iframe(iframe, {
				width: container.find('.uyoutube-main-video').width(),
				height: container.find('.uyoutube-main-video').height(),
				title: container.find('.ufyt_main-video-title').text(),
				fullscreen: true
			});
			load_deferred_thumbnails(container);
		}, 0);
	}, true);

	apply_dimensions(document);
})(jQuery);
