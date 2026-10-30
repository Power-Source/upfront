<?php

require_once dirname(__FILE__) . '/class_upfront_presets_server.php';

class Upfront_Module_Group_Presets_Server extends Upfront_Presets_Server {
	private static $instance;
	protected $isModuleGroupServer = true;

	public function get_element_name() {
		return 'module_group';
	}

	public static function serve() {
		self::$instance = new self;
		self::$instance->_add_hooks();
		add_filter('get_element_preset_styles', array(self::$instance, 'get_preset_styles_filter'));
	}

	public static function get_instance() {
		return self::$instance;
	}

	public function get_preset_styles_filter($styles) {
		return $styles . $this->get_presets_styles();
	}

	protected function get_style_template_path() {
		return Upfront::get_root_dir() . '/scripts/upfront/templates/module-group-preset-style.html';
	}

	public static function get_preset_defaults() {
		return array(
			'id' => 'default',
			'name' => self::$instance->get_l10n('default_preset'),
			'preset_style' => ''
		);
	}
}

add_action('upfront-core-initialized', array('Upfront_Module_Group_Presets_Server', 'serve'));
