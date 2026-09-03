<?php

class Upfront_Admin_CodePen extends Upfront_Admin_Page {

	const COLLECTION_URL = 'https://codepen.io/collection/adZzzB';
	const INSPIRATION_COLLECTION_URL = 'https://codepen.io/collection/vOpwwd';
	const DEVELOPER_INFO_URL = 'https://psource.eimen.net/wiki/upfront-themes/upfront-theme-entwickler/';
	const MY_PENS_URL = 'https://codepen.io/your-work?search_term=uf-codepens';
	const SCHEMA = 'upfront-theme-style';
	const SCHEMA_VERSION = 1;

	private $hook_suffix = '';

	public function __construct () {
		if (!$this->_can_access()) return;

		$this->hook_suffix = add_submenu_page(
			Upfront_Admin::$menu_slugs['main'],
			__('Upfront zu CodePen', 'upfront'),
			__('CodePen-Styleguide', 'upfront'),
			'manage_options',
			Upfront_Admin::$menu_slugs['codepen'],
			array($this, 'render_page')
		);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	public static function register_ajax () {
		upfront_add_ajax('upfront_codepen_preview', array(__CLASS__, 'ajax_preview'));
		upfront_add_ajax('upfront_codepen_import', array(__CLASS__, 'ajax_import'));
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
			filemtime(Upfront::get_root_dir() . '/scripts/admin-codepen.js'),
			true
		);
		wp_localize_script('upfront-admin-codepen', 'Upfront_CodePen', array(
			'themeName' => $theme->get('Name'),
			'themeVersion' => $theme->get('Version'),
			'themeColors' => $this->decode_setting($theme_colors),
			'themeFonts' => $this->decode_setting($theme_fonts),
			'googleFontsStylesheetUrl' => Upfront_Local_Fonts_Server::get_endpoint_url(),
			'typography' => $this->decode_setting($typography),
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('upfront_codepen_import'),
			'schema' => self::SCHEMA,
			'schemaVersion' => self::SCHEMA_VERSION,
			'messages' => array(
				'checking' => __('Style wird geprüft …', 'upfront'),
				'importing' => __('Style wird importiert …', 'upfront'),
				'imported' => __('Der Theme-Style wurde importiert.', 'upfront'),
				'confirm' => __('Die ausgewählten Einstellungen des aktiven Themes werden ersetzt. Fortfahren?', 'upfront'),
				'requestFailed' => __('CodePen konnte nicht verarbeitet werden.', 'upfront'),
				'legacyPen' => __('Dieser Pen zeigt seinen Styleguide, übermittelt aber noch keine Importdaten. Erstelle ihn einmal mit dem aktuellen Upfront-Export neu.', 'upfront'),
				'colors' => __('Farben', 'upfront'),
				'noColors' => __('Keine Farben', 'upfront'),
				'fonts' => __('Schriften', 'upfront'),
				'noFonts' => __('Keine Schriften', 'upfront'),
				'typography' => __('Typografie', 'upfront'),
				'element' => __('Element', 'upfront'),
				'font' => __('Schrift', 'upfront'),
				'size' => __('Größe', 'upfront'),
				'color' => __('Farbe', 'upfront'),
				'noTypography' => __('Keine Typografie-Regeln', 'upfront'),
				'checkingShort' => __('Style wird geprüft ...', 'upfront'),
				'previewTitle' => __('CodePen-Style-Vorschau', 'upfront'),
				'noImportData' => __('Keine Importdaten verfügbar.', 'upfront'),
				'sampleHeading' => __('Überschrift', 'upfront'),
				'sampleLink' => __('Beispiel-Link', 'upfront'),
				'sampleText' => __('Beispieltext für die Typografie des aktiven Themes.', 'upfront'),
				'listItem' => __('Listeneintrag', 'upfront'),
				'quote' => __('Beispiel für ein Zitat.', 'upfront'),
				'alternativeQuote' => __('Alternatives Zitat.', 'upfront'),
			),
		));
		wp_add_inline_style('upfront_admin',
			'.upfront-codepen-header{display:flex;align-items:flex-start;justify-content:space-between;gap:32px;margin:20px 0;padding:24px 28px;border-left:4px solid #1abc9c;background:#fff;box-shadow:0 1px 2px rgba(36,57,77,.12)}' .
			'.upfront-codepen-header-main{max-width:760px}' .
			'.upfront_admin .upfront-codepen-header h1{margin:0 0 8px;padding:0}' .
			'.upfront-codepen-header-intro{max-width:680px;margin:0;color:#4d5963;font-size:15px;line-height:1.55}' .
			'.upfront-codepen-header-meta{display:flex;flex-wrap:wrap;gap:8px 22px;margin-top:14px;color:#66727c;font-size:12px}' .
			'.upfront-codepen-header-meta span{display:inline-flex;align-items:center;gap:5px}' .
			'.upfront-codepen-header-meta .dashicons{width:16px;height:16px;color:#159b80;font-size:16px;line-height:16px}' .
			'.upfront-codepen-header-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;min-width:330px}' .
			'.upfront-codepen-header-actions .button{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:38px;margin:0;padding:4px 12px;text-align:center}' .
			'.upfront-codepen-header-actions .button:last-child{grid-column:1/-1}' .
			'.upfront-codepen-header-actions .dashicons{width:17px;height:17px;font-size:17px;line-height:17px}' .
			'.upfront-codepen-comparison{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin:20px 0}' .
			'.upfront-codepen-column{min-width:0;border:1px solid #ddd;padding:16px;background:#fff}' .
			'.upfront-codepen-column h4{margin-top:0}' .
			'.upfront-codepen-swatches{display:grid;grid-template-columns:repeat(auto-fill,minmax(105px,1fr));gap:8px}' .
			'.upfront-codepen-swatch{display:flex;align-items:center;gap:7px;min-width:0}' .
			'.upfront-codepen-swatch span{width:28px;height:28px;flex:0 0 28px;border:1px solid #aaa}' .
			'.upfront-codepen-swatch code{overflow:hidden;text-overflow:ellipsis}' .
			'.upfront-codepen-values{width:100%;border-collapse:collapse}' .
			'.upfront-codepen-values th,.upfront-codepen-values td{padding:6px;border-bottom:1px solid #eee;text-align:left;vertical-align:top}' .
			'#upfront-codepen-render iframe{display:block;width:100%;height:430px;border:1px solid #ddd;margin-top:8px}' .
			'.upfront-codepen-gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px}' .
			'.upfront-codepen-gallery-item{min-width:0}' .
			'.upfront-codepen-gallery-item h3{margin:0 0 8px}' .
			'.upfront-codepen-gallery-item iframe{display:block;width:100%;height:360px;border:1px solid #ddd;background:#fff}' .
			'.upfront-codepen-gallery-empty{padding:24px;border:1px solid #ddd;background:#fff}' .
			'@media(max-width:900px){.upfront-codepen-header{flex-direction:column;gap:20px;padding:20px}.upfront-codepen-header-actions{width:100%;min-width:0}.upfront-codepen-comparison{grid-template-columns:1fr}}' .
			'@media(max-width:520px){.upfront-codepen-header-actions{grid-template-columns:1fr}.upfront-codepen-header-actions .button:last-child{grid-column:auto}}'
		);
	}

