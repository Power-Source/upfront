<?php

class Upfront_Admin_Theme_Catalog {
	const ORGANIZATION = 'Power-Source';
	const CACHE_KEY = 'upfront_theme_catalog_v1';
	const CACHE_TTL = 43200;

	private static $_instance;
	private $_installing_slug = '';

	public static function get_instance() {
		if (!self::$_instance) self::$_instance = new self();
		return self::$_instance;
	}

	private function __construct() {
		add_action('admin_post_upfront_install_catalog_theme', array($this, 'install_theme'));
		add_filter('upfront_theme_catalog_screenshot', array($this, 'get_theme_screenshot'), 10, 2);
	}

	public function get_theme_screenshot($screenshot, $slug) {
		if ($screenshot || !$this->_is_slug($slug)) return $screenshot;

		$catalog = $this->_get_catalog();
		if (!is_array($catalog)) return $screenshot;
		foreach ($catalog as $theme) {
			if ($theme['slug'] === $slug && !empty($theme['screenshot'])) return $theme['screenshot'];
		}
		return $screenshot;
	}

	public function render() {
		$catalog = $this->_get_catalog();
		$notice = $this->_get_notice();
		?>
		<div class="postbox-container upfront-theme-catalog">
			<div class="postbox">
				<h2 class="title"><?php esc_html_e('Upfront Theme-Katalog', 'upfront'); ?></h2>
				<div class="inside">
					<?php if ($notice) { ?>
						<div class="notice notice-<?php echo esc_attr($notice['type']); ?> inline"><p><?php echo esc_html($notice['message']); ?></p></div>
					<?php } ?>
					<p class="description"><?php esc_html_e('Entdecke freigegebene Upfront-Child-Themes von PSOURCE und installiere sie direkt.', 'upfront'); ?></p>
					<?php if (is_wp_error($catalog)) { ?>
						<div class="notice notice-error inline"><p><?php echo esc_html($catalog->get_error_message()); ?></p></div>
					<?php } elseif (empty($catalog)) { ?>
						<p><?php esc_html_e('Aktuell sind keine installierbaren Upfront-Themes verfügbar.', 'upfront'); ?></p>
					<?php } else { ?>
						<div class="upfront-theme-catalog-grid">
							<?php foreach ($catalog as $theme) $this->_render_theme($theme); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function _render_theme($theme) {
		$installed = wp_get_theme($theme['slug'])->exists();
		?>
		<article class="upfront-theme-catalog-item">
			<div class="upfront-theme-catalog-screenshot">
				<?php if ($theme['screenshot']) { ?>
					<img src="<?php echo esc_url($theme['screenshot']); ?>" alt="<?php echo esc_attr($theme['name']); ?>">
				<?php } else { ?>
					<span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span>
				<?php } ?>
			</div>
			<div class="upfront-theme-catalog-content">
				<h3><?php echo esc_html($theme['name']); ?></h3>
				<p class="upfront-theme-catalog-meta"><?php echo esc_html(sprintf(__('Version %1$s von %2$s', 'upfront'), $theme['version'], $theme['author'])); ?></p>
				<p><?php echo esc_html($theme['description']); ?></p>
				<div class="upfront-theme-catalog-actions">
					<?php if ($installed) { ?>
						<span class="upfront-theme-installed"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e('Installiert', 'upfront'); ?></span>
					<?php } elseif (current_user_can('install_themes')) { ?>
						<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
							<input type="hidden" name="action" value="upfront_install_catalog_theme">
							<input type="hidden" name="theme" value="<?php echo esc_attr($theme['slug']); ?>">
							<input type="hidden" name="commit" value="<?php echo esc_attr($theme['commit']); ?>">
							<?php wp_nonce_field('upfront_install_catalog_theme_' . $theme['slug']); ?>
							<button type="submit" class="button button-primary"><?php esc_html_e('Installieren', 'upfront'); ?></button>
						</form>
					<?php } ?>
					<?php if ($theme['theme_uri']) { ?>
						<a class="button" href="<?php echo esc_url($theme['theme_uri']); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Details', 'upfront'); ?></a>
					<?php } ?>
				</div>
			</div>
		</article>
		<?php
	}

	private function _get_catalog() {
		$cached = get_transient(self::CACHE_KEY);
		if (is_array($cached)) return $cached;

		$repositories = $this->_get_json('https://api.github.com/orgs/' . self::ORGANIZATION . '/repos?type=public&per_page=100&sort=updated');
		if (is_wp_error($repositories)) return $repositories;

		$catalog = array();
		foreach ($repositories as $repository) {
			if (!$this->_is_catalog_repository($repository)) continue;

			$slug = $repository['name'];
			$commit_data = $this->_get_json(sprintf(
				'https://api.github.com/repos/%s/%s/commits/%s',
				rawurlencode(self::ORGANIZATION),
				rawurlencode($slug),
				rawurlencode($repository['default_branch'])
			));
			if (is_wp_error($commit_data) || empty($commit_data['sha']) || !$this->_is_commit($commit_data['sha'])) continue;

			$commit = $commit_data['sha'];
			$headers = $this->_get_remote_headers($slug, $commit);
			if (is_wp_error($headers) || !$this->_is_upfront_theme($headers)) continue;

			$catalog[] = array(
				'slug' => $slug,
				'commit' => $commit,
				'name' => $headers['Name'],
				'description' => $headers['Description'],
				'version' => $headers['Version'] ?: __('Unbekannt', 'upfront'),
				'author' => $headers['Author'] ?: self::ORGANIZATION,
				'theme_uri' => esc_url_raw($headers['ThemeURI'] ?: $repository['html_url']),
				'screenshot' => $this->_cache_screenshot($slug, $commit),
			);
		}

		usort($catalog, function($first, $second) {
			return strcasecmp($first['name'], $second['name']);
		});
		set_transient(self::CACHE_KEY, $catalog, self::CACHE_TTL);
		return $catalog;
	}

	private function _is_catalog_repository($repository) {
		if (!is_array($repository) || !empty($repository['archived']) || empty($repository['name']) || empty($repository['default_branch'])) return false;
		if (empty($repository['owner']['login']) || self::ORGANIZATION !== $repository['owner']['login']) return false;
		return (bool) preg_match('/^uf-[a-z0-9-]+$/', $repository['name']);
	}

	private function _get_json($url) {
		$response = $this->_request($url, 1048576, 2);
		if (is_wp_error($response)) return $response;

		$data = json_decode(wp_remote_retrieve_body($response), true);
		if (!is_array($data)) return new WP_Error('upfront_catalog_invalid_json', __('Der Theme-Katalog hat ungültige Daten geliefert.', 'upfront'));
		return $data;
	}

	private function _get_remote_headers($slug, $commit) {
		if (!$this->_is_slug($slug) || !$this->_is_commit($commit)) return new WP_Error('upfront_catalog_invalid_theme', __('Ungültiges Theme.', 'upfront'));

		$url = sprintf('https://raw.githubusercontent.com/%s/%s/%s/style.css', self::ORGANIZATION, $slug, $commit);
		$response = $this->_request($url, 16384, 0);
		if (is_wp_error($response)) return $response;

		return $this->_parse_headers(wp_remote_retrieve_body($response));
	}

	private function _parse_headers($stylesheet) {
		$fields = array(
			'Name' => 'Theme Name',
			'Description' => 'Description',
			'Version' => 'Version',
			'Template' => 'Template',
			'Author' => 'Author',
			'ThemeURI' => 'Theme URI',
		);
		$headers = array();
		foreach ($fields as $key => $field) {
			$headers[$key] = preg_match('/^[ \t\/*#@]*' . preg_quote($field, '/') . ':\s*(.+)$/mi', $stylesheet, $match)
				? sanitize_text_field(trim($match[1]))
				: '';
		}
		return $headers;
	}

	private function _is_upfront_theme($headers) {
		return is_array($headers) && !empty($headers['Name']) && 'upfront' === strtolower($headers['Template']);
	}

	private function _request($url, $limit, $redirects) {
		$response = wp_safe_remote_get($url, array(
			'timeout' => 15,
			'redirection' => $redirects,
			'limit_response_size' => $limit,
			'headers' => array(
				'Accept' => 'application/vnd.github+json',
				'User-Agent' => 'Upfront-Theme-Catalog',
			),
		));
		if (is_wp_error($response)) return $response;
		if (200 !== wp_remote_retrieve_response_code($response)) return new WP_Error('upfront_catalog_http_error', __('Der Theme-Katalog ist derzeit nicht erreichbar.', 'upfront'));
		return $response;
	}

	private function _cache_screenshot($slug, $commit) {
		$uploads = wp_upload_dir();
		if (!empty($uploads['error'])) return '';

		$directory = trailingslashit($uploads['basedir']) . 'upfront-theme-catalog';
		$url_base = trailingslashit($uploads['baseurl']) . 'upfront-theme-catalog';
		if (!wp_mkdir_p($directory)) return '';

		foreach (array('png', 'jpg', 'jpeg', 'webp') as $extension) {
			$filename = $slug . '-' . $commit . '.' . $extension;
			$path = trailingslashit($directory) . $filename;
			if (is_readable($path)) return trailingslashit($url_base) . rawurlencode($filename);

			$url = sprintf('https://raw.githubusercontent.com/%s/%s/%s/screenshot.%s', self::ORGANIZATION, $slug, $commit, $extension);
			$response = $this->_request($url, 2097152, 0);
			if (is_wp_error($response)) continue;

			$image = wp_remote_retrieve_body($response);
			$info = function_exists('getimagesizefromstring') ? @getimagesizefromstring($image) : false;
			$types = array('image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp');
			if (!$info || empty($types[$info['mime']])) continue;

			$filename = $slug . '-' . $commit . '.' . $types[$info['mime']];
			$path = trailingslashit($directory) . $filename;
			if (false !== file_put_contents($path, $image, LOCK_EX)) return trailingslashit($url_base) . rawurlencode($filename);
		}
		return '';
	}

	public function install_theme() {
		if (!current_user_can('install_themes')) wp_die(
			esc_html__('Du darfst keine Themes installieren.', 'upfront'),
			esc_html__('Zugriff verweigert', 'upfront'),
			array('response' => 403)
		);

		$slug = isset($_POST['theme']) ? sanitize_key(wp_unslash($_POST['theme'])) : '';
		$commit = isset($_POST['commit']) ? sanitize_text_field(wp_unslash($_POST['commit'])) : '';
		check_admin_referer('upfront_install_catalog_theme_' . $slug);

		if (!$this->_is_slug($slug) || !$this->_is_commit($commit)) $this->_redirect('error', __('Ungültige Installationsanfrage.', 'upfront'));
		$theme = $this->_find_catalog_theme($slug, $commit);
		if (!$theme) $this->_redirect('error', __('Dieses Theme ist nicht im freigegebenen Katalog enthalten.', 'upfront'));
		if (wp_get_theme($slug)->exists()) $this->_redirect('warning', __('Dieses Theme ist bereits installiert.', 'upfront'));

		$headers = $this->_get_remote_headers($slug, $commit);
		if (is_wp_error($headers) || !$this->_is_upfront_theme($headers)) $this->_redirect('error', __('Das Paket ist kein gültiges Upfront-Child-Theme.', 'upfront'));

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$this->_installing_slug = $slug;
		add_filter('upgrader_source_selection', array($this, 'validate_source'), 10, 4);
		$upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());
		$result = $upgrader->install(sprintf('https://codeload.github.com/%s/%s/zip/%s', self::ORGANIZATION, $slug, $commit));
		remove_filter('upgrader_source_selection', array($this, 'validate_source'), 10);
		$this->_installing_slug = '';

		if (is_wp_error($result)) $this->_redirect('error', $result->get_error_message());
		if (!$result || !wp_get_theme($slug)->exists()) $this->_redirect('error', __('Das Theme konnte nicht installiert werden.', 'upfront'));
		$this->_redirect('success', sprintf(__('%s wurde erfolgreich installiert.', 'upfront'), $theme['name']));
	}

	private function _find_catalog_theme($slug, $commit) {
		$catalog = $this->_get_catalog();
		if (!is_array($catalog)) return false;
		foreach ($catalog as $theme) {
			if ($theme['slug'] === $slug && hash_equals($theme['commit'], $commit)) return $theme;
		}
		return false;
	}

	public function validate_source($source, $remote_source, $upgrader, $hook_extra) {
		if (!$this->_installing_slug) return $source;

		global $wp_filesystem;
		if (!$wp_filesystem || !$wp_filesystem->is_dir($source)) return new WP_Error('upfront_catalog_invalid_source', __('Das Theme-Paket konnte nicht geprüft werden.', 'upfront'));
		$stylesheet = trailingslashit($source) . 'style.css';
		if (!$wp_filesystem->is_file($stylesheet) || !$wp_filesystem->is_readable($stylesheet)) return new WP_Error('upfront_catalog_missing_stylesheet', __('Das Paket enthält keine style.css im Theme-Stammverzeichnis.', 'upfront'));
		$contents = $wp_filesystem->get_contents($stylesheet);
		if (false === $contents) return new WP_Error('upfront_catalog_unreadable_stylesheet', __('Die style.css des Themes konnte nicht gelesen werden.', 'upfront'));
		$headers = $this->_parse_headers($contents);
		if (!$this->_is_upfront_theme($headers)) return new WP_Error('upfront_catalog_invalid_parent', __('Das Paket ist kein gültiges Upfront-Child-Theme.', 'upfront'));

		$destination = trailingslashit($remote_source) . $this->_installing_slug;
		if ($wp_filesystem->exists($destination)) return new WP_Error('upfront_catalog_destination_exists', __('Das Theme-Verzeichnis existiert bereits.', 'upfront'));
		if (!$wp_filesystem->move($source, $destination, true)) return new WP_Error('upfront_catalog_move_failed', __('Das Theme-Paket konnte nicht vorbereitet werden.', 'upfront'));
		return trailingslashit($destination);
	}

	private function _is_slug($slug) {
		return (bool) preg_match('/^uf-[a-z0-9-]+$/', $slug);
	}

	private function _is_commit($commit) {
		return (bool) preg_match('/^[a-f0-9]{40}$/', $commit);
	}

	private function _redirect($type, $message) {
		set_transient('upfront_theme_catalog_notice_' . get_current_user_id(), array(
			'type' => sanitize_key($type),
			'message' => sanitize_text_field($message),
		), MINUTE_IN_SECONDS);
		wp_safe_redirect(admin_url('admin.php?page=' . Upfront_Admin::$menu_slugs['main']));
		exit;
	}

	private function _get_notice() {
		$key = 'upfront_theme_catalog_notice_' . get_current_user_id();
		$notice = get_transient($key);
		if (!is_array($notice) || empty($notice['message'])) return false;

		delete_transient($key);
		$types = array('success', 'warning', 'error', 'info');
		$notice['type'] = in_array($notice['type'], $types, true) ? $notice['type'] : 'info';
		return $notice;
	}
}