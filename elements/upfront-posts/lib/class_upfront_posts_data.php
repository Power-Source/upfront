<?php

class Upfront_Posts_PostsData {

	/**
	 * Fetch all default values for properties.
	 * @return array Default element properties
	 */
	public static function get_defaults () {
		static $defaults;
		if (!empty($defaults)) return $defaults;

		$default_parts = Upfront_Posts_PostView::get_default_parts();
		$default_parts = apply_filters('upfront_posts-defaults-default_parts', $default_parts);

		// Enabled parts are a subset of default ones
		$enabled_parts = $default_parts;
		$meta = array_search('meta', $enabled_parts);
		if (false !== $meta) {
			unset($enabled_parts[$meta]);
		}
		$enabled_parts = apply_filters('upfront_posts-defaults-enabled_parts', $enabled_parts);

		$defaults = array(
			'type' => 'PostsModel',
			'view_class' => 'PostsView',
			'has_settings' => 1,
			'class' => 'c24 uposts-object',
			'id_slug' => 'posts',

			'display_type' => '', // single, list or '' (initial)
			'list_type' => 'generic', // custom, taxonomy or generic

			// list_type===taxonomy settings
			'offset' => 1, // NR freshest
			'taxonomy' => '', // taxonomy
			'term' => '', // term
			'content' => 'excerpt', // excerpt or content
			'limit' => 5, // Only applicable if 'display_type' <> 'single'
			'pagination' => '', // '' (none), 'numeric', 'arrows' - only applicable if 'display_type' <> 'single'
			'sticky' => '', // '' (default - as normal posts), 'exclude', 'prepend'

			// list_type===custom settings
			'posts_list' => '', // JSON map of id/permalink pairs

			'thumbnail_size' => 'large', // thumbnail, medium, large, uf_post_featured_image, uf_custom_thumbnail_size
			'custom_thumbnail_width' => 200,
			'custom_thumbnail_height' => 200,

			// Post parts
			'post_parts' => $enabled_parts,
			'enabled_post_parts' => $enabled_parts,

			// These are the default ones
			'default_parts' => $default_parts,

			// Part options
			'date_posted_format' => get_option('date_format') . ' ' . get_option('time_format'),
			'categories_limit' => 3,
			'tags_limit' => 3,
			'comment_count_hide' => 0,
			'content_length' => 0,
			'resize_featured' => '1',
			'gravatar_size' => 200,

			// Parts markup goes here
			'preset' => 'default'
		);

		foreach ($default_parts as $part) {
			$key = self::_slug_to_part_key($part);
			$defaults[$key] = self::get_template($part);
		}

		return $defaults;
	}

	/**
	 * Slug sanitization utility method
	 * @param  string $slug Raw slug
	 * @return string Normalized slug
	 */
	private static function _get_normalized_slug ($slug) {
		$slug = preg_replace('/[^-_a-z0-9]/i', '', $slug);
		return $slug;
	}

	/**
	 * Part key creation utility method
	 * @param  string $slug Raw slug
	 * @return string Finished part name
	 */
	private static function _slug_to_part_key ($slug) {
		$slug = self::_get_normalized_slug($slug);
		return "post-part-{$slug}";
	}

	/**
	 * Template fetching method.
	 * The appropriate element template property is checked first and used if present.
	 * Otherwise, we load up stuff from theme template if present, or fall back to default.
	 * @param  string $slug Raw slug for template resolution
	 * @param  array  $data Raw data (element properties)
	 * @return string Template contents
	 */
	public static function get_template ($slug, $data=array()) {
		$slug = self::_get_normalized_slug($slug);

		$data_key = self::_slug_to_part_key($slug);
		if (!empty($data) && isset($data[$data_key])) return $data[$data_key];

		return upfront_get_template("posts-{$slug}", $data, dirname(dirname(__FILE__)) . '/tpl/parts/posts-' . $slug . '.php');
	}