	public function render_page () {
		$theme = wp_get_theme();
		?>
		<div class="wrap upfront_admin upfront-codepen">
			<header class="upfront-codepen-header">
				<div class="upfront-codepen-header-main">
					<h1><?php esc_html_e('CodePen-Styleguide', 'upfront'); ?></h1>
					<p class="upfront-codepen-header-intro"><?php esc_html_e('Übertrage Farben, Schriften und Typografie zwischen Deinem aktiven Upfront-Theme und kompatiblen CodePens.', 'upfront'); ?></p>
					<div class="upfront-codepen-header-meta">
						<span><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span><?php esc_html_e('Aktives Theme', 'upfront'); ?>: <strong><?php echo esc_html($theme->get('Name')); ?></strong></span>
						<?php if ($theme->get('Version')) { ?>
							<span><span class="dashicons dashicons-tag" aria-hidden="true"></span><?php printf(esc_html__('Theme-Version %s', 'upfront'), esc_html($theme->get('Version'))); ?></span>
						<?php } ?>
						<span><span class="dashicons dashicons-editor-code" aria-hidden="true"></span><?php printf(esc_html__('Style-Schema v%s', 'upfront'), esc_html(self::SCHEMA_VERSION)); ?></span>
					</div>
				</div>
				<nav class="upfront-codepen-header-actions" aria-label="<?php esc_attr_e('Schnellzugriff', 'upfront'); ?>">
					<a class="button button-primary" href="#upfront-codepen-export-section"><span class="dashicons dashicons-upload" aria-hidden="true"></span><?php esc_html_e('Style exportieren', 'upfront'); ?></a>
					<a class="button" href="#upfront-codepen-import-section"><span class="dashicons dashicons-download" aria-hidden="true"></span><?php esc_html_e('Style importieren', 'upfront'); ?></a>
					<a class="button" href="<?php echo esc_url(self::COLLECTION_URL); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-external" aria-hidden="true"></span><?php esc_html_e('Offizielle Collection', 'upfront'); ?></a>
				</nav>
			</header>
			<div class="postbox-container">
				<div class="postbox" id="upfront-codepen-export-section">
					<h2 class="title"><?php esc_html_e('Theme-Stile exportieren', 'upfront'); ?></h2>
					<div class="inside">
						<p><?php esc_html_e('Erstellt einen neuen CodePen mit den Farben und Typografie-Einstellungen des aktiven Upfront-Themes.', 'upfront'); ?></p>
						<form id="upfront-codepen-form" action="https://codepen.io/pen/define" method="post" target="_blank">
							<input type="hidden" id="upfront-codepen-data" name="data" value="">
							<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Neuen Pen erstellen', 'upfront'); ?></button></p>
						</form>
					</div>
				</div>
				<div class="postbox" id="upfront-codepen-import-section">
					<h2 class="title"><?php esc_html_e('Theme-Stile finden und importieren', 'upfront'); ?></h2>
					<div class="inside">
						<ol>
							<li>
								<strong><?php esc_html_e('Theme-Style auswählen:', 'upfront'); ?></strong>
								<?php esc_html_e('Suche in der offiziellen UF-CodePens-Collection oder in Deinen eigenen Pens nach dem gewünschten Style.', 'upfront'); ?>
								<p>
									<a class="button button-primary" href="<?php echo esc_url(self::COLLECTION_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Offizielle Collection öffnen', 'upfront'); ?></a>
									<a class="button" href="<?php echo esc_url(self::MY_PENS_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Meine UF CodePens öffnen', 'upfront'); ?></a>
								</p>
							</li>
							<li>
								<strong><?php esc_html_e('Pen-URL kopieren:', 'upfront'); ?></strong>
								<?php esc_html_e('Öffne den gewünschten Pen und kopiere seine normale CodePen-Adresse aus der Browserzeile.', 'upfront'); ?>
							</li>
							<li>
								<strong><?php esc_html_e('Style prüfen und importieren:', 'upfront'); ?></strong>
								<?php esc_html_e('Füge die Pen-URL hier ein. Nach der Prüfung kannst Du Farben, Schriften und Typografie gezielt übernehmen.', 'upfront'); ?>
							</li>
						</ol>
						<form id="upfront-codepen-import-form">
							<label class="screen-reader-text" for="upfront-codepen-url"><?php esc_html_e('CodePen-URL', 'upfront'); ?></label>
							<input type="url" class="regular-text" id="upfront-codepen-url" placeholder="https://codepen.io/username/pen/AbCdEf" required>
							<button type="submit" class="button button-secondary"><?php esc_html_e('Style prüfen', 'upfront'); ?></button>
						</form>
						<div id="upfront-codepen-status" class="notice inline" hidden><p></p></div>
						<div class="upfront-codepen-comparison">
							<section class="upfront-codepen-column">
								<h4><?php esc_html_e('Aktives Upfront-Theme', 'upfront'); ?></h4>
								<div id="upfront-codepen-current-values"></div>
							</section>
							<section class="upfront-codepen-column">
								<h4><?php esc_html_e('Zu importierender CodePen-Style', 'upfront'); ?></h4>
								<div id="upfront-codepen-import-values"><p><?php esc_html_e('Noch kein Style geprüft.', 'upfront'); ?></p></div>
							</section>
						</div>
						<h4><?php esc_html_e('Gerenderte CodePen-Vorschau', 'upfront'); ?></h4>
						<div id="upfront-codepen-render"><p><?php esc_html_e('Füge oben eine Pen-URL ein, um die Vorschau zu laden.', 'upfront'); ?></p></div>
						<div id="upfront-codepen-preview" hidden>
							<h3 id="upfront-codepen-preview-title"></h3>
							<p id="upfront-codepen-preview-summary"></p>
							<p><strong><?php esc_html_e('Bis hierher wurde Dein aktives Theme nicht verändert.', 'upfront'); ?></strong></p>
							<fieldset>
								<legend><strong><?php esc_html_e('Was möchtest Du übernehmen?', 'upfront'); ?></strong></legend>
								<label><input type="checkbox" name="upfront-codepen-parts" value="colors" checked> <?php esc_html_e('Farben', 'upfront'); ?></label><br>
								<label><input type="checkbox" name="upfront-codepen-parts" value="fonts" checked> <?php esc_html_e('Schriften', 'upfront'); ?></label><br>
								<label><input type="checkbox" name="upfront-codepen-parts" value="typography" checked> <?php esc_html_e('Typografie', 'upfront'); ?></label>
							</fieldset>
							<p class="submit"><button type="button" id="upfront-codepen-import" class="button button-primary"><?php esc_html_e('Ausgewählte Werte übernehmen', 'upfront'); ?></button></p>
						</div>
					</div>
				</div>
				<div class="postbox upfront-codepen-inspiration">
					<h2 class="title"><?php esc_html_e('Hilfreiche CodePens', 'upfront'); ?></h2>
					<div class="inside">
						<?php $inspiration_pens = $this->get_inspiration_pens(); ?>
						<?php if (!empty($inspiration_pens)) { ?>
							<div class="upfront-codepen-gallery">
								<?php foreach ($inspiration_pens as $pen) { ?>
									<article class="upfront-codepen-gallery-item">
										<h3><?php echo esc_html($pen['title']); ?></h3>
										<iframe src="<?php echo esc_url('https://codepen.io/' . rawurlencode($pen['user']) . '/embed/' . rawurlencode($pen['slug']) . '?default-tab=result'); ?>" title="<?php echo esc_attr($pen['title']); ?>" loading="lazy" allowfullscreen></iframe>
									</article>
								<?php } ?>
							</div>
						<?php } else { ?>
							<div class="upfront-codepen-gallery-empty">
								<strong><?php esc_html_e('In dieser Collection sind aktuell noch keine Pens.', 'upfront'); ?></strong>
								<p><?php esc_html_e('Sobald hilfreiche Erweiterungen ausgewählt sind, erscheinen sie hier als direkt nutzbare Vorschau.', 'upfront'); ?></p>
							</div>
						<?php } ?>
						<p class="submit">
							<a class="button button-primary" href="<?php echo esc_url(self::INSPIRATION_COLLECTION_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Collection extern öffnen', 'upfront'); ?></a>
							<a class="button" href="<?php echo esc_url(self::DEVELOPER_INFO_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Eigenen CodePen einreichen und prüfen lassen', 'upfront'); ?></a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function ajax_preview () {
		self::verify_ajax_request();
		$payload = self::request_payload();
		if (is_wp_error($payload)) self::send_error($payload);

		wp_send_json_success(array(
			'payload' => $payload,
			'summary' => array(
				'colors' => count($payload['colors']['colors']),
				'fonts' => count($payload['fonts']),
				'typography' => count($payload['typography']),
			),
		));
	}

