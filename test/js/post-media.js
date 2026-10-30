var assert = require('assert'),
	fs = require('fs'),
	path = require('path'),
	vm = require('vm'),
	JSDOM = require('jsdom').JSDOM;

describe('Responsive post media', function () {
	var css = fs.readFileSync(path.join(__dirname, '../../styles/global.css'), 'utf8');

	['upostdata-part content', 'uposts-part content', 'post_content'].forEach(function (className) {
		it('constrains images and caption wrappers in ' + className, function () {
			var dom = new JSDOM('<!doctype html><html class="uf-responsive"><head><style>' + css +
				'</style></head><body><div class="' + className + '">' +
				'<div class="wp-caption" style="width:1200px"><img width="1200" height="800"></div>' +
				'</div></body></html>'),
				imageStyle = dom.window.getComputedStyle(dom.window.document.querySelector('img')),
				captionStyle = dom.window.getComputedStyle(dom.window.document.querySelector('.wp-caption'));

			assert.strictEqual(imageStyle.maxWidth, '100%');
			assert.strictEqual(imageStyle.height, 'auto');
			assert.strictEqual(captionStyle.maxWidth, '100%');
			dom.window.close();
		});
	});

	it('releases external inserted images from their desktop crop on mobile', function () {
		var dom = new JSDOM('<!doctype html><html class="uf-responsive"><head><style>' + css +
			'</style></head><body><div class="upfront-inserted_image-wrapper">' +
			'<div class="ueditor-insert-variant-group"><div class="uinsert-image-wrapper uinsert-image-external">' +
			'<img></div></div></div></body></html>'),
			imageStyle = dom.window.getComputedStyle(dom.window.document.querySelector('img')),
			groupStyle = dom.window.getComputedStyle(dom.window.document.querySelector('.ueditor-insert-variant-group'));

		assert.strictEqual(imageStyle.position, 'static');
		assert.strictEqual(imageStyle.maxWidth, '100%');
		assert.strictEqual(imageStyle.height, 'auto');
		assert.strictEqual(groupStyle.maxWidth, '100%');
		assert.strictEqual(groupStyle.minHeight, '0px');
		dom.window.close();
	});

	it('preserves desktop external-image cropping', function () {
		var dom = new JSDOM('<!doctype html><html><head><style>' + css +
			'</style></head><body><div class="uinsert-image-external"><img></div></body></html>'),
			imageStyle = dom.window.getComputedStyle(dom.window.document.querySelector('img'));

		assert.strictEqual(imageStyle.position, 'absolute');
		assert.strictEqual(imageStyle.maxWidth, 'none');
		dom.window.close();
	});
});

describe('Post content CSS selectors', function () {
	it('offers the complete post container separately from its title and heading', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../elements/upfront-post-data/js/post-data.js'), 'utf8'),
			l10nSource = fs.readFileSync(path.join(__dirname, '../../elements/upfront-post-data/lib/class_upfront_post_data_l10n_server.php'), 'utf8'),
			labels = {},
			start = source.indexOf('cssSelectors: {'),
			end = source.indexOf('\n\t\t\t},', start),
			selectors;

		l10nSource.replace(/'([^']+)' => __\('([^']*)', 'upfront'\)/g, function (match, key, label) {
			labels[key] = label;
			return match;
		});
		selectors = vm.runInNewContext('({' + source.slice(start, end) + '}}).cssSelectors', {l10n: {css: labels}});

		assert.strictEqual(selectors['.upost-data-object'].label, 'Gesamt-Beitrags-Wrapper');
		assert.strictEqual(selectors['.title'].label, 'Titel-Wrapper');
		assert.strictEqual(selectors['.title h1'].label, 'Titel-Überschrift');
		assert.strictEqual(Object.keys(selectors).filter(function (selector) {
			return selectors[selector].label === 'Titel-Wrapper';
		}).length, 1);
	});
});

describe('Slider image sizing', function () {
	it('does not force cropped images to fill the slider', function () {
		var template = fs.readFileSync(path.join(__dirname, '../../elements/upfront-slider/tpl/uslider.html'), 'utf8'),
			css = fs.readFileSync(path.join(__dirname, '../../elements/upfront-slider/css/uslider.css'), 'utf8'),
			imageTag = template.match(/<img[^>]*\?>[^>]*>/)[0],
			dom = new JSDOM('<!doctype html><head><style>' + css + '</style></head>' +
				'<body><div class="uslide-image"><img></div></body>'),
			imageStyle = dom.window.getComputedStyle(dom.window.document.querySelector('img'));

		assert.ok(!/min-width| min-height/.test(imageTag));
		assert.strictEqual(imageStyle.width, 'auto');
		assert.strictEqual(imageStyle.height, 'auto');
		assert.strictEqual(imageStyle.maxWidth, '100%');
		assert.strictEqual(imageStyle.minWidth, '0px');
		assert.strictEqual(imageStyle.minHeight, '0px');
		dom.window.close();
	});

	it('keeps both crop offsets when applying an image edit', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../elements/upfront-slider/js/uslider.js'), 'utf8'),
			start = source.indexOf('\n\timageEditMask: function(e) {'),
			end = source.indexOf('\n\tgetEditorOptions:', start),
			result = {
				src: 'cropped.png', srcFull: 'original.png', imageId: 7, rotation: 0,
				imageSize: {width: 900, height: 600},
				cropSize: {width: 400, height: 300},
				imageOffset: {left: 140, top: 50}
			},
			slide = {
				id: 7,
				get: function () { return 'ok'; },
				set: function (data) { this.saved = data; }
			},
			view = {
				imageProps: {},
				model: {slideCollection: {
					at: function () { return slide; },
					get: function () { return slide; }
				}},
				getCurrentSlide: function () { return 0; },
				getEditorOptions: function () { return {}; },
				render: function () { this.rendered = true; }
			},
			context = {
				$: function () { return {closest: function () { return {}; }}; },
				Upfront: {Views: {Editor: {ImageEditor: {
					open: function () {
						return {
							done: function (callback) { callback(result); return this; },
							fail: function () { return this; }
						};
					}
				}}}}
			},
			edit = vm.runInNewContext('({' + source.slice(start, end) + '}).imageEditMask', context);

		edit.call(view, {target: {}, preventDefault: function () {}});
		assert.strictEqual(view.imageProps[7].cropOffset.left, 140);
		assert.strictEqual(view.imageProps[7].cropOffset.top, 50);
		assert.strictEqual(slide.saved.cropOffset.left, 140);
		assert.strictEqual(view.rendered, true);
	});
});