	/**
	 * Fetch one of the default values.
	 * @param  string $key Property key to look for
	 * @param  mixed $fallback Fallback value if the key hasn't been found. Defaults to (bool)false
	 * @return mixed Found value or $fallback
	 */
	public static function get_default ($key, $fallback=false) {
		$defaults = self::get_defaults();
		return isset($defaults[$key])
			? $defaults[$key]
			: $fallback
		;
	}

	public static function add_js_defaults ($data) {
		if (!empty($data['upfront_posts'])) return $data;

		$data['upfront_posts'] = self::get_defaults();

		return $data;
	}

	public static function add_l10n_strings ($strings) {
		if (!empty($strings['posts_element'])) return $strings;
		$strings['posts_element'] = self::_get_l10n();
		return $strings;
	}

	private static function _get_l10n ($key=false) {
		$l10n = array(
			'element_name' => __('Posts', 'upfront'),
			'loading' => __('Laden', 'upfront'),
			'select_tax' => __('Bitte wähle eine Taxonomie', 'upfront'),
			'posts_settings' => __('Beitragseinstellungen', 'upfront'),
			'taxonomy' => __('Neuesten von', 'upfront'),
			'term' => __('nach Laufzeit', 'upfront'),
			'error' => __('Hoppla, etwas ist schief gelaufen', 'upfront'),
			'single_post' => __('Einzelbeitrag', 'upfront'),
			'post_list' => __('Beitragsliste', 'upfront'),
			'continue' => __('Weiter', 'upfront'),
			'general' => __('Allgemein', 'upfront'),
			'query_settings' => __('Query Einstellungen', 'upfront'),
			'post_parts' => __('Beitragsteile', 'upfront'),
			'display_type_label' => __('Was anzeigen:', 'upfront'),
			'display_type_label_initial' => __('Was soll das Element anzeigen?', 'upfront'),
			'list_type_label' => __('Wie sollen die Beiträge angezeigt werden:', 'upfront'),
			'post_list_custom' => __('Benutzerdefinierte Beiträge', 'upfront'),
			'post_list_tax' => __('Beiträge nach Taxonomie', 'upfront'),
			'post_list_generic' => __('Generisch', 'upfront'),
			'post_type' => __('Beitragstyp', 'upfront'),
			'offset' => __('Nr.', 'upfront'),
			'result_length' => __('Ergebnislänge', 'upfront'),
			'excerpt' => __('Auszug', 'upfront'),
			'full_post' => __('Vollständiger Beitrag', 'upfront'),
			'limit' => __('Begrenzen auf', 'upfront'),
			'pagination' => __('Seitennavigation:', 'upfront'),
			'none' => __('Keine', 'upfront'),
			'prev_next' => __('Vorherige / Nächste Seite', 'upfront'),
			'numeric' => __('Numerisch', 'upfront'),
			'post_parts_picker' => __('Wähle Beitragsteile zum Anzeigen', 'upfront'),
			'post_parts_sorter' => __('Zum Neuanordnen der Beitragsbestandteile ziehen', 'upfront'),
			'select_custom_post' => __('Wähle benutzerdefinierten Beitrag', 'upfront'),
			'add_custom_post' => __('Füge einen benutzerdefinierten Beitrag hinzu', 'upfront'),
			'resize_featured' => __('Beitragsbild an die Größe des Containers anpassen', 'upfront'),
			'general_settings' => __('Allgemeine Einstellungen', 'upfront'),
			'post_part_settings' => __('Beitragsteile Einstellungen', 'upfront'),

			'css' => array(
				'container_label' => __('Element Container', 'upfront'),
				'container_info' => __('Der Container für alle Beiträge', 'upfront'),
				'post_label' => __('Einzelner Beitrag', 'upfront'),
				'post_info' => __('Der Container für jeden einzelnen Beitrag', 'upfront'),
				'post_part_label' => __('Beitragsteil', 'upfront'),
				'post_part_info' => __('Allgemeiner Beitragsteil-Selektor', 'upfront'),
				'date_label' => __('Veröffentlicht am', 'upfront'),
				'date_info' => __('Teil des Veröffentlichungsdatums', 'upfront'),
				'author_label' => __('Autor', 'upfront'),
				'author_info' => __('Autorenteil', 'upfront'),
				'categories_label' => __('Kategorien', 'upfront'),
				'categories_info' => __('Teil der Beitragskategorieliste', 'upfront'),
				'comment_count_label' => __('Anzahl der Kommentare', 'upfront'),
				'comment_count_info' => __('Teil der Kommentaranzahl', 'upfront'),
				'content_label' => __('Inhalt', 'upfront'),
				'content_info' => __('Hauptinhaltsteil', 'upfront'),
				'gravatar_label' => __('Gravatar', 'upfront'),
				'gravatar_info' => __('Teil des Autoren-Gravatars', 'upfront'),
				'read_more_label' => __('Weiterlesen', 'upfront'),
				'read_more_info' => __('Teil des Weiterlesen-Buttons', 'upfront'),
				'post_tags_label' => __('Schlagwörter', 'upfront'),
				'post_tags_info' => __('Teil der Beitragsschlagwörter', 'upfront'),
				'thumbnail_label' => __('Miniaturansicht', 'upfront'),
				'thumbnail_info' => __('Teil des Beitragsbildes', 'upfront'),
				'title_label' => __('Titel', 'upfront'),
				'title_info' => __('Teil des Beitragstitels', 'upfront'),
			),

			'part_date_posted' => __('Veröffentlichungsdatum', 'upfront'),
			'part_author' => __('Autor', 'upfront'),
			'part_gravatar' => __('Gravatar', 'upfront'),
			'part_comment_count' => __('Anzahl der Kommentare', 'upfront'),
			'part_featured_image' => __('Beitragsbild', 'upfront'),
			'part_title' => __('Titel', 'upfront'),
			'part_content' => __('Inhalt', 'upfront'),
			'part_read_more' => __('Weiterlesen', 'upfront'),
			'part_tags' => __('Schlagwörter', 'upfront'),
			'part_categories' => __('Kategorien', 'upfront'),
			'part_meta' => __('Meta', 'upfront'),

			'edit' => __('Bearbeiten', 'upfront'),
			'edit_html' => __('HTML bearbeiten', 'upfront'),
			'format' => __('Format', 'upfront'),
			'max_categories' => __('max. Kategorien', 'upfront'),
			'max_tags' => __('max. Schlagwörter', 'upfront'),
			'hide_comments' => __('Ausblenden, wenn keine Kommentare', 'upfront'),
			'limit_words' => __('Wörter begrenzen', 'upfront'),
			'resize_to_fit' => __('An Containergröße anpassen', 'upfront'),
			'size_px' => __('Größe in px', 'upfront'),

			'meta_insert' => __('Metafeld einfügen', 'upfront'),
			'meta_toggle' => __('Versteckte Felder ausblenden', 'upfront'),
			'meta_fields' => __('Verfügbare Meta-Felder', 'upfront'),
			
			'sticky_posts' => __('Sticky-Beiträge', 'upfront'),
			'sticky_ignore' => __('Sticky-Beiträge ignorieren', 'upfront'),
			'sticky_prepend' => __('Sticky-Beiträge voranstellen', 'upfront'),
			'sticky_exclude' => __('Sticky-Beiträge ausschließen', 'upfront'),
			'thumbnail_size' => __('Thumbnail-Größe', 'upfront'),
			'thumbnail_size_thumbnail' => __('Thumbnail', 'upfront'),
			'thumbnail_size_medium' => __('Medium', 'upfront'),
			'thumbnail_size_large' => __('Large', 'upfront'),
			'thumbnail_size_post_feature' => __('Beitragsbild', 'upfront'),
			'thumbnail_size_custom' => __('Benutzerdefiniert <em>(bestehende nicht betroffen)</em>', 'upfront'),
			'thumbnail_size_custom_width' => __('Benutzerdefinierte Breite in px', 'upfront'),
			'thumbnail_size_custom_height' => __('Benutzerdefinierte Höhe in px', 'upfront'),
		);
		return !empty($key)
			? (!empty($l10n[$key]) ? $l10n[$key] : $key)
			: $l10n
		;
	}
}