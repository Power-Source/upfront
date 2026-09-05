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

	it('commits a palette color immediately', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/spectrum/spectrum.js'), 'utf8'),
			clickStart = source.indexOf('function paletteElementClick'),
			clickEnd = source.indexOf('var paletteEvent', clickStart),
			clickSource = source.slice(clickStart, clickEnd);

		assert.ok(/}\s*else\s*{\s*updateOriginalInput\(true\);/.test(clickSource));
	});

	it('formats the restored selection directly and closes the picker', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			updateStart = source.indexOf('updateColors: function'),
			inlineStart = source.indexOf('if (is_inline_selection)', updateStart),
			inlineEnd = source.indexOf("return 'inline'", inlineStart),
			inlineSource = source.slice(inlineStart, inlineEnd),
			finishStart = source.indexOf('finishColorChange: function'),
			finishEnd = source.indexOf('getActiveColorTarget:', finishStart),
			finishSource = source.slice(finishStart, finishEnd);

		assert.notEqual(inlineSource.indexOf('contents = range.extractContents()'), -1);
		assert.notEqual(inlineSource.indexOf('range.insertNode(wrapper)'), -1);
		assert.equal(inlineSource.indexOf('self.redactor.inline.format('), -1);
		assert.notEqual(source.indexOf("if (self.updateColors('foreground')) self.finishColorChange()"), -1);
		assert.notEqual(finishSource.indexOf('this.panel.hide()'), -1);
		assert.notEqual(finishSource.indexOf("this.button.removeClass('dropact redactor_act')"), -1);
		assert.notEqual(finishSource.indexOf('this.redactor.selection.remove()'), -1);
	});
});