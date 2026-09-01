/*global module, require */
module.exports = function(grunt) {
	var fs = require('fs');
	var spawnSync = require('child_process').spawnSync;

	grunt.registerTask('makepot', 'Generate the Upfront translation template.', function() {
		var result = spawnSync('wp', [
			'i18n',
			'make-pot',
			'.',
			'languages/upfront.pot',
			'--domain=upfront',
			'--exclude=node_modules,test',
			'--skip-audit'
		], {
			stdio: 'inherit'
		});

		if (result.error) {
			grunt.fail.fatal(result.error.message);
		}
		if (result.status !== 0) {
			grunt.fail.fatal('WP-CLI could not generate languages/upfront.pot.');
		}

		var potPath = 'languages/upfront.pot';
		var blocks = fs.readFileSync(potPath, 'utf8').split(/\n\n/);
		var filtered = blocks.filter(function(block) {
			var references = block.split('\n').filter(function(line) {
				return line.indexOf('#:') === 0;
			}).reduce(function(all, line) {
				return all.concat(line.slice(2).trim().split(/\s+/));
			}, []);

			return !references.length || references.some(function(reference) {
				return reference !== 'style.css';
			});
		});
		fs.writeFileSync(potPath, filtered.join('\n\n').replace(/\n*$/, '\n'));
	});

	grunt.registerTask('default', ['makepot']);
};
