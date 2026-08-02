<?php

class Upfront_CodeView extends Upfront_Object {

	public function get_markup () {
		$properties = array();
		foreach ($this->_data['properties'] as $prop) {
			$properties[$prop['name']] = $prop['value'];
		}
		//$properties = wp_parse_args($properties, self::default_properties());

		if (empty($properties)) return ''; // No info for this element, carry on.

		$element_id = !empty($properties['element_id']) ? $properties['element_id'] : false; // Try to give the styles some context.

		// Alright! Let's see if we have any CSS here and scope it if we do
		$style = !empty($properties['style'])
			? $this->_to_scoped_style($properties['style'], $element_id)
			: ''
		;
		$script = !empty($properties['script'])
			? $this->_to_scoped_script($properties['script'])
			: ''
		;
		$markup = !empty($properties['markup'])
			? $this->_to_valid_html($properties['markup'])
			: ''
		;
		return '<div class="upfront_code-element clearfix">' . $markup . $style . $script . "</div>";
	}

	private function _to_scoped_style ($raw, $id) {
		$id = !empty($id) ? "#{$id} .upfront_code-element" : '.upfront_code-element';
		$scoped = '';
		$raw = explode('}', $raw);
		if (empty($raw)) return $scoped;
		foreach ($raw as $rule) {
			$scoped .= "{$id} {$rule} }";
		}

		if (class_exists('Upfront_UFC')) {
			$scoped = Upfront_UFC::init()->process_colors($scoped);
		}

		return !empty($scoped)
			? "<style>{$scoped}</style>"
			: ''
		;
	}

	private function _to_scoped_script ($raw) {
		return !empty($raw)
			? "<script>;try { (function ($) { {$raw}\n })(jQuery); } catch(e) {}</script>"
			: ''
		;
	}

	private function _to_valid_html ($raw) {
		if (class_exists('DOMDocument') && class_exists('DOMXpath')) {
			// So this is just wrong on so many levels, but apparently necessary... 
			// Force the content type header, so that DOMDocument encoding doesn't default to latin-1 -.-
			// As per: http://stackoverflow.com/questions/3523409/domdocument-encoding-problems-characters-transformed
			$raw = "<head><meta http-equiv='Content-type' content='text/html; charset=UTF-8' /></head><body>{$raw}</body>";
			
			$doc = new DOMDocument();
			if (function_exists('libxml_use_internal_errors')) libxml_use_internal_errors(true);
			$doc->loadHTML($raw);
			$parsed = $doc->saveHTML();

			if (function_exists('libxml_use_internal_errors')) libxml_use_internal_errors(false);
			$raw = !empty($parsed)
				? preg_replace('/^.*<body>/ms', '', preg_replace('/<\/body>.*$/ms', '', $parsed))
				: $raw
			;
		}

		return $raw;
	}

	public static function default_properties () {
		return array(
			'type' => "CodeModel",
			'view_class' => "CodeView",
			"class" => "c24 upfront-code_element-object",
			'has_settings' => 0,
			'id_slug' => 'upfront-code_element',

			'fallbacks' => array(
				'markup' => self::_get_l10n('default_markup'),
				'style' => self::_get_l10n('default_style'),
				'script' => self::_get_l10n('default_script'),
			)
		);
	}

	public static function add_js_defaults ($data) {
		$data['upfront_code'] = array(
			'defaults' => self::default_properties(),
		 );
		return $data;
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['code_element'])) return $strings;
		$strings['code_element'] = self::_get_l10n();
		return $strings;
	}

	private static function _get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Code', 'upfront'),
			'default_markup' => __('<b>Gib Dein Markup hier ein...</b>', 'upfront'),
			'default_style' => __('/* Deine Styles hier */', 'upfront'),
			'default_script' => __('/* Dein Code hier */', 'upfront'),
			'settings' => __('Einstellungen', 'upfront'),
			'intro' => array(
				'embed' => __('Code von Drittanbietern einbetten', 'upfront'),
				'code' => __('Eigenen Code schreiben', 'upfront'),
			),
			'create' => array(
				'change' => __('Klicken, um zu ändern', 'upfront'),
				'ok' => __('OK', 'upfront'),
			),
			'errors' => array(
				'markup' => __('HTML Fehler:', 'upfront'),
				'style' => __('CSS Fehler:', 'upfront'),
				'script' => __('JS Fehler:', 'upfront'),
				'error_markup' => __('Es gibt einen Fehler in deinem HTML. Bitte überprüfe dein Markup auf ungültige Argumente, fehlerhafte Tags und Ähnliches.', 'upfront'),
			),
			'template' => array(
				'html' => __('HTML', 'upfront'),
				'css' => __('CSS', 'upfront'),
				'js' => __('JS', 'upfront'),
				'link_image' => __('Bild verlinken', 'upfront'),
				'link_theme_image' => __('Theme-Bild verlinken', 'upfront'),
				'code_error' => __('Es gibt einen Fehler in deinem JS-Code', 'upfront'),
				'close' => __('Schließen', 'upfront'),
				'save' => __('Speichern', 'upfront'),
				'paste_your_code' => __('Füge deinen Einbettungscode unten ein', 'upfront'),
			),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}

}