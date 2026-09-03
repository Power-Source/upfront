var assert = require('assert'),
	JSDOM = require('jsdom').JSDOM;

describe('YouTube front end', function () {
	var $, dom;

	before(function () {
		dom = new JSDOM('<!doctype html><html><body>' +
			'<div class="upfront-youtube-container" id="safe">' +
				'<div class="uyoutube-consent-placeholder" data-embed-url="https://www.youtube-nocookie.com/embed/abc_123-XYZ?modestbranding=1">' +
					'<button class="uyoutube-consent-button">Load</button>' +
				'</div>' +
				'<img id="safe-thumbnail" data-youtube-src="https://img.youtube.com/vi/abc_123-XYZ/hqdefault.jpg">' +
				'<img id="unsafe-thumbnail" data-youtube-src="javascript:alert(1)">' +
			'</div>' +
			'<div class="upfront-youtube-container" id="unsafe">' +
				'<div class="uyoutube-consent-placeholder" data-embed-url="https://example.com/embed/abc_123-XYZ">' +
					'<button class="uyoutube-consent-button">Load</button>' +
				'</div>' +
			'</div>' +
			'<div class="upfront-youtube-container" id="gallery">' +
				'<iframe></iframe><h3 class="ufyt_main-video-title"></h3>' +
				'<div class="uyoutube-gallery-item" data-video-id="abc_123-XYZ"><h4><img src=x onerror=alert(1)>Visible title</h4></div>' +
			'</div>' +
			'</body></html>', {url: 'https://example.test/'});
		global.window = dom.window;
		global.document = dom.window.document;
		global.navigator = dom.window.navigator;
		delete require.cache[require.resolve('jquery')];
		$ = global.jQuery = require('jquery');
		require('../../elements/upfront-youtube/js/uyoutube-front.js');
	});

	after(function () {
		dom.window.close();
		delete global.window;
		delete global.document;
		delete global.navigator;
		delete global.jQuery;
	});

	it('loads only approved HTTPS embed and thumbnail URLs', function () {
		$('#safe .uyoutube-consent-button').trigger('click');

		assert.strictEqual($('#safe iframe').attr('src'), 'https://www.youtube-nocookie.com/embed/abc_123-XYZ?modestbranding=1');
		assert.strictEqual($('#safe-thumbnail').attr('src'), 'https://img.youtube.com/vi/abc_123-XYZ/hqdefault.jpg');
		assert.strictEqual($('#unsafe-thumbnail').attr('src'), undefined);
	});

	it('rejects an embed URL on an unapproved host', function () {
		$('#unsafe .uyoutube-consent-button').trigger('click');

		assert.strictEqual($('#unsafe iframe').length, 0);
		assert.strictEqual($('#unsafe .uyoutube-consent-placeholder').length, 1);
	});

	it('copies video titles as text instead of HTML', function () {
		$('#gallery .uyoutube-gallery-item').trigger('click');

		assert.strictEqual($('#gallery .ufyt_main-video-title').text(), 'Visible title');
		assert.strictEqual($('#gallery .ufyt_main-video-title img').length, 0);
	});
});