<?php

/**
 * Object implementation for Search entity.
 * A fairly simple implementation, with applied settings.
 */
class Upfront_UsearchView extends Upfront_Object {
	public function get_markup(){
		$data = $this->properties_to_array();
		$data['action'] = home_url('/');
		$data['iconClass'] = $data['label'] == '__image__' ? 'icon' : 'text';
		$data['roundedClass'] = !empty($data['is_rounded']) ? 'rounded' : '';

		return upfront_get_template('usearch', $data, dirname(dirname(__FILE__)) . '/tpl/usearch.html');
	}

	private function properties_to_array(){
		$out = array();
		foreach($this->_data['properties'] as $prop)
			$out[$prop['name']] = $prop['value'];
		return $out;
	}

	public static function add_js_defaults($data){
		$data['usearch'] = array(
			'defaults' => self::default_properties(),
		 );
		return $data;
	}

	//Defaults for properties
	public static function default_properties(){
		return array(
			'type' => 'UsearchModel',
			'view_class' => 'UsearchView',
			'class' => 'c24 upfront-search',
			'has_settings' => 1,
			'id_slug' => 'usearch',

			'placeholder' => self::_get_l10n('placeholder'),
			'label' => self::_get_l10n('custom'),
			'is_rounded' => 0,
			'color' => ''
		);
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['search_element'])) return $strings;
		$strings['search_element'] = self::_get_l10n();
		return $strings;
	}

	private static function _get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Suche', 'upfront'),
			'placeholder' => __('Suche', 'upfront'),
			'custom' => __('Eigener Text', 'upfront'),
			'css' => array(
				'container_label' => __('Suche-Container', 'upfront'),
				'container_info' => __('Der Container, der das Suchfeld und die Suchschaltfläche umschließt', 'upfront'),
				'field_label' => __('Suchfeld', 'upfront'),
				'field_info' => __('Das Such-Eingabefeld', 'upfront'),
				'button_label' => __('Suchschaltfläche', 'upfront'),
				'button_info' => __('Die Suchschaltfläche', 'upfront'),
			),
			'settings' => __('Suche-Einstellungen', 'upfront'),
			'placeholder_label' => __('Platzhaltertext:', 'upfront'),
			'field' => __('Feld', 'upfront'),
			'field_settings' => __('Feldeinstellungen', 'upfront'),
			'btn_content' => __('Schaltflächeninhalt', 'upfront'),
			'rounded' => __('Abgerundete Form', 'upfront'),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}

	public static function add_frontend_dependencies () {
		upfront_add_element_style('usearch_style', array('css/search.css', dirname(__FILE__)));
	}
}
