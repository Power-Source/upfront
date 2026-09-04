var assert = require('assert'),
	fs = require('fs'),
	path = require('path'),
	vm = require('vm');

describe('Pako AMD registration', function () {
	it('loads the minified named module to avoid the cached anonymous URL', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../library/servers/class_upfront_javascript_main.php'), 'utf8');

		assert.notEqual(source.indexOf('"pako" => "scripts/pako/pako.min"'), -1);
	});

	it('registers pako.min.js as a named module', function () {
		var moduleName,
			source = fs.readFileSync(path.join(__dirname, '../../scripts/pako/pako.min.js'), 'utf8'),
			define = function (name) {
				moduleName = name;
			};

		define.amd = {};
		vm.runInNewContext(source, { define: define });

		assert.equal(moduleName, 'pako');
	});
});