	public static function ajax_import () {
		self::verify_ajax_request();
		$payload = self::request_payload();
		if (is_wp_error($payload)) self::send_error($payload);

		$parts = isset($_POST['parts']) ? array_map('sanitize_key', (array) wp_unslash($_POST['parts'])) : array();
		$parts = array_intersect($parts, array('colors', 'fonts', 'typography'));
		if (empty($parts)) self::send_error(new WP_Error('upfront_codepen_no_parts', __('Wähle mindestens einen Bereich für den Import aus.', 'upfront')));

		$stylesheet = get_stylesheet();
		if (in_array('colors', $parts, true)) {
			Upfront_Cache_Utils::update_option('upfront_' . $stylesheet . '_theme_colors', wp_json_encode($payload['colors']));
		}
		if (in_array('fonts', $parts, true)) {
			Upfront_Cache_Utils::update_option('upfront_' . $stylesheet . '_theme_fonts', wp_json_encode($payload['fonts']));
		}
		if (in_array('typography', $parts, true)) {
			$layout_data = array('properties' => Upfront_Layout::get_layout_properties());
			upfront_set_property_value('typography', $payload['typography'], $layout_data);
			$option = Upfront_Layout::get_storage_key() . '-' . $stylesheet . '-layout-properties';
			Upfront_Cache_Utils::update_option($option, wp_json_encode($layout_data['properties']));
		}

		wp_send_json_success(array('parts' => array_values($parts)));
	}

