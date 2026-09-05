var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Button hover animation', function () {
	var presetServer = fs.readFileSync(path.join(__dirname, '../../elements/upfront-button/lib/class_upfront_button_presets_server.php'), 'utf8'),
		styleTemplate = fs.readFileSync(path.join(__dirname, '../../elements/upfront-button/tpl/preset-style.html'), 'utf8'),
		buttonView = fs.readFileSync(path.join(__dirname, '../../elements/upfront-button/lib/upfront_button.php'), 'utf8'),
		defaults = presetServer.slice(presetServer.indexOf('public static function get_preset_defaults'));

	it('uses one consistent set of enabled animation defaults', function () {
		assert.equal((defaults.match(/'hov_duration'\s*=>/g) || []).length, 1);
		assert.notEqual(defaults.indexOf("'hov_use_animation' => 'yes'"), -1);
		assert.notEqual(defaults.indexOf("'hov_duration' => 0.3"), -1);
		assert.notEqual(defaults.indexOf("'hov_transition' => 'ease-in-out'"), -1);
	});

	it('normalizes legacy and locale-specific animation values', function () {
		assert.notEqual(presetServer.indexOf("str_replace(',', '.', trim((string) $preset['hov_duration']))"), -1);
		assert.notEqual(presetServer.indexOf("isset($preset['hov_easing']) ? $preset['hov_easing']"), -1);
		assert.notEqual(presetServer.indexOf("$presets = $this->migrate_presets($presets);"), -1);
	});

	it('only emits the transition when animation is enabled', function () {
		assert.notEqual(styleTemplate.indexOf("if(!empty($properties['hov_use_animation']))"), -1);
		assert.notEqual(styleTemplate.indexOf("transition: all <?php echo $properties['hov_duration']"), -1);
		assert.notEqual(buttonView.indexOf('Upfront_Button_Presets_Server::normalize_animation($item)'), -1);
		assert.notEqual(buttonView.indexOf("$preset['hov_use_animation'] === 'yes'"), -1);
	});
});