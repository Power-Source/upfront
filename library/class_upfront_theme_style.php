<?php

class Upfront_Theme_Style {

	public static function apply($style, $parts) {
		$stylesheet = get_stylesheet();
		$parts = array_intersect((array) $parts, array('colors', 'fonts', 'typography'));
		$child = Upfront_ChildTheme::get_instance();
		$settings = $child instanceof Upfront_ChildTheme ? $child->get_theme_settings() : false;
		if (!$settings instanceof Upfront_Theme_Settings) {
			return new WP_Error('upfront_theme_style_settings', __('Die Einstellungen des aktiven Child-Themes sind nicht verfügbar.', 'upfront'));
		}

		if (in_array('colors', $parts, true)) {
			$colors = wp_json_encode($style['colors']);
			if (!$settings->set('theme_colors', $colors)
				|| !self::persist_override('upfront_' . $stylesheet . '_theme_colors', $colors)
				|| !self::persist_override('upfront_' . $stylesheet . '_theme_colors_styles', self::build_color_styles($style['colors']['colors']))) {
				return new WP_Error('upfront_theme_style_colors', __('Die Theme-Farben konnten nicht gespeichert werden.', 'upfront'));
			}
		}

		if (in_array('fonts', $parts, true)) {
			$fonts = wp_json_encode($style['fonts']);
			if (!$settings->set('theme_fonts', $fonts)
				|| !self::persist_override('upfront_' . $stylesheet . '_theme_fonts', $fonts)) {
				return new WP_Error('upfront_theme_style_fonts', __('Die Theme-Schriften konnten nicht gespeichert werden.', 'upfront'));
			}
			Upfront_Local_Fonts_Server::sync_theme_fonts($style['fonts']);
		}

		if (in_array('typography', $parts, true)) {
			$typography = wp_json_encode($style['typography']);
			if (!$settings->set('typography', $typography)) {
				return new WP_Error('upfront_theme_style_typography', __('Die Typografie konnte nicht im Child-Theme gespeichert werden.', 'upfront'));
			}
			$layout_data = array('properties' => Upfront_Layout::get_layout_properties());
			upfront_set_property_value('typography', $style['typography'], $layout_data);
			$key = Upfront_Layout::get_storage_key() . '-' . $stylesheet . '-layout-properties';
			if (!self::persist_override($key, wp_json_encode($layout_data['properties']))) {
				return new WP_Error('upfront_theme_style_typography', __('Die Typografie konnte nicht gespeichert werden.', 'upfront'));
			}
		}

		return self::get($parts);
	}

	public static function get($parts = array('colors', 'fonts', 'typography')) {
		$stylesheet = get_stylesheet();
		$settings = Upfront_ChildTheme::get_settings();
		$style = array('stylesheet' => $stylesheet);
		if (!$settings instanceof Upfront_Theme_Settings) return $style;

		if (in_array('colors', $parts, true)) {
			$style['colors'] = self::decode($settings->get('theme_colors'), array('colors' => array(), 'range' => 0));
		}
		if (in_array('fonts', $parts, true)) {
			$style['fonts'] = self::decode($settings->get('theme_fonts'), array());
		}
		if (in_array('typography', $parts, true)) {
			$style['typography'] = self::decode($settings->get('typography'), array());
		}

		return $style;
	}

	private static function persist_override($key, $value) {
		if (false === get_option($key, false)) return true;
		Upfront_Cache_Utils::clear_cache($key, 'upfront_options');
		update_option($key, $value);
		Upfront_Cache_Utils::clear_cache($key, 'upfront_options');
		return $value === get_option($key);
	}

	private static function decode($value, $default) {
		$decoded = is_string($value) ? json_decode($value, true) : $value;
		return is_array($decoded) ? $decoded : $default;
	}

	public static function build_color_styles($colors) {
		$styles = '';
		foreach (array_slice((array) $colors, 0, 10) as $index => $item) {
			if (empty($item['color'])) continue;
			$color = $item['color'];
			$styles .= " .upfront_theme_color_{$index}{ color: {$color};}";
			$styles .= " a .upfront_theme_color_{$index}:hover{ color: {$color};}";
			$styles .= " button .upfront_theme_color_{$index}:hover{ color: {$color};}";
			$styles .= " .upfront_theme_bg_color_{$index}{ background-color: {$color};}";
			$styles .= " a .upfront_theme_bg_color_{$index}:hover{ background-color: {$color};}";
			$styles .= " button .upfront_theme_bg_color_{$index}:hover{ background-color: {$color};}";
		}
		return $styles;
	}
}