	private static function verify_ajax_request () {
		if (!current_user_can('manage_options')) wp_send_json_error(array('message' => __('Du hast keine Berechtigung für diesen Import.', 'upfront')), 403);
		check_ajax_referer('upfront_codepen_import', 'nonce');
	}

	private static function request_payload () {
		if (isset($_POST['payload'])) {
			$payload = json_decode(wp_unslash($_POST['payload']), true);
			if (!is_array($payload)) return new WP_Error('upfront_codepen_invalid_json', __('Der Upfront-Style im Pen ist beschädigt.', 'upfront'));
			return self::validate_payload($payload);
		}

		return self::fetch_payload(isset($_POST['url']) ? wp_unslash($_POST['url']) : '');
	}

	private static function fetch_payload ($input) {
		$input = trim((string) $input);
		if (strlen($input) > 524288) return new WP_Error('upfront_codepen_source_too_large', __('Der eingefügte Code ist zu groß.', 'upfront'));

		if (false !== strpos($input, 'UPFRONT_THEME_STYLE')) {
			$source = $input;
		} else {
			$source_url = self::parse_pen_url($input);
			if (is_wp_error($source_url)) return $source_url;

			$response = wp_safe_remote_get($source_url, array(
				'timeout' => 15,
				'redirection' => 5,
				'limit_response_size' => 524288,
				'headers' => array('User-Agent' => 'Upfront-CodePen-Importer'),
			));
			if (is_wp_error($response)) return new WP_Error('upfront_codepen_request_failed', __('Der Pen ist nicht erreichbar oder nicht öffentlich.', 'upfront'));
			$status = wp_remote_retrieve_response_code($response);
			if (403 === $status && 'codepen.io' === wp_parse_url($source_url, PHP_URL_HOST)) {
				return new WP_Error('upfront_codepen_blocked', __('CodePen blockiert den direkten Abruf. Kopiere stattdessen den vollständigen Inhalt des JavaScript-Panels und füge ihn hier ein.', 'upfront'));
			}
			if (200 !== $status) return new WP_Error('upfront_codepen_http_error', __('Der Pen ist nicht erreichbar oder nicht öffentlich.', 'upfront'));

			$source = wp_remote_retrieve_body($response);
		}

		if (!preg_match('/\/\*\s*UPFRONT_THEME_STYLE\s*(\{.*\})\s*END_UPFRONT_THEME_STYLE\s*\*\//sU', $source, $matches)) {
			return new WP_Error('upfront_codepen_missing_data', __('Dieser Pen enthält keinen importierbaren Upfront-Style.', 'upfront'));
		}

		$payload = json_decode($matches[1], true);
		if (!is_array($payload)) return new WP_Error('upfront_codepen_invalid_json', __('Der Upfront-Style im Pen ist beschädigt.', 'upfront'));
		return self::validate_payload($payload);
	}

