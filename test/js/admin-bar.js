var assert = require('assert'),
	fs = require('fs'),
	path = require('path'),
	JSDOM = require('jsdom').JSDOM;

describe('Mobile multisite admin bar', function () {
	it('limits the menu and truncates long names without affecting other menus', function () {
		var css = fs.readFileSync(path.join(__dirname, '../../styles/global.css'), 'utf8'),
			sheetDom = new JSDOM('<style>' + css + '</style>'),
			rules = Array.from(sheetDom.window.document.styleSheets[0].cssRules),
			mobile = rules.find(function (rule) {
				return rule.conditionText === 'screen and (max-width: 600px)' &&
					rule.cssText.indexOf('#wp-admin-bar-my-sites') !== -1;
			}),
			dom = new JSDOM('<style>' + Array.from(mobile.cssRules).map(function (rule) {
				return rule.cssText;
			}).join('\n') + '</style><div id="wpadminbar">' +
			'<div id="wp-admin-bar-my-sites"><div class="ab-sub-wrapper">' +
			'<a class="ab-item">A very long site name</a></div></div>' +
			'<div id="wp-admin-bar-other"><div class="ab-sub-wrapper"><a class="ab-item">Other</a></div></div>' +
			'</div>'),
			document = dom.window.document,
			menu = document.querySelector('#wp-admin-bar-my-sites .ab-sub-wrapper'),
			link = menu.querySelector('.ab-item'),
			other = document.querySelector('#wp-admin-bar-other .ab-item');

		assert.strictEqual(dom.window.getComputedStyle(menu).minWidth, '0px');
		assert.strictEqual(dom.window.getComputedStyle(menu).maxWidth, '100%');
		assert.strictEqual(dom.window.getComputedStyle(link).overflow, 'hidden');
		assert.strictEqual(dom.window.getComputedStyle(link).textOverflow, 'ellipsis');
		assert.notStrictEqual(dom.window.getComputedStyle(other).overflow, 'hidden');
		dom.window.close();
		sheetDom.window.close();
	});
});
