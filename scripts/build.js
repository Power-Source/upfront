const fs = require('fs');
const path = require('path');
const vm = require('vm');

const themeDir = path.resolve(__dirname, '..');
const buildDir = path.join(themeDir, 'build');
const profilePath = path.join(__dirname, 'app.build.js');
const stylePath = path.join(themeDir, 'style.css');

function readThemeVersion() {
	const match = fs.readFileSync(stylePath, 'utf8').match(/^Version:\s*(\S+)\s*$/mi);
	if (!match) throw new Error('Theme version is missing from style.css');
	return match[1];
}

function readBuildProfile() {
	const source = fs.readFileSync(profilePath, 'utf8');
	return vm.runInNewContext(source, {}, {filename: profilePath});
}

function resolveRequireJs() {
	try {
		return require('requirejs');
	} catch (error) {
		throw new Error('Missing build dependency. Run npm install before building.');
	}
}

function createConfig(version) {
	const profile = readBuildProfile();
	const redactorSource = path.join(__dirname, 'redactor', 'redactor.js');
	const redactorFallback = path.join(buildDir, 'redactor', 'redactor.js');

	delete profile.appDir;
	delete profile.dir;
	delete profile.modules;
	delete profile.optimizeCss;
	delete profile.removeCombined;

	profile.baseUrl = __dirname;
	profile.name = 'main-' + version;
	profile.create = true;
	profile.include = ['requireLib', 'setup'];
	profile.out = path.join(buildDir, '.main-' + version + '.tmp.js');
	profile.paths.redactor = fs.existsSync(redactorSource)
		? 'redactor/redactor'
		: '../build/redactor/redactor';

	if (!fs.existsSync(redactorSource) && !fs.existsSync(redactorFallback)) {
		throw new Error('Missing Redactor source and build fallback');
	}

	return profile;
}

async function main() {
	const version = readThemeVersion();
	const outputPath = path.join(buildDir, 'main-' + version + '.js');
	const config = createConfig(version);

	console.log('Theme version:', version);
	console.log('Output:', outputPath);
	console.log('Redactor:', config.paths.redactor);

	if (process.argv.includes('--check')) return;

	fs.mkdirSync(buildDir, {recursive: true});
	const requirejs = resolveRequireJs();
	const report = await new Promise((resolve, reject) => {
		requirejs.optimize(config, resolve, reject);
	});
	fs.renameSync(config.out, outputPath);
	fs.writeFileSync(path.join(buildDir, 'build.txt'), report + '\n');
}

main().catch((error) => {
	console.error(error.stack || error.message || error);
	process.exitCode = 1;
});