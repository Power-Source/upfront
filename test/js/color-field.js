var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Color field', function () {
	it('handles an unset saved color while rendering the preview', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-views-editor/fields.js'), 'utf8'),
			isHexStart = source.indexOf('is_hex : function'),
			isHexEnd = source.indexOf('get_field_html:', isHexStart),
			previewStart = source.indexOf('update_color_picker_preview: function'),
			previewEnd = source.indexOf('rgba_sidebar_changed', previewStart),
			isHexSource = source.slice(isHexStart, isHexEnd),
			previewSource = source.slice(previewStart, previewEnd);

		assert.notEqual(isHexSource.indexOf('typeof color_code === "string"'), -1);
		assert.notEqual(previewSource.indexOf('typeof color === "string" ? color : ""'), -1);
	});

	it('does not fire a second change after moving to a palette color', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/spectrum/spectrum.js'), 'utf8'),
			clickStart = source.indexOf('function paletteElementClick'),
			clickEnd = source.indexOf('var paletteEvent', clickStart),
			clickSource = source.slice(clickStart, clickEnd);

		assert.ok(/}\s*else\s*{\s*updateOriginalInput\(\);/.test(clickSource));
	});

	it('shows an OK button while applying Redactor palette clicks immediately', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			openStart = source.indexOf('open: function (e, redactor)'),
			openEnd = source.indexOf('getActiveColorTarget:', openStart),
			openSource = source.slice(openStart, openEnd),
			moveCallbacks = openSource.match(/move: function \(color\) \{[\s\S]*?\n\s*\}/g) || [],
			changeCallbacks = openSource.match(/change: function \(color\) \{[\s\S]*?\n\s*\}/g) || [],
			chooseStart = openSource.indexOf('this.$(".sp-choose").on'),
			chooseSource = openSource.slice(chooseStart);

		assert.equal((openSource.match(/showButtons: true/g) || []).length, 2);
		assert.equal(moveCallbacks.length, 2);
		moveCallbacks.forEach(function (callback) {
			assert.notEqual(callback.indexOf('updateColors'), -1);
		});
		assert.equal(changeCallbacks.length, 2);
		changeCallbacks.forEach(function (callback) {
			assert.equal(callback.indexOf('updateColors'), -1);
		});
		assert.equal(chooseSource.indexOf('self.updateColors'), -1);
	});

	it('formats the restored selection directly without restoring it twice', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			updateStart = source.indexOf('updateColors: function'),
			inlineStart = source.indexOf('if (is_inline_selection)', updateStart),
			inlineEnd = source.indexOf("return 'inline'", inlineStart),
			inlineSource = source.slice(inlineStart, inlineEnd);

		assert.notEqual(inlineSource.indexOf('contents = range.extractContents()'), -1);
		assert.notEqual(inlineSource.indexOf('range.insertNode(wrapper)'), -1);
		assert.equal(inlineSource.indexOf('self.redactor.inline.format('), -1);
		assert.equal(inlineSource.indexOf('self.redactor.selection.restore()'), -1);
	});

	it('sets the resolved theme color inline as well as keeping its class', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			colorSetStart = source.indexOf('color_set = function'),
			colorSetEnd = source.indexOf('color_remove = function', colorSetStart),
			colorSetSource = source.slice(colorSetStart, colorSetEnd);

		assert.notEqual(colorSetSource.indexOf('theme_color = raw_value.theme_color_code || raw_value.theme_color'), -1);
		assert.ok(/if \(cls\)[\s\S]*?\.addClass\(cls\)[\s\S]*?if \(!!theme_color\)[\s\S]*?\.attr\("style", rule \+ ':' \+ theme_color\)/.test(colorSetSource));
	});

	it('converts the truthy theme color position back to a zero-based UFC index', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			themeColorStart = source.indexOf('var theme_color_index'),
			themeColorEnd = source.indexOf('var self = this', themeColorStart),
			themeColorSource = source.slice(themeColorStart, themeColorEnd);

		assert.equal((themeColorSource.match(/theme_color_index -= 1;/g) || []).length, 2);
		assert.equal((themeColorSource.match(/convert_string_ufc_to_color\([^;]+, false\)/g) || []).length, 2);
	});
});