	public static function parse_pen_url ($url) {
		$url = esc_url_raw(trim((string) $url));
		$parts = wp_parse_url($url);
		if (empty($parts['scheme']) || 'https' !== strtolower($parts['scheme']) || empty($parts['host'])) {
			return new WP_Error('upfront_codepen_invalid_url', __('Bitte gib eine gültige öffentliche CodePen-URL ein.', 'upfront'));
		}

		$host = strtolower($parts['host']);
		$path = isset($parts['path']) ? $parts['path'] : '';
		if (preg_match('/^[a-z0-9-]+\.codepenusercontent\.com$/', $host) && preg_match('~^/[A-Za-z0-9_./-]+\.js$~', $path) && false === strpos($path, '..')) {
			return 'https://' . $host . $path;
		}

		if ('codepen.io' !== $host) {
			return new WP_Error('upfront_codepen_invalid_url', __('Bitte gib eine gültige öffentliche CodePen-URL ein.', 'upfront'));
		}

		if (!preg_match('~^/(?:editor/)?([A-Za-z0-9_-]+)/(?:(?:pen|details|full)/)([A-Za-z0-9-]+)~', $path, $matches)) {
			return new WP_Error('upfront_codepen_invalid_url', __('Die URL muss direkt auf einen CodePen verweisen.', 'upfront'));
		}

		return 'https://codepen.io/' . rawurlencode($matches[1]) . '/pen/' . rawurlencode($matches[2]) . '.js';
	}

