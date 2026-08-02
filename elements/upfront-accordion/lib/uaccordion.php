<?php
/**
 * Accordion element for Upfront
 */
class Upfront_UaccordionView extends Upfront_Object {
	public static function default_properties() {
		$defaultPanel = new StdClass();
		$defaultPanel->title = self::_get_l10n('default_panel_title');
		$defaultPanel->content = self::_get_l10n('default_panel_content');
		$secondPanel = new StdClass();
		$secondPanel->title = self::_get_l10n('panel_label').' 2';
		$secondPanel->content = self::_get_l10n('content_label');
		return array(
			'type' => 'UaccordionModel',
			'view_class' => 'UaccordionView',
			'has_settings' => 1,
			'class' =>  'upfront-accordion',
			'accordion' => array($defaultPanel, $secondPanel),
			'accordion_count' => 2,
			'accordion_fixed_width' => 'auto',
			'id_slug' => 'uaccordion',
			'preset' => 'default',
		);
	}

	function __construct($data) {
		$data['properties'] = $this->merge_default_properties($data);
		parent::__construct($data);
	}

	protected function merge_default_properties($data){
		$flat = array();
		if(!isset($data['properties']))
			return $flat;

		foreach($data['properties'] as $prop) {
			if (isset($prop['value']) === false) continue;
			$flat[$prop['name']] = $prop['value'];
		}

		$flat = array_merge(self::default_properties(), $flat);

		$properties = array();
		foreach($flat as $name => $value)
			$properties[] = array('name' => $name, 'value' => $value);

		return $properties;
	}

	public function get_markup () {
		// This data is passed on to the template to precompile template
		$data = $this->properties_to_array();

		// Do shortcode
		foreach($data['accordion'] as $index=>$accordion) {
			$accordion['content'] = $this->_do_shortcode($accordion['content']);
			$data['accordion'][$index] = $accordion;
		}

		$data['preset'] = isset($data['preset']) ? $data['preset'] : 'default';

		$data['wrapper_id'] = str_replace('uaccordion-object-', 'wrapper-', $data['element_id']);

		$markup = upfront_get_template('uaccordion', $data, dirname(dirname(__FILE__)) . '/tpl/uaccordion.html');

		// upfront_add_element_style('uaccordion_style', array('css/uaccordion.css', dirname(__FILE__)));
		upfront_add_element_script('uaccordion_script', array('js/uaccordion-front.js', dirname(__FILE__)));
		return $markup;
	}

	protected function _do_shortcode ($content) {
		return Upfront_Codec::get('wordpress')->do_shortcode($content);
	}

	public function add_js_defaults($data){
		$newdata = array(
			'defaults' => self::default_properties(),
			'template' => upfront_get_template_url('uaccordion', upfront_element_url('tpl/uaccordion.html', dirname(__FILE__)))
		);

		if(isset($data['uaccordion'])) {
			if(isset($data['uaccordion']['defaults'])) {
				$merged_defaults = array_merge($data['uaccordion']['defaults'], $newdata['defaults']);
				$data['uaccordion']['defaults'] = $merged_defaults;
			}
			else {
				$data['uaccordion']['defaults'] = $newdata['defaults'];
			}
			$data['uaccordion']['template'] = $newdata['template'];
		}
		else
			$data['uaccordion'] = $newdata;

		return $data;
	}

	private function properties_to_array(){
		$out = array();
		foreach($this->_data['properties'] as $prop)
			$out[$prop['name']] = $prop['value'];
		return $out;
	}
	public static function add_styles_scripts() {
		upfront_add_element_style('uaccordion_style', array('css/uaccordion.css', dirname(__FILE__)));
		//wp_enqueue_style('uaccordion_style', upfront_element_url('css/uaccordion.css', dirname(__FILE__)));
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['accordion_element'])) return $strings;
		$strings['accordion_element'] = self::_get_l10n();
		return $strings;
	}

	private static function _get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Akkordeon', 'upfront'),
			'default_panel_title' => __('Panel 1', 'upfront'),
			'default_panel_content' => __('<p>Klicke auf den aktiven Panel-Titel, um den Titel zu bearbeiten. Bestätige mit der Eingabetaste.</p><p>Klicke auf die Plus-Schaltfläche [+], um ein neues Panel hinzuzufügen.</p>', 'upfront'),
			'css' => array(
				'containers_label' => __('Panel Container', 'upfront'),
				'containers_info' => __('Der Wrapper-Layer jedes Panels.', 'upfront'),
				'header_label' => __('Panel-Header', 'upfront'),
				'header_info' => __('Der Header-Titel jedes Panels.', 'upfront'),
				'active_header_label' => __('Aktiver Panel-Header', 'upfront'),
				'active_header_info' => __('Der Header-Titel des aktiven Panels.', 'upfront'),
				'body_label' => __('Panel-Inhalt', 'upfront'),
				'body_info' => __('Der Inhaltsbereich jedes Panels.', 'upfront'),
				'first_label' => __('Erster Panel-Container', 'upfront'),
				'first_info' => __('Der Wrapper-Layer des ersten Panels.', 'upfront'),
				'last_label' => __('Letzter Panel-Container', 'upfront'),
				'last_info' => __('Der Wrapper-Layer des letzten Panels.', 'upfront'),
				'odd_label' => __('Ungerader Panel-Container', 'upfront'),
				'odd_info' => __('Der Wrapper-Layer der ungeraden Panels.', 'upfront'),
				'even_label' => __('Gerader Panel-Container', 'upfront'),
				'even_info' => __('Der Wrapper-Layer der geraden Panels.', 'upfront'),
				'wrap' => __('Element Wrapper', 'upfront'),
				'wrap_info' => __('Der Wrapper des gesamten Elements.', 'upfront'),
			),
			'settings' => __('Einstellungen', 'upfront'),
			'panel_label'	=> __('Panel', 'upfront'),
			'add_panel'	=> __('Panel hinzufügen', 'upfront'),
			'content_label' => __('<p>Inhalt</p>', 'upfront'),
			'appearance' => __('Aussehen', 'upfront'),
			'section_bg' => __('Abschnitt Hintergrund:', 'upfront'),
			'header_bg' => __('Header Hintergrund:', 'upfront'),
			'header_border' => __('Header Rahmen:', 'upfront'),
			'default_preset' => __('Standard', 'upfront'),
			'content_area_colors_label' => __('Content Area Farben', 'upfront'),
			'content_area_bg_label' => __('Content Area BG', 'upfront'),
			'colors_label' => __('Farben', 'upfront'),
			'header_bg_label' => __('Header BG', 'upfront'),
			'triangle_icon_label' => __('Triangle Icon', 'upfront'),
			'typography_tab_label' => __('Tab Label Typography', 'upfront'),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}

}

function upfront_accordion_add_local_url ($data) {
	$data['upfront_accordion'] = array(
		"root_url" => trailingslashit(upfront_element_url('/', dirname(__FILE__)))
	);
	return $data;
}
add_filter('upfront_data', 'upfront_accordion_add_local_url');
