<?php

/**
 * Object implementation for Search entity.
 * A fairly simple implementation, with applied settings.
 */
class Upfront_UsliderView extends Upfront_Object {

	public function get_markup () {
		$data = $this->properties_to_array();
		$slides = array();
		if ( isset($data['slides']) && $data['slides'] ) {
			foreach($data['slides'] as $slide){
				$slide = array_merge(self::slide_defaults(), $slide);
				$slide['breakpoint_map'] = !empty($slide['breakpoint']) ? json_encode($slide['breakpoint']) : "";
				$slides[] = $slide;
			}
		}

		if (isset($data['usingNewAppearance']) === false) {
			$data['usingNewAppearance'] = false;
		}

		if (!isset($data['preset'])) {
			$data['preset'] = 'default';
		}
		$data['properties'] = Upfront_Slider_Presets_Server::get_instance()->get_preset_properties($data['preset']);

		$data['slides'] = $slides;
		$data['rotate'] = $data['rotate'] ? true : false;
		$data['keyboardControls'] = $data['keyboardControls'] ? true : false;

		$data['dots'] = array_search($data['controls'], array('dots', 'both')) !== false;
		$data['arrows'] = array_search($data['controls'], array('arrows', 'both')) !== false;

		$data['slidesLength'] = sizeof($slides);

		$side_style = $data['properties']['primaryStyle'] === 'side';

		$data['imageWidth'] = $side_style ? floor($data['rightImageWidth'] / $data['rightWidth'] * 100) . '%': '100%';
		$data['textWidth'] =  $side_style ? floor(($data['rightWidth'] - $data['rightImageWidth']) / $data['rightWidth'] * 100) . '%' : '100%';

		$data['imageHeight'] = sizeof($slides) ? $slides[0]['cropSize']['height'] : 0;

		$data['production'] = true;
		$data['startingSlide'] = 0;

		// Overwrite properties with preset properties
		if ($data['usingNewAppearance'] !== false) {
			if (isset($data['properties']['primaryStyle'])) {
				$data['primaryStyle'] = $data['properties']['primaryStyle'];
			}
			if (isset($data['properties']['captionBackground'])) {
				$data['captionBackground'] = $data['properties']['captionBackground'];
			}
		}


		$markup = upfront_get_template('uslider', $data, dirname(dirname(__FILE__)) . '/tpl/uslider.html');

		return $markup;
	}

	public static function add_styles_scripts () {
		upfront_add_element_style('uslider_css', array('css/uslider.css', dirname(__FILE__)));
		upfront_add_element_style('uslider_settings_css', array('css/uslider_settings.css', dirname(__FILE__)));
		//wp_enqueue_style( 'uslider_css', upfront_element_url('css/uslider.css', dirname(__FILE__)), array(), "0.1" );
		//wp_enqueue_style( 'uslider_settings_css', upfront_element_url('css/uslider_settings.css', dirname(__FILE__)), array(), "0.1" );

		//wp_enqueue_script('uslider-front', upfront_element_url('js/uslider-front.js', dirname(__FILE__)), array('jquery'));
		upfront_add_element_script('uslider-front', array('js/uslider-front.js', dirname(__FILE__)));
	}

	public static function add_js_defaults($data){
		$data['uslider'] = array(
			'defaults' => self::default_properties(),
			'slideDefaults' => self::slide_defaults(),
			'template' => upfront_get_template_url('uslider', upfront_element_url('tpl/uslider.html', dirname(__FILE__)))
		);
		return $data;
	}

	private function properties_to_array(){
		$out = array();
		foreach($this->_data['properties'] as $prop){
			$out[$prop['name']] = $prop['value'];
		}
		return $out;
	}

