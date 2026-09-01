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
		return cssValue(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
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
			links.push('<link href="https://fonts.googleapis.com/css?family=' + encodeURIComponent(family).replace(/%20/g, '+') + (variant ? ':' + encodeURIComponent(variant) : '') + '" rel="stylesheet">');
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
			output += '\t\t<' + tag + '>Überschrift ' + (index + 1) + '</' + tag + '>\n';
		});
		output += '\t\t<a href="#">Beispiel-Link</a>\n\t\t<p>Beispieltext für die Typografie des aktiven Themes.</p>\n';
		output += '\t\t<ul><li>Listeneintrag</li><li>Listeneintrag</li></ul>\n';
		output += '\t\t<ol><li>Listeneintrag</li><li>Listeneintrag</li></ol>\n';
		output += '\t\t<blockquote>Beispiel für ein Zitat.</blockquote>\n';
		output += '\t\t<blockquote class="upfront-quote-alternative">Alternatives Zitat.</blockquote>\n';
		output += '\t</div>\n</div>\n';
		return output;
	}

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
			js: ''
		}));
	});
})(jQuery);