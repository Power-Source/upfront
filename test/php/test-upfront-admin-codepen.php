<?php
/**
 * @group upfront-admin
 */
class Upfront_Admin_CodePen_Test extends WP_UnitTestCase {

	public function setUp () {
		parent::setUp();
		require_once(dirname(__FILE__) . '/../../library/class_upfront_admin.php');
	}

	public function test_parse_pen_url () {
		$url = Upfront_Admin_CodePen::parse_pen_url('https://codepen.io/karner42oliver/pen/dPvYeGo');
		$this->assertSame('https://codepen.io/karner42oliver/pen/dPvYeGo.js', $url);

		$editor_url = Upfront_Admin_CodePen::parse_pen_url('https://codepen.io/editor/karner42oliver/pen/01a03ac8-38b5-7846-bdb7-3ececce5da80?panel=details');
		$this->assertSame('https://codepen.io/karner42oliver/pen/01a03ac8-38b5-7846-bdb7-3ececce5da80.js', $editor_url);

		$raw_url = 'https://0198772a-a759-7dc4-b8fc-1dfdbed39fdb.codepenusercontent.com/script.js';
		$this->assertSame($raw_url, Upfront_Admin_CodePen::parse_pen_url($raw_url));

		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('http://codepen.io/user/pen/abc'));
		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('https://example.com/user/pen/abc'));
		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('https://codepenusercontent.com/script.js'));
		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('https://example.codepenusercontent.com/../script.js'));
		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('https://example.codepenusercontent.com/style.css'));
		$this->assertWPError(Upfront_Admin_CodePen::parse_pen_url('https://codepen.io/collection/adZzzB'));
	}

	public function test_validate_payload () {
		$payload = Upfront_Admin_CodePen::validate_payload(array(
			'schema' => 'upfront-theme-style',
			'version' => 1,
			'name' => 'Parrot',
			'colors' => array(
				'colors' => array(array('color' => '#123456'), array('color' => 'red; display:none')),
				'range' => 4,
			),
			'fonts' => array(array('font' => array('family' => 'Roboto'), 'variant' => 'regular')),
			'typography' => array(
				'h1' => array('font_face' => 'Roboto', 'size' => '32', 'unknown' => 'discarded'),
				'body<script>' => array('color' => 'red'),
			),
		));

		$this->assertFalse(is_wp_error($payload));
		$this->assertSame(array(array('color' => '#123456')), $payload['colors']['colors']);
		$this->assertSame('Roboto', $payload['fonts'][0]['font']['family']);
		$this->assertSame(array('font_face' => 'Roboto', 'size' => '32'), $payload['typography']['h1']);
		$this->assertArrayNotHasKey('body<script>', $payload['typography']);
	}

	public function test_rejects_unknown_schema () {
		$payload = Upfront_Admin_CodePen::validate_payload(array('schema' => 'other', 'version' => 1));
		$this->assertWPError($payload);
	}

	public function test_limits_colors_and_builds_their_runtime_styles () {
		$colors = array();
		for ($index = 0; $index < 12; $index++) {
			$colors[] = array('color' => sprintf('#%06d', $index));
		}
		$payload = Upfront_Admin_CodePen::validate_payload(array(
			'schema' => 'upfront-theme-style',
			'version' => 1,
			'colors' => array('colors' => $colors),
		));

		$this->assertCount(10, $payload['colors']['colors']);
		$styles = Upfront_Theme_Style::build_color_styles($payload['colors']['colors']);
		$this->assertContains('.upfront_theme_color_9', $styles);
		$this->assertNotContains('.upfront_theme_color_10', $styles);
		$this->assertContains('background-color: #000009', $styles);
	}

	public function test_theme_settings_are_written_to_child_file () {
		$file = tempnam(sys_get_temp_dir(), 'upfront-settings-');
		$settings = new Upfront_Theme_Settings($file);
		$colors = wp_json_encode(array('colors' => array(array('color' => '#123456'))));

		$this->assertTrue($settings->set('theme_colors', $colors));
		$reloaded = new Upfront_Theme_Settings($file);
		$this->assertSame($colors, $reloaded->get('theme_colors'));

		unlink($file);
	}

	public function test_only_existing_database_overrides_are_synchronized () {
		$method = new ReflectionMethod('Upfront_Theme_Style', 'persist_override');
		$method->setAccessible(true);
		$key = 'upfront_test_theme_style_override';
		delete_option($key);

		$this->assertTrue($method->invoke(null, $key, 'child-value'));
		$this->assertFalse(get_option($key, false));

		update_option($key, 'old-value');
		$this->assertTrue($method->invoke(null, $key, 'child-value'));
		$this->assertSame('child-value', get_option($key));

		delete_option($key);
	}
}