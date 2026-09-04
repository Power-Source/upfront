var assert = require('assert'),
	fs = require('fs'),
	path = require('path'),
	vm = require('vm');

describe('Pako AMD registration', function () {
	['pako.js', 'pako.min.js'].forEach(function (filename) {
		it('registers ' + filename + ' as a named module', function () {
			var moduleName,
				source = fs.readFileSync(path.join(__dirname, '../../scripts/pako', filename), 'utf8'),
				define = function (name) {
					moduleName = name;
				};

			define.amd = {};
			vm.runInNewContext(source, { define: define });

			assert.equal(moduleName, 'pako');
		});
	});
});