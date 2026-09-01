<?php

class Upfront_SocialMediaView extends Upfront_Object {
	public function get_markup() {
		$type = $this->_get_property('social_type');
		$mode = 'likes' === $type ? 'share' : 'profile';
		$services = $this->_get_property('share' === $mode ? 'services' : 'button_services');
		$size = $this->_get_property('button_size') ?: 'medium';
		$style = $this->_get_property('button_style') ?: 'button-style-3';
		$markup = self::render_services($services, $mode);
		if (!$markup) return '<span class="upfront-general-notice">' . esc_html(self::_get_l10n('select_some')) . '</span>';
		return '<div class="upfront-social usocial-button-' . esc_attr($size) . ' upfront-' . esc_attr($style) . '">' . $markup . '</div>';
	}

	public static function services() {
		return array(
			'facebook' => array('id' => 'facebook', 'name' => 'Facebook', 'modes' => array('share', 'profile')),
			'twitter' => array('id' => 'twitter', 'name' => 'X', 'modes' => array('share', 'profile')),
			'instagram' => array('id' => 'instagram', 'name' => 'Instagram', 'modes' => array('profile')),
			'linkedin' => array('id' => 'linkedin', 'name' => 'LinkedIn', 'modes' => array('share', 'profile')),
			'youtube' => array('id' => 'youtube', 'name' => 'YouTube', 'modes' => array('profile')),
			'pinterest' => array('id' => 'pinterest', 'name' => 'Pinterest', 'modes' => array('profile')),
			'tiktok' => array('id' => 'tiktok', 'name' => 'TikTok', 'modes' => array('profile')),
			'threads' => array('id' => 'threads', 'name' => 'Threads', 'modes' => array('profile')),
			'bluesky' => array('id' => 'bluesky', 'name' => 'Bluesky', 'modes' => array('share', 'profile')),
			'mastodon' => array('id' => 'mastodon', 'name' => 'Mastodon', 'modes' => array('profile')),
			'reddit' => array('id' => 'reddit', 'name' => 'Reddit', 'modes' => array('share', 'profile')),
			'whatsapp' => array('id' => 'whatsapp', 'name' => 'WhatsApp', 'modes' => array('share', 'profile')),
			'telegram' => array('id' => 'telegram', 'name' => 'Telegram', 'modes' => array('share', 'profile')),
			'github' => array('id' => 'github', 'name' => 'GitHub', 'modes' => array('profile')),
			'email' => array('id' => 'email', 'name' => 'E-Mail', 'modes' => array('share', 'profile')),
		);
	}

	private static function canonical_id($id) {
		if ('x' === $id) return 'twitter';
		if ('linked-in' === $id) return 'linkedin';
		return $id;
	}

	private static function service_value($service, $key, $default = '') {
		if (is_object($service)) return isset($service->{$key}) ? $service->{$key} : $default;
		return isset($service[$key]) ? $service[$key] : $default;
	}

	private static function share_url($id, $url, $title) {
		$encoded_url = rawurlencode($url);
		$encoded_title = rawurlencode($title);
		$encoded_text = rawurlencode(trim($title . ' ' . $url));
		$urls = array(
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
			'twitter' => 'https://twitter.com/intent/tweet?text=' . $encoded_title . '&url=' . $encoded_url,
			'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
			'bluesky' => 'https://bsky.app/intent/compose?text=' . $encoded_text,
			'reddit' => 'https://www.reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title,
			'whatsapp' => 'https://wa.me/?text=' . $encoded_text,
			'telegram' => 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title,
			'email' => 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_text,
		);
		return isset($urls[$id]) ? $urls[$id] : '';
	}

	public static function render_services($services, $mode = 'share', $url = '', $title = '') {
		if (!is_array($services)) return '';
		$url = $url ?: (is_singular() ? get_permalink() : home_url('/'));
		$title = $title ?: wp_get_document_title();
		$registry = self::services();
		$icon_url = upfront_element_url('images/social-icons.svg', dirname(__FILE__));
		$output = '';
		foreach ($services as $service) {
			if (!self::service_value($service, 'active', false)) continue;
			$id = self::canonical_id(self::service_value($service, 'id'));
			if (!isset($registry[$id]) || !in_array($mode, $registry[$id]['modes'], true)) continue;
			$href = 'share' === $mode ? self::share_url($id, $url, $title) : self::service_value($service, 'url');
			if (!$href) continue;
			if ('profile' === $mode && 'email' === $id && false === strpos($href, ':')) $href = 'mailto:' . $href;
			$icon = '<svg class="usocial-icon" aria-hidden="true" focusable="false"><use href="' . esc_url($icon_url) . '#social-' . esc_attr($id) . '" xlink:href="' . esc_url($icon_url) . '#social-' . esc_attr($id) . '"></use></svg>';
			$output .= '<a class="upfront-social-icon usocial-' . esc_attr($id) . '" href="' . esc_url($href) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($registry[$id]['name']) . '">' . $icon . '</a>';
		}
		return $output;
	}

