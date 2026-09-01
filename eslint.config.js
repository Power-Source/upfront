module.exports = [
	{
		ignores: [
			'node_modules/**',
			'build/**',
			'scripts/chosen/**',
			'scripts/file_upload/**',
			'scripts/findandreplace/**',
			'scripts/jquery/**',
			'scripts/magnific-popup/**',
			'scripts/realperson/**',
			'scripts/spectrum/**',
			'**/*.min.js'
		]
	},
	{
		files: ['**/*.js'],
		linterOptions: {
			reportUnusedDisableDirectives: false
		},
		languageOptions: {
			ecmaVersion: 'latest',
			sourceType: 'script'
		},
		rules: {}
	}
];