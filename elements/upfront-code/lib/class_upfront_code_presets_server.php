<?php

require_once Upfront::get_root_dir() . '/library/servers/class_upfront_presets_server.php';

class Upfront_Code_Presets_Server extends Upfront_Presets_Server {
	private static $instance;

	public function get_element_name() {
		return 'code';
	}

	public static function serve() {
		self::$instance = new self;
		self::$instance->_add_hooks();
	}

	public static function get_instance() {
		return self::$instance;
	}

	public function get_presets() {
		$key = $this->get_child_theme_key();
		global $wpdb;
		$value = $key ? $wpdb->get_var($wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
			$key
		)) : '[]';
		$presets = json_decode((string) $value, true);
		return is_array($presets) ? $presets : array();
	}

	public function save() {
		if (!isset($_POST['data'])) return;
		if (!Upfront_Permissions::current(Upfront_Permissions::MODIFY_ELEMENT_PRESETS)) $this->_reject();

		$properties = wp_unslash($_POST['data']);
		if (empty($properties['id']) || empty($properties['name'])) $this->_reject(__('Ungültige Code-Vorlage.', 'upfront'));

		$presets = array();
		foreach ($this->get_presets() as $preset) {
			if (!empty($preset['id']) && $preset['id'] === $properties['id']) continue;
			$presets[] = $preset;
		}
		$presets[] = $properties;
		if (!$this->update_presets($presets)) $this->_reject(__('Die Code-Vorlage konnte nicht im aktiven Child-Theme gespeichert werden.', 'upfront'));

		$this->_out(new Upfront_JsonResponse_Success($properties));
	}

	protected function update_presets($presets = array()) {
		$key = $this->get_child_theme_key();
		if (!$key) return false;
		$value = wp_json_encode(array_values($presets));
		update_option($key, $value);
		return $value === get_option($key, false);
	}

	private function get_child_theme_key() {
		$stylesheet = sanitize_key(get_stylesheet());
		if (empty($stylesheet) || 'upfront' === $stylesheet || $stylesheet === sanitize_key(get_template())) return false;
		return 'upfront_' . $stylesheet . '_code_presets';
	}

	protected function get_style_template_path() {
		return false;
	}
}

Upfront_Code_Presets_Server::serve();