	public static function default_properties(){
		return array(
			'id_slug' => 'uslider',
			'type' => "USliderModel",
			'view_class' => "USliderView",
			"class" => "c24 upfront-uslider",
			'has_settings' => 1,
			'preset' => 'default',

			'primaryStyle' => 'default', // notext, below, over, side, onlytext

			/* TO BE DEPRECATED, it is moved inside the slide */
			'style' => 'bottomOver', // nocaption, below, above, right, bottomOver, topOver, bottomCover, middleCover, topCover

			'controls' => 'both', // both, arrows, dots, none
			'controlsWhen' => 'always', // always, hover

			'rotate' => array('false'),
			'rotateTime' => 5,
			'keyboardControls' => array('true'),

			'transition' => 'crossfade', // crossfade, slide-left, slide-right, slide-bottom, slide-top
			'slides' => array(), // Convert to Uslider_Slides to use, and to Object to store

			'captionUseBackground' => '0',
			'captionBackground' => apply_filters('upfront_slider_caption_background', 'transparent'),

			/* TO BE DEPRECATED, it is moved inside the slide */
			'rightImageWidth' => 3,
			'rightWidth' => 6,
		);
	}


	public static function slide_defaults(){
		return array(
			'id' => 0,
			'src' => '',
			'srcFull' => '',
			'sizes' => array(),
			'size' => array('width' => 0, 'height' => 0),
			'cropSize' => array('width' => 0, 'height' => 0),
			'cropOffset' => array('top' => 0, 'left' => 0),
			'rotation' => 0,
			'url' => '',
			'urlType' => '',
			'text' => '',
			'margin' => array('left' => 0, 'top' => 0),
			'captionColor' => apply_filters('upfront_slider_caption_color', '#ffffff'),
			'captionBackground' => apply_filters('upfront_slider_caption_background', '#000000'),


			'style' => 'bottomOver', // nocaption, below, above, right, bottomOver, topOver, bottomCover, middleCover, topCover
			'rightImageWidth' => 3,
			'rightWidth' => 6,

			'breakpoint' => ''
		);
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['slider_element'])) return $strings;
		$strings['slider_element'] = self::_get_l10n();
		return $strings;
	}

	private static function _get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Slider', 'upfront'),
			'css' => array(
				'images_label' => __('Bilder', 'upfront'),
				'images_info' => __('Bilder des Sliders', 'upfront'),
				'captions_label' => __('Bildunterschriften', 'upfront'),
				'captions_info' => __('Bildunterschriften der Slides', 'upfront'),
				'caption_label' => __('Beschriftungsfeld', 'upfront'),
				'caption_info' => __('Beschriftungsebene', 'upfront'),
				'img_containers_label' => __('Bildcontainer', 'upfront'),
				'img_containers_info' => __('Die Bildebene', 'upfront'),
				'dots_wrapper_label' => __('Navigationspunkte-Wrapper', 'upfront'),
				'dots_wrapper_info' => __('Container der Navigationspunkte', 'upfront'),
				'dots_label' => __('Navigationspunkte', 'upfront'),
				'dots_info' => __('Marker der Navigationspunkte', 'upfront'),
				'dot_current_label' => __('Aktueller Navigationspunkt', 'upfront'),
				'dot_current_info' => __('Der Punkt, der die aktuelle Folie darstellt', 'upfront'),
				'prev_label' => __('Navigation vorheriger', 'upfront'),
				'prev_info' => __('Vorheriger Navigationsbutton', 'upfront'),
				'next_label' => __('Navigation nächster', 'upfront'),
				'next_info' => __('Nächster Navigationsbutton', 'upfront'),
			),
			'settings' => __('Einstellungen', 'upfront'),
			'general' => __('Allgemeine Einstellungen', 'upfront'),
			'above_img' => __('Bildunterschrift über der Folie', 'upfront'),
			'below_img' => __('Bildunterschrift unter der Folie', 'upfront'),
			'slider_behaviour' => __('Slider-Verhalten', 'upfront'),
			'image_caption_position' => __('Bild- &amp; Bildunterschriften-Position:', 'upfront'),
			'slider_transition' => __('Slider-Übergang:', 'upfront'),
			'back_button' => __('Zurück', 'upfront'),
			'no_text' => __('Keine Bildunterschrift', 'upfront'),
			'over_top' => __('Über dem Bild, oben', 'upfront'),
			'over_bottom' => __('Über dem Bild, unten', 'upfront'),
			'cover_top' => __('Bedeckt das Bild, oben', 'upfront'),
			'cover_mid' => __('Bedeckt das Bild, Mitte', 'upfront'),
			'cover_bottom' => __('Bedeckt das Bild, unten', 'upfront'),
			'at_right' => __('Folie links, Bildunterschrift rechts', 'upfront'),
			'at_left' => __('Folie rechts, Bildunterschrift links', 'upfront'),
			'cap_position' => __('Layout der Bildunterschrift', 'upfront'),
			'edit_img' => __('Aktuelle Folie zuschneiden', 'upfront'),
			'remove_slide' => __('Folie entfernen', 'upfront'),
			'img_link' => __('Link der aktuellen Folie', 'upfront'),
			'preparing_img' => __('Bilder werden vorbereitet', 'upfront'),
			'preparing_slides' => __('Folien werden vorbereitet', 'upfront'),
			'slider_styles' => __('Slider Stile', 'upfront'),
			'notxt' => __('Keine Bildunterschrift', 'upfront'),
			'txtb' => __('Bildunterschrift unter der Folie', 'upfront'),
			'txto' => __('Bildunterschrift über der Folie', 'upfront'),
			'txts' => __('Bildunterschrift an der Seite', 'upfront'),
			'caption_bg' => __('Hintergrund der Bildunterschrift', 'upfront'),
			'none' => __('Keine', 'upfront'),
			'pick_color' => __('Farbe wählen', 'upfront'),
			'colors' => __('Farben', 'upfront'),
			'rotate_every' => __('Automatisch drehen alle ', 'upfront'),
			'slide_down' => __('Nach unten gleiten', 'upfront'),
			'slide_up' => __('Nach oben gleiten', 'upfront'),
			'slide_right' => __('Nach rechts gleiten', 'upfront'),
			'slide_left' => __('Nach links gleiten', 'upfront'),
			'crossfade' => __('Crossfade', 'upfront'),
			'slider_controls' => __('Slider Kontrollen', 'upfront'),
			'slider_controls_style' => __('Slider-Kontrollen Stil', 'upfront'),
			'on_hover' => __('Bei Hover anzeigen', 'upfront'),
			'always' => __('Immer anzeigen', 'upfront'),
			'show_controls' => __('Kontrollen anzeigen', 'upfront'),
			'dots' => __('Punkte', 'upfront'),
			'arrows' => __('Pfeile', 'upfront'),
			'both' => __('Beide', 'upfront'),
			'enable_arrows_slide' => __('Mit Pfeiltasten durch Folien navigieren?', 'upfront'),
			'ok' => __('Ok', 'upfront'),
			'slides_order' => __('Reihenfolge der Folien', 'upfront'),
			'accessibility' => __('Barrierefreiheit', 'upfront'),
			'slides' => __('Folien', 'upfront'),
			'add_slide' => __('Folie hinzufügen', 'upfront'),
			'choose_type' => __('Bitte wähle den Typ des Sliders', 'upfront'),
			'can_change' => __('Dies kann später über das Einstellungsfeld geändert werden', 'upfront'),
			'img_only' => __('Nur Bild', 'upfront'),
			'default' => __('Standard', 'upfront'),
			'txt_over_img' => __('Text über dem Bild', 'upfront'),
			'txt_below_img' => __('Text unter dem Bild', 'upfront'),
			'txt_on_side' => __('Text an der Seite', 'upfront'),
			'txt_only' => __('Nur Text / Widget', 'upfront'),
			'choose_img' => __('Bilder auswählen', 'upfront'),
			'slide_desc' => __('Folienbeschreibung', 'upfront'),
			'delete_slide_confirm' => __('Bist Du sicher, dass Du diese Folie löschen möchtest?', 'upfront'),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}
}
