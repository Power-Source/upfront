<?php

class Upfront_UwidgetView extends Upfront_Object {

	public function get_markup () {
		$element_id = $this->_get_property('element_id');
		$element_id = $element_id ? "id='{$element_id}'" : '';

		$widget_name = $this->_get_property('widget');
		$widget = new Upfront_Uwidget($widget_name);
		$fields = $widget->get_widget_admin_fields();

		// Treat the legacy widget setup
		if (empty($fields) && !(defined('DOING_AJAX') && DOING_AJAX)) {
			$fields_tmp = $this->_get_property('widget_specific_fields');
			if (!empty($fields_tmp)) foreach ($fields_tmp as $field) {
				if (empty($field['name'])) continue;
				$fields[] = array('name' => $field['name']);
			}
		}
		// We should be good here now

		$instance = array();
		$role = '';

		foreach($fields as $field) {
			$name = !empty($field['name']) ? $field['name'] : false;
			if (empty($name)) continue;
			$instance[$name] = $this->_get_property($name);
		}
		
		
		// If custom menu widget we need role navigation
		if( strpos($widget_name, 'nav_menu') !== false ) {
			$role = ' role="navigation" aria-label="'. $widget_name .'"';
		}

		return "<div class='upfront-widget' ". $role .">" .
			$widget->get_widget_markup($instance) .
		"</div>";
	}


	public static function add_js_defaults($data){
		$self = !empty($data['uwidget']) ? $data['uwidget'] : array();
		$data['uwidget'] = array_merge($self, array(
			'defaults' => self::default_properties(),
		));
		return $data;
	}

	public static function default_properties(){
		return array(
			'id_slug' => 'uwidget',
			'type' => "UwidgetModel",
			'view_class' => "UwidgetView",
			"class" => "c24 upfront-widget",
			'has_settings' => 1,
			'preset' => 'default',
			'widget' => false
		);
	}

	public static function add_dependencies () {
		if (Upfront_Permissions::current(Upfront_Permissions::BOOT)) {
			upfront_add_element_style('upfront_widget', array('css/widget.css', dirname(__FILE__)));
		}
	}

	public static function add_data ($data) {
		$self = !empty($data['uwidget']) ? $data['uwidget'] : array();
		$data['uwidget'] = array_merge($self, array(
			'widgets' => Upfront_Uwidget::get_widget_list()
		));
		return $data;
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['widget_element'])) return $strings;
		$strings['widget_element'] = self::get_l10n();
		return $strings;
	}

	public static function get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Widget', 'upfront'),
			'loading' => __('Lade...', 'upfront'),
			'done' => __('Erledigt!', 'upfront'),
			'widget' => __('Widget', 'upfront'),
			'settings' => __('Widget-Einstellungen', 'upfront'),
			'general_settings' => __('Allgemeine Einstellungen', 'upfront'),
			'widget_select' => __('Widget auswählen', 'upfront'),
			'select_widget' => __('Bitte wähle ein Widget in den Einstellungen aus', 'upfront'),
			'select_one' => __('Bitte wähle ein Widget aus', 'upfront'),
			'css' => array(
				'container_label' => __('Widget-Container', 'upfront'),
				'container_info' => __('Der Container, der das Widget-Element umschließt', 'upfront'),
				'links_label' => __('Widget-Links', 'upfront'),
				'links_info' => __('Die Links im Widget', 'upfront'),
			),
			'render_error' => __('Hoppla, bei der Darstellung des Widgets scheint etwas schiefgelaufen zu sein.', 'upfront'),
			'not_shown_to_visitors' => __('Diese Nachricht wird deinen Besuchern nicht angezeigt', 'upfront'),
			'missing_admin_data' => __('Wir konnten keine Felder für das ausgewählte Widget finden. Etwas könnte nicht stimmen.', 'upfront'),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}

}
