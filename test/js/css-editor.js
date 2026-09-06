var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('CSS editor', function () {
	var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-views-editor/css/css-editor.js'), 'utf8');

	it('keeps ordinary CSS outside comments while tokenizing', function () {
		var tokenizeStart = source.indexOf('var tokenize = function(css)'),
			tokenizeEnd = source.indexOf('\n\n\n\t\treturn Backbone.View.extend', tokenizeStart),
			tokenizeSource = source.slice(tokenizeStart, tokenizeEnd),
			tokenize;

		eval(tokenizeSource + '\ntokenize = tokenize;');

		assert.deepEqual(tokenize('.title { color: red; }'), ['.title { color: red; }', '']);
	});

	it('scopes CSS before resolving UFC theme-color placeholders', function () {
		var updateStart = source.indexOf('updateStyles: function(contents)'),
			updateEnd = source.indexOf('\n\n\t\t\tstylesAddSelector:', updateStart),
			updateSource = source.slice(updateStart, updateEnd),
			scopePosition = updateSource.indexOf('this.stylesAddSelector('),
			colorPosition = updateSource.indexOf('convert_string_ufc_to_color(contents)');

		assert.notEqual(scopePosition, -1);
		assert.notEqual(colorPosition, -1);
		assert.ok(scopePosition < colorPosition);
		assert.equal(source.indexOf('openingWs'), -1);
	});
});