	public static function add_public_style() {
		upfront_add_element_style('upfront-social-media', array('css/upfront-social-media-style.css', dirname(__FILE__)));
	}

	private static function default_service_list($mode) {
		$output = array();
		foreach (self::services() as $service) {
			if (!in_array($mode, $service['modes'], true)) continue;
			$service['active'] = 'share' === $mode && in_array($service['id'], array('facebook', 'twitter', 'linkedin'), true);
			$service['url'] = '';
			$service['meta'] = array();
			$output[] = $service;
		}
		return $output;
	}

	public static function add_upfront_data($data) {
		$globals = Upfront_SocialMedia_Setting::get_globals();
		if ($globals && !empty($globals['services']) && is_array($globals['services'])) {
			$globals['services'] = array_map(function ($service) {
				return array(
					'id' => self::service_value($service, 'id'),
					'name' => self::service_value($service, 'name'),
					'url' => self::service_value($service, 'url'),
					'active' => (bool) self::service_value($service, 'active', false),
				);
			}, $globals['services']);
		}
		$data['usocial'] = array(
			'defaults' => self::default_properties(),
			'services' => self::services(),
			'icon_url' => upfront_element_url('images/social-icons.svg', dirname(__FILE__)),
			'globals' => $globals ?: false,
		);
		return $data;
	}

	public static function default_properties() {
		return array(
			'social_type' => 'likes',
			'services' => self::default_service_list('share'),
			'button_services' => self::default_service_list('profile'),
			'button_size' => 'medium',
			'button_style' => 'button-style-3',
			'id_slug' => 'SocialMedia',
			'type' => 'SocialMediaModel',
			'view_class' => 'SocialMediaView',
			'class' => 'c24 upfront-Social-Media',
			'has_settings' => 1,
		);
	}

	public static function add_l10n_strings($strings) {
		if (empty($strings['social_element'])) $strings['social_element'] = self::_get_l10n();
		return $strings;
	}

	public static function _get_l10n($key = false) {
		$l10n = array(
			'element_name' => __('Social', 'upfront'),
			'networks' => __('Soziale Netzwerke', 'upfront'),
			'settings' => __('Social-Media-Einstellungen', 'upfront'),
			'mode' => __('Verwendung', 'upfront'),
			'share' => __('Teilen', 'upfront'),
			'profiles' => __('Profil-Links', 'upfront'),
			'services' => __('Dienste', 'upfront'),
			'appearance' => __('Darstellung', 'upfront'),
			'size' => __('Größe', 'upfront'),
			'shape' => __('Form', 'upfront'),
			'small' => __('Klein', 'upfront'),
			'medium' => __('Mittel', 'upfront'),
			'large' => __('Groß', 'upfront'),
			'square' => __('Eckig', 'upfront'),
			'rounded' => __('Gerundet', 'upfront'),
			'circle' => __('Rund', 'upfront'),
			'select_some' => __('Bitte wähle mindestens einen Dienst.', 'upfront'),
			'url_missing' => __('Bitte hinterlege eine Profil-URL.', 'upfront'),
			'css' => array(
				'container_label' => __('Social-Container', 'upfront'),
				'container_info' => __('Enthält alle Social-Media-Links', 'upfront'),
				'box_label' => __('Social-Link', 'upfront'),
				'box_info' => __('Ein einzelner Social-Media-Link', 'upfront'),
			),
		);
		return $key ? (isset($l10n[$key]) ? $l10n[$key] : $key) : $l10n;
	}
}

class Upfront_SocialMedia_Setting {
	public static function get_globals() {
		$raw = Upfront_Cache_Utils::get_option('upfront_social_media_global_settings', false);
		if (!$raw) return false;
		return self::properties_to_array(json_decode($raw));
	}

	public static function properties_to_array($properties) {
		$output = array();
		if (is_array($properties)) foreach ($properties as $property) {
			if (isset($property->name)) $output[$property->name] = $property->value;
		}
		return $output;
	}

	public static function add_post_filters() {
		$globals = self::get_globals();
		if (!$globals || empty($globals['inpost']) || empty($globals['services'])) return;
		if (!empty($globals['after_title'])) add_filter('the_content', array(__CLASS__, 'prepend_social'));
		if (!empty($globals['after_content'])) add_filter('the_content', array(__CLASS__, 'append_social'));
	}

	public static function prepend_social($content) {
		return self::global_markup('after_title_align') . $content;
	}

	public static function append_social($content) {
		return $content . self::global_markup('after_content_align');
	}

	private static function global_markup($alignment_key) {
		$globals = self::get_globals();
		$alignment = !empty($globals[$alignment_key]) ? $globals[$alignment_key] : 'left';
		return '<div class="upfront-social usocial-inpost usocial-inpost-' . esc_attr($alignment) . '">' . Upfront_SocialMediaView::render_services($globals['services'], 'share') . '</div>';
	}
}

function upfront_social($data) {
	$data['social'] = array('settings' => Upfront_Cache_Utils::get_option('upfront_social_media_global_settings'));
	return $data;
}
add_filter('upfront_data', 'upfront_social');
