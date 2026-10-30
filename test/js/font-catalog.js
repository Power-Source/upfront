var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Font catalog', function () {
	it('shows readable labels while keeping local font IDs as select values', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-views-editor/fonts.js'), 'utf8'),
			loadStart = source.indexOf('load_google_fonts: function'),
			loadEnd = source.indexOf('update_variants_on_remove', loadStart),
			loadSource = source.slice(loadStart, loadEnd);

		assert.notEqual(loadSource.indexOf("label: font.get('label') || font.get('family')"), -1);
		assert.notEqual(loadSource.indexOf("value: font.get('family')"), -1);
	});

	it('parses numeric italic variants from the generated catalog', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-views-editor/fonts.js'), 'utf8'),
			parseStart = source.indexOf('parse_variant: function'),
			parseEnd = source.indexOf('get_variant: function', parseStart),
			parseSource = source.slice(parseStart, parseEnd);

		assert.notEqual(parseSource.indexOf("variant.match(/^(\\d+)italic$/)"), -1);
	});

	it('loads and renders each saved theme font in its own style', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-views-editor/fonts.js'), 'utf8'),
			itemStart = source.indexOf('var ThemeFontListItem'),
			itemEnd = source.indexOf('var ThemeFontsPanel', itemStart),
			itemSource = source.slice(itemStart, itemEnd),
			template = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/templates/popup.html'), 'utf8');

		assert.notEqual(itemSource.indexOf("family=' + encodeURIComponent(family + ':' + variant)"), -1);
		assert.notEqual(itemSource.indexOf("'font-family': family"), -1);
		assert.notEqual(itemSource.indexOf("'font-weight': weight_style.weight"), -1);
		assert.notEqual(itemSource.indexOf("'font-style': weight_style.style"), -1);
		assert.notEqual(template.indexOf('theme-font-list-item-preview'), -1);
	});
});
