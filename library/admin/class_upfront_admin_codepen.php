<?php

class Upfront_Admin_CodePen extends Upfront_Admin_Page {

	private $hook_suffix = '';

	public function __construct () {
		if (!$this->_can_access()) return;

		$this->hook_suffix = add_submenu_page(
			Upfront_Admin::$menu_slugs['main'],
			__('Upfront zu CodePen', Upfront::TextDomain),
			__('CodePen-Styleguide', Upfront::TextDomain),
			'manage_options',
			Upfront_Admin::$menu_slugs['codepen'],
			array($this, 'render_page')
		);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	public function enqueue_scripts ($hook) {
		if ($hook !== $this->hook_suffix) return;

		$theme = wp_get_theme();
		$layout_properties = Upfront_Layout::get_layout_properties();
		$typography = upfront_get_property_value('typography', array('properties' => $layout_properties));
		$theme_colors = Upfront_Cache_Utils::get_option('upfront_' . get_stylesheet() . '_theme_colors');
		$theme_colors = apply_filters('upfront_get_theme_colors', $theme_colors, array('json' => true));
		$theme_fonts = Upfront_Cache_Utils::get_option('upfront_' . get_stylesheet() . '_theme_fonts');
		$theme_fonts = apply_filters('upfront_get_theme_fonts', $theme_fonts, array('json' => true));

		wp_enqueue_script(
			'upfront-admin-codepen',
			Upfront::get_root_url() . '/scripts/admin-codepen.js',
			array('jquery'),
			Upfront_ChildTheme::get_version(),
			true
		);
		wp_localize_script('upfront-admin-codepen', 'Upfront_CodePen', array(
			'themeName' => $theme->get('Name'),
			'themeVersion' => $theme->get('Version'),
			'themeColors' => $this->decode_setting($theme_colors),
			'themeFonts' => $this->decode_setting($theme_fonts),
			'typography' => $this->decode_setting($typography),
		));
	}

	public function render_page () {
		?>
		<div class="wrap upfront_admin upfront-codepen">
			<h1><?php esc_html_e('CodePen-Styleguide', Upfront::TextDomain); ?></h1>
			<div class="postbox-container">
				<div class="postbox">
					<h2 class="title"><?php esc_html_e('Theme-Stile exportieren', Upfront::TextDomain); ?></h2>
					<div class="inside">
						<p><?php esc_html_e('Erstellt einen neuen CodePen mit den Farben und Typografie-Einstellungen des aktiven Upfront-Themes.', Upfront::TextDomain); ?></p>
						<form id="upfront-codepen-form" action="https://codepen.io/pen/define" method="post" target="_blank">
							<input type="hidden" id="upfront-codepen-data" name="data" value="">
							<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Neuen Pen erstellen', Upfront::TextDomain); ?></button></p>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function decode_setting ($value) {
		if (is_string($value)) {
			$decoded = json_decode($value, true);
			return is_array($decoded) ? $decoded : array();
		}
		if (is_object($value)) return json_decode(wp_json_encode($value), true);
		return is_array($value) ? $value : array();
	}
}