	public static function validate_payload ($payload) {
		if (!isset($payload['schema'], $payload['version']) || self::SCHEMA !== $payload['schema'] || self::SCHEMA_VERSION !== (int) $payload['version']) {
			return new WP_Error('upfront_codepen_schema', __('Diese Upfront-Style-Version wird nicht unterstützt.', 'upfront'));
		}

		$name = isset($payload['name']) ? sanitize_text_field($payload['name']) : __('Upfront Theme Style', 'upfront');
		$colors = isset($payload['colors']['colors']) && is_array($payload['colors']['colors']) ? array_slice($payload['colors']['colors'], 0, 64) : array();
		$clean_colors = array();
		foreach ($colors as $color) {
			if (!is_array($color) || empty($color['color'])) continue;
			$value = self::clean_style_value($color['color']);
			if ('' !== $value) $clean_colors[] = array('color' => $value);
		}

		$clean_fonts = array();
		$fonts = isset($payload['fonts']) && is_array($payload['fonts']) ? array_slice($payload['fonts'], 0, 64) : array();
		foreach ($fonts as $font) {
			if (!is_array($font)) continue;
			$clean_font = self::sanitize_tree($font, 3);
			if (!empty($clean_font)) $clean_fonts[] = $clean_font;
		}

		$allowed_selectors = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'a', 'a:hover', 'ul', 'ol', 'blockquote', 'blockquote.upfront-quote-alternative');
		$allowed_rules = array('color', 'font_face', 'font_family', 'line_height', 'size', 'style', 'weight', 'theme_color_class');
		$clean_typography = array();
		$typography = isset($payload['typography']) && is_array($payload['typography']) ? $payload['typography'] : array();
		foreach ($allowed_selectors as $selector) {
			if (empty($typography[$selector]) || !is_array($typography[$selector])) continue;
			foreach ($allowed_rules as $rule) {
				if (!isset($typography[$selector][$rule]) || !is_scalar($typography[$selector][$rule])) continue;
				$value = self::clean_style_value($typography[$selector][$rule]);
				if ('' !== $value) $clean_typography[$selector][$rule] = $value;
			}
		}

		return array(
			'schema' => self::SCHEMA,
			'version' => self::SCHEMA_VERSION,
			'name' => $name,
			'colors' => array('colors' => $clean_colors, 'range' => isset($payload['colors']['range']) ? absint($payload['colors']['range']) : 0),
			'fonts' => $clean_fonts,
			'typography' => $clean_typography,
		);
	}

	private static function clean_style_value ($value) {
		$value = sanitize_text_field((string) $value);
		if (preg_match('/[;{}\x00-\x1F]/', $value)) return '';
		return substr($value, 0, 200);
	}

	private static function sanitize_tree ($value, $depth) {
		if ($depth < 1) return array();
		$clean = array();
		foreach ($value as $key => $item) {
			$key = sanitize_key($key);
			if (is_array($item)) $clean[$key] = self::sanitize_tree($item, $depth - 1);
			else if (is_scalar($item)) $clean[$key] = self::clean_style_value($item);
		}
		return $clean;
	}

	private static function send_error ($error) {
		wp_send_json_error(array('message' => $error->get_error_message()));
	}

	private function get_inspiration_pens () {
		$pens = apply_filters('upfront_codepen_inspiration_pens', array());
		$valid = array();
		foreach ((array) $pens as $pen) {
			if (empty($pen['user']) || empty($pen['slug'])) continue;
			$valid[] = array(
				'user' => sanitize_key($pen['user']),
				'slug' => preg_replace('/[^A-Za-z0-9-]/', '', $pen['slug']),
				'title' => !empty($pen['title']) ? sanitize_text_field($pen['title']) : __('CodePen-Inspiration', 'upfront'),
			);
		}
		return $valid;
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