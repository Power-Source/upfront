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
});