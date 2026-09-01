/*global module, require */
module.exports = function(grunt) {
	var spawnSync = require('child_process').spawnSync;

	grunt.registerTask('makepot', 'Generate the Upfront translation template.', function() {
		var result = spawnSync('wp', [
			'i18n',
			'make-pot',
			'.',
			'languages/upfront.pot',
			'--ignore-domain',
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
	});

	grunt.registerTask('default', ['makepot']);
};
