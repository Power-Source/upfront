var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Redactor links', function () {
	it('uses pointer events instead of the jQuery UI draggable adapter for modals', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/redactor.js'), 'utf8'),
			setDraggableStart = source.indexOf('setDraggable: function()'),
			setDraggableEnd = source.indexOf('setEnter: function(e)', setDraggableStart),
			setDraggableSource = source.slice(setDraggableStart, setDraggableEnd);

		assert.notEqual(setDraggableSource.indexOf("header.addEventListener('pointerdown', pointerDown)"), -1);
		assert.equal(setDraggableSource.indexOf('.draggable('), -1);
	});

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

	it('uses the 1.2.5 link formatter on the restored text selection', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/redactor/plugins.js'), 'utf8'),
			linkStart = source.indexOf('link: function (dontflag)'),
			linkEnd = source.indexOf('bindEvents: function', linkStart),
			linkSource = source.slice(linkStart, linkEnd),
			restore = linkSource.indexOf('this.redactor.selection.restore()'),
			setLink = linkSource.indexOf('this.redactor.link.set(');

		assert.ok(restore !== -1 && restore < setLink);
		assert.notEqual(linkSource.indexOf('OPEN_TAG_RPL_MARK'), -1);
		assert.notEqual(linkSource.indexOf('CLOSE_TAG_RPL_MARK'), -1);
		assert.notEqual(setLink, -1);
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