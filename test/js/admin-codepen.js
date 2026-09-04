var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('CodePen import', function () {
	it('checks the Pen URL when the iframe does not provide import data', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/admin-codepen.js'), 'utf8'),
			formHandler = source.indexOf("$('#upfront-codepen-import-form').on('submit'"),
			pendingRegistration = source.indexOf('pendingPreview = {', formHandler),
			iframeInsertion = source.indexOf("$('#upfront-codepen-render').empty().append(", formHandler);

		assert.notEqual(source.indexOf('requestPreview({url: url}, button, true)'), -1);
		assert.notEqual(source.indexOf("if ('abort' === status) return;"), -1);
		assert.notEqual(source.indexOf("'/fullembedgrid/'"), -1);
		assert.notEqual(source.indexOf('renderStylePreview(result.payload)'), -1);
		assert.notEqual(source.indexOf('hidden: true'), -1);
		assert.ok(pendingRegistration < iframeInsertion, 'preview must be registered before the iframe can post its data');
	});
});