var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Redactor links', function () {
	it('saves the text selection before the link button can take focus', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			buttonCreation = source.indexOf('var btn = self.button[addMethod](id, b.title)'),
			mousedown = source.indexOf("btn.on('mousedown'", buttonCreation),
			preventDefault = source.indexOf('e.preventDefault()', mousedown),
			save = source.indexOf('self.selection.save()', mousedown);

		assert.ok(buttonCreation !== -1 && buttonCreation < mousedown);
		assert.ok(mousedown < preventDefault && preventDefault < save);
		assert.notEqual(source.indexOf('b.panel.button = btn', buttonCreation), -1);
	});

	it('wraps the restored text selection in an anchor', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			linkStart = source.indexOf('link: function (dontflag)'),
			linkEnd = source.indexOf('bindEvents: function', linkStart),
			linkSource = source.slice(linkStart, linkEnd),
			restore = linkSource.indexOf('this.redactor.selection.restore()'),
			wrap = linkSource.indexOf("this.redactor.selection.wrap('a')");

		assert.ok(restore !== -1 && restore < wrap);
		assert.notEqual(linkSource.indexOf(".attr('href', this.linkModel.get('url'))"), -1);
		assert.equal(linkSource.indexOf('this.redactor.link.set('), -1);
	});

	it('closes the link panel without clicking the link button again', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			panelStart = source.indexOf('RedactorPlugins.upfrontLink'),
			panelEnd = source.indexOf('RedactorPlugins.upfrontColor', panelStart),
			panelSource = source.slice(panelStart, panelEnd);

		assert.equal(panelSource.indexOf("$('a.re-upfrontLink').click()"), -1);
		assert.equal(panelSource.indexOf('this.redactor.caret.setOffset('), -1);
		assert.notEqual(panelSource.indexOf('this.panel.hide()'), -1);
		assert.notEqual(panelSource.indexOf("this.button.removeClass('dropact redactor_act')"), -1);
	});

	it('does not remove href from links without a visit flag', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/ueditor.js'), 'utf8'),
			hideStart = source.indexOf('hideLinkFlags: function'),
			hideEnd = source.indexOf('visitLink: function', hideStart),
			hideSource = source.slice(hideStart, hideEnd),
			guard = hideSource.indexOf('if (!$flag.length) return'),
			removeHref = hideSource.indexOf("this.removeAttribute('href')");

		assert.ok(guard !== -1 && guard < removeHref);
	});
});