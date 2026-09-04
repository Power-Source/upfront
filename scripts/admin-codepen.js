(function($) {
	'use strict';

	var data = window.Upfront_CodePen || {};
	var colors = data.themeColors && data.themeColors.colors ? data.themeColors.colors : [];
	var fonts = Array.isArray(data.themeFonts) ? data.themeFonts : [];
	var typography = data.typography || {};

	function escapeHtml(value) {
		return String(value).replace(/[&<>"']/g, function(character) {
			return {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			}[character];
		});
	}

	function cssValue(value) {
		return String(value).replace(/[\r\n;{}]/g, '');
	}

	function cssStringValue(value) {
		return cssValue(value).replace(/[\\"]/g, function(character) {
			return '\\' + character;
		});
	}

	function colorValue(value) {
		var color = cssValue(value);
		return color.indexOf('#ufc') === 0 ? '$' + color.substring(1) : color;
	}

	function buildHead() {
		var links = [];
		fonts.forEach(function(font) {
			var family = font && font.font ? font.font.family : '';
			var variant = font ? font.variant : '';
			if (!family) return;
			links.push('<link href="' + Upfront_CodePen.googleFontsStylesheetUrl + (Upfront_CodePen.googleFontsStylesheetUrl.indexOf('?') === -1 ? '?' : '&') + 'family=' + encodeURIComponent(family + (variant ? ':' + variant : '')) + '" rel="stylesheet">');
		});
		return links.join('\n');
	}

	function buildTypographyScss() {
		var output = '';
		var selectors = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'a', 'a:hover', 'ul', 'ol', 'blockquote', 'blockquote.upfront-quote-alternative'];
		selectors.forEach(function(selector) {
			var style = typography[selector];
			if (!style) return;
			output += selector + ' {\n';
			if (style.color) output += '\tcolor: ' + colorValue(style.color) + ';\n';
			if (style.font_face) output += '\tfont-family: "' + cssStringValue(style.font_face) + '", ' + cssValue(style.font_family || 'sans-serif') + ';\n';
			if (style.line_height) output += '\tline-height: ' + cssValue(style.line_height) + 'em;\n';
			if (style.size) output += '\tfont-size: ' + cssValue(style.size) + 'px;\n';
			if (style.style) output += '\tfont-style: ' + cssValue(style.style) + ';\n';
			if (style.weight) output += '\tfont-weight: ' + cssValue(style.weight) + ';\n';
			output += '}\n\n';
		});
		return output;
	}

	function buildScss() {
		var output = '';
		colors.forEach(function(item, index) {
			if (item && item.color) output += '$ufc' + index + ': ' + cssValue(item.color) + ';\n';
		});
		output += '\n' + buildTypographyScss();
		output += '.container { padding: 15px; }\n';
		output += '.typography { max-width: 700px; }\n';
		output += '.theme-colors { display: flex; gap: 15px; margin: 0; padding: 0; list-style: none; text-align: center; }\n';
		output += '.theme-colors li { flex: 1; overflow: hidden; padding: 0 0 15px; border: 1px solid #eee; border-radius: 4px; }\n';
		output += '.theme-colors span { display: block; width: 100%; height: 60px; margin-bottom: 15px; }\n';
		colors.forEach(function(item, index) {
			if (item && item.color) output += '.theme-colors .ufc' + index + ' span { background-color: $ufc' + index + '; }\n';
		});
		return output;
	}

	function buildHtml() {
		var output = '<div class="container">\n\t<ul class="theme-colors">\n';
		colors.forEach(function(item, index) {
			if (!item || !item.color) return;
			output += '\t\t<li class="ufc' + index + '"><span></span><p>#ufc' + index + '</p><p>' + escapeHtml(item.color) + '</p></li>\n';
		});
		output += '\t</ul>\n\t<div class="typography">\n';
		['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function(tag, index) {
			output += '\t\t<' + tag + '>' + escapeHtml(data.messages.sampleHeading) + ' ' + (index + 1) + '</' + tag + '>\n';
		});
		output += '\t\t<a href="#">' + escapeHtml(data.messages.sampleLink) + '</a>\n\t\t<p>' + escapeHtml(data.messages.sampleText) + '</p>\n';
		output += '\t\t<ul><li>' + escapeHtml(data.messages.listItem) + '</li><li>' + escapeHtml(data.messages.listItem) + '</li></ul>\n';
		output += '\t\t<ol><li>' + escapeHtml(data.messages.listItem) + '</li><li>' + escapeHtml(data.messages.listItem) + '</li></ol>\n';
		output += '\t\t<blockquote>' + escapeHtml(data.messages.quote) + '</blockquote>\n';
		output += '\t\t<blockquote class="upfront-quote-alternative">' + escapeHtml(data.messages.alternativeQuote) + '</blockquote>\n';
		output += '\t</div>\n</div>\n';
		return output;
	}

	function buildImportData() {
		return {
			schema: data.schema || 'upfront-theme-style',
			version: data.schemaVersion || 1,
			name: data.themeName || 'Upfront Theme Style',
			colors: data.themeColors || { colors: [], range: 0 },
			fonts: fonts,
			typography: typography
		};
	}

	function buildImportSource() {
		var payload = JSON.stringify(buildImportData());
		return '/* UPFRONT_THEME_STYLE\n' + payload + '\nEND_UPFRONT_THEME_STYLE */\n' +
			'window.top.postMessage({type:"upfront-theme-style",payload:' + payload + '}, "*");';
	}

	function showStatus(message, type) {
		var status = $('#upfront-codepen-status');
		status.removeClass('notice-info notice-success notice-error')
			.addClass('notice-' + type)
			.prop('hidden', false)
			.find('p').text(message);
	}

	function responseMessage(xhr) {
		return xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ?
			xhr.responseJSON.data.message : data.messages.requestFailed;
	}

	function responseData(response) {
		if (!response || true !== response.success) {
			return {
				error: response && response.data && response.data.message ?
					response.data.message : data.messages.requestFailed
			};
		}

		var result = response.data;
		if (result && result.data && !result.payload) result = result.data;
		if (!result || !result.payload || !result.summary) {
			return {error: data.messages.requestFailed};
		}

		return {result: result};
	}

	function embedUrl(url) {
		var match = String(url).match(/^https:\/\/codepen\.io\/(?:editor\/)?([A-Za-z0-9_-]+)\/(?:pen|details|full)\/([A-Za-z0-9-]+)/i);
		return match ? 'https://codepen.io/' + encodeURIComponent(match[1]) + '/fullembedgrid/' + encodeURIComponent(match[2]) + '?animations=run&type=embed' : '';
	}

	function renderStyleValues(selector, payload) {
		var container = $(selector).empty();
		var colorItems = payload.colors && Array.isArray(payload.colors.colors) ? payload.colors.colors : [];
		$('<h5></h5>').text(data.messages.colors).appendTo(container);
		var swatches = $('<div class="upfront-codepen-swatches"></div>').appendTo(container);
		colorItems.forEach(function(item, index) {
			if (!item || !item.color) return;
			var swatch = $('<div class="upfront-codepen-swatch"></div>').appendTo(swatches);
			$('<span></span>').css('background-color', item.color).appendTo(swatch);
			$('<code></code>').text('#ufc' + index + ' ' + item.color).appendTo(swatch);
		});
		if (!colorItems.length) $('<p></p>').text(data.messages.noColors).appendTo(swatches);

		$('<h5></h5>').text(data.messages.fonts).appendTo(container);
		var fontNames = [];
		(payload.fonts || []).forEach(function(font) {
			var family = font && font.font ? font.font.family : '';
			if (family && -1 === fontNames.indexOf(family)) fontNames.push(family);
		});
		$('<p></p>').text(fontNames.length ? fontNames.join(', ') : data.messages.noFonts).appendTo(container);

		$('<h5></h5>').text(data.messages.typography).appendTo(container);
		var table = $('<table class="upfront-codepen-values"><thead><tr></tr></thead><tbody></tbody></table>').appendTo(container);
		[data.messages.element, data.messages.font, data.messages.size, data.messages.color].forEach(function(label) {
			$('<th></th>').text(label).appendTo(table.find('thead tr'));
		});
		var body = table.find('tbody');
		Object.keys(payload.typography || {}).forEach(function(element) {
			var rule = payload.typography[element] || {};
			var row = $('<tr></tr>').appendTo(body);
			$('<th scope="row"></th>').text(element).appendTo(row);
			$('<td></td>').text(rule.font_face || rule.font_family || '-').appendTo(row);
			$('<td></td>').text(rule.size ? rule.size + ' px' : '-').appendTo(row);
			$('<td></td>').text(rule.color || '-').appendTo(row);
		});
		if (!body.children().length) $('<tr><td colspan="4"></td></tr>').find('td').text(data.messages.noTypography).end().appendTo(body);
	}

	function renderStylePreview(payload) {
		var palette = payload.colors && Array.isArray(payload.colors.colors) ? payload.colors.colors : [];
		var preview = $('<div class="upfront-codepen-style-preview"></div>');
		var colorList = $('<ul class="upfront-codepen-preview-colors"></ul>').appendTo(preview);
		palette.forEach(function(item, index) {
			if (!item || !item.color) return;
			var color = cssValue(item.color);
			var entry = $('<li></li>').appendTo(colorList);
			$('<span></span>').css('background-color', color).appendTo(entry);
			$('<code></code>').text('#ufc' + index + ' ' + color).appendTo(entry);
		});

		var typographyPreview = $('<div class="upfront-codepen-preview-typography"></div>').appendTo(preview);
		function addSample(tag, selector, text) {
			var rule = payload.typography && payload.typography[selector] ? payload.typography[selector] : {};
			var colorMatch = String(rule.color || '').match(/^#ufc(\d+)$/);
			var color = colorMatch && palette[colorMatch[1]] ? palette[colorMatch[1]].color : rule.color;
			var sample = $('<' + tag + '></' + tag + '>').text(text).appendTo(typographyPreview);
			if (color) sample.css('color', cssValue(color));
			if (rule.font_face) sample.css('font-family', cssValue(rule.font_face) + ', ' + cssValue(rule.font_family || 'sans-serif'));
			if (rule.line_height) sample.css('line-height', cssValue(rule.line_height));
			if (rule.size) sample.css('font-size', cssValue(rule.size) + 'px');
			if (rule.style) sample.css('font-style', cssValue(rule.style));
			if (rule.weight) sample.css('font-weight', cssValue(rule.weight));
			return sample;
		}

		['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function(tag, index) {
			addSample(tag, tag, data.messages.sampleHeading + ' ' + (index + 1));
		});
		addSample('a', 'a', data.messages.sampleLink).attr('href', '#');
		addSample('p', 'p', data.messages.sampleText);
		addSample('blockquote', 'blockquote', data.messages.quote);
		addSample('blockquote', 'blockquote.upfront-quote-alternative', data.messages.alternativeQuote).addClass('upfront-quote-alternative');
		$('#upfront-codepen-render').empty().append(preview);
	}

	var receivedPayload = null;
	var pendingPreview = null;
	renderStyleValues('#upfront-codepen-current-values', buildImportData());

	function requestPreview(params, button, waitForIframe) {
		params.action = 'upfront_codepen_preview';
		params.nonce = data.nonce;
		return $.post(data.ajaxUrl, params).done(function(response) {
			var normalized = responseData(response);
			if (normalized.error) {
				if (waitForIframe && pendingPreview) {
					pendingPreview.error = normalized.error;
					return;
				}
				showStatus(normalized.error, 'error');
				return;
			}
			if (pendingPreview) {
				window.clearTimeout(pendingPreview.timeout);
				pendingPreview = null;
			}
			var result = normalized.result;
			receivedPayload = result.payload;
			$('#upfront-codepen-preview-title').text(result.payload.name);
			$('#upfront-codepen-preview-summary').text(
				result.summary.colors + ' ' + data.messages.colors + ', ' +
				result.summary.fonts + ' ' + data.messages.fonts + ', ' +
				result.summary.typography + ' ' + data.messages.typography
			);
			renderStyleValues('#upfront-codepen-import-values', result.payload);
			renderStylePreview(result.payload);
			$('#upfront-codepen-preview').prop('hidden', false);
			$('#upfront-codepen-status').prop('hidden', true);
		}).fail(function(xhr, status) {
			if ('abort' === status) return;
			if (waitForIframe && pendingPreview) {
				pendingPreview.error = responseMessage(xhr);
				return;
			}
			showStatus(responseMessage(xhr), 'error');
		}).always(function() {
			if (!pendingPreview) button.prop('disabled', false);
		});
	}

	window.addEventListener('message', function(event) {
		if (!pendingPreview || !event.data || 'upfront-theme-style' !== event.data.type) return;
		if (!/^https:\/\/([a-z0-9-]+\.)?(codepen\.io|cdpn\.io|codepenusercontent\.com)$/i.test(event.origin)) return;

		var preview = pendingPreview;
		window.clearTimeout(preview.timeout);
		pendingPreview = null;
		if (preview.request) preview.request.abort();
		receivedPayload = event.data.payload;
		requestPreview({payload: JSON.stringify(receivedPayload)}, preview.button);
	});

	$('#upfront-codepen-form').on('submit', function() {
		var version = data.themeVersion ? ' v' + data.themeVersion : '';
		$('#upfront-codepen-data').val(JSON.stringify({
			title: (data.themeName || 'Upfront Theme') + version,
			description: '',
			head: buildHead(),
			html: buildHtml(),
			css_starter: 'normalize',
			css_pre_processor: 'scss',
			css: buildScss(),
			js: buildImportSource()
		}));
	});

	$('#upfront-codepen-import-form').on('submit', function(event) {
		event.preventDefault();
		var button = $(this).find('button[type="submit"]');
		var url = $('#upfront-codepen-url').val();
		var source = embedUrl(url);
		button.prop('disabled', true);
		$('#upfront-codepen-preview').prop('hidden', true);
		$('#upfront-codepen-import-values').empty().append($('<p></p>').text(data.messages.checkingShort));
		showStatus(data.messages.checking, 'info');
		receivedPayload = null;

		if (!source) {
			button.prop('disabled', false);
			showStatus(data.messages.requestFailed, 'error');
			return;
		}

		if (pendingPreview) {
			window.clearTimeout(pendingPreview.timeout);
			pendingPreview.iframe.remove();
			if (pendingPreview.request) pendingPreview.request.abort();
		}
		var iframe = $('<iframe></iframe>').attr({src: source, title: data.messages.previewTitle, hidden: true});
		pendingPreview = {
			button: button,
			iframe: iframe,
			timeout: window.setTimeout(function() {
				var preview = pendingPreview;
				var error = preview && preview.error;
				pendingPreview = null;
				if (preview && preview.request) preview.request.abort();
				button.prop('disabled', false);
				$('#upfront-codepen-import-values').empty().append($('<p></p>').text(data.messages.noImportData));
				showStatus(error || data.messages.legacyPen, 'error');
			}, 15000)
		};
		$('#upfront-codepen-render').empty().append($('<p></p>').text(data.messages.checkingShort), iframe);
		pendingPreview.request = requestPreview({url: url}, button, true);
	});

	$('#upfront-codepen-import').on('click', function() {
		var button = $(this);
		var parts = $('input[name="upfront-codepen-parts"]:checked').map(function() {
			return this.value;
		}).get();
		if (!parts.length || !window.confirm(data.messages.confirm)) return;

		button.prop('disabled', true);
		showStatus(data.messages.importing, 'info');
		$.post(data.ajaxUrl, {
			action: 'upfront_codepen_import',
			nonce: data.nonce,
			payload: JSON.stringify(receivedPayload),
			parts: parts
		}).done(function(response) {
			if (!response || true !== response.success) {
				showStatus(response && response.data && response.data.message ? response.data.message : data.messages.requestFailed, 'error');
				return;
			}
			var applied = response.data && response.data.applied ? response.data.applied : {};
			if (applied.colors) {
				data.themeColors = applied.colors;
				colors = applied.colors.colors || [];
			}
			if (applied.fonts) {
				data.themeFonts = applied.fonts;
				fonts = applied.fonts;
			}
			if (applied.typography) {
				data.typography = applied.typography;
				typography = applied.typography;
			}
			renderStyleValues('#upfront-codepen-current-values', buildImportData());
			$.post(data.ajaxUrl, {action: 'upfront_reset_cache'}).always(function() {
				showStatus(data.messages.imported, 'success');
			});
		}).fail(function(xhr) {
			showStatus(responseMessage(xhr), 'error');
		}).always(function() {
			button.prop('disabled', false);
		});
	});
})(jQuery);