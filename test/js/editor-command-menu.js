var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Editor command menu', function () {
	var menuPath = '../../scripts/upfront/upfront-views-editor/commands/menu/';

	it('keeps close and WP Admin clicks out of the parent menu handler', function () {
		var closeSource = fs.readFileSync(path.join(__dirname, menuPath + 'command-close.js'), 'utf8'),
			adminSource = fs.readFileSync(path.join(__dirname, menuPath + 'command-wpadmin.js'), 'utf8');

		assert.notEqual(closeSource.indexOf('event.stopPropagation()'), -1);
		assert.notEqual(adminSource.indexOf('event.stopPropagation()'), -1);
	});

	it('renders WP Admin as the styled command item', function () {
		var source = fs.readFileSync(path.join(__dirname, menuPath + 'command-wpadmin.js'), 'utf8');

		assert.notEqual(source.indexOf('this.$el.html(l10n.wp_admin)'), -1);
		assert.equal(source.indexOf("this.$el.html('<a"), -1);
	});

	it('delegates child command events whenever the menu opens', function () {
		var source = fs.readFileSync(path.join(__dirname, menuPath + '../command-menu.js'), 'utf8'),
			toggle = source.indexOf('this.toggle_menu()'),
			delegate = source.indexOf('command.delegateEvents()', toggle);

		assert.ok(toggle !== -1 && toggle < delegate);
	});

	it('provides one overridable contextual help URL', function () {
		var source = fs.readFileSync(path.join(__dirname, menuPath + '../command-menu.js'), 'utf8');

		assert.notEqual(source.indexOf('"helpUrl": this.get_help_url()'), -1);
		assert.notEqual(source.indexOf('get_help_url: function ()'), -1);
		assert.notEqual(source.indexOf("return 'https://psource.eimen.net/wiki/upfront-dokumentation/'"), -1);
	});
});