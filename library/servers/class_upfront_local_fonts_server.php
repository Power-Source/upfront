<?php

class Upfront_Local_Fonts_Server extends Upfront_Server {

	const ACTION = 'upfront_local_fonts_css';
	const CACHE_DIRECTORY = 'upfront-fonts';
	const CACHE_VERSION = '1';
	const UNUSED_CACHE_LIFETIME = 2592000;

	public static function serve () {
		$me = new self;
		upfront_add_ajax(self::ACTION, array($me, 'output_css'));
		upfront_add_ajax_nopriv(self::ACTION, array($me, 'output_css'));
	}

	public static function get_endpoint_url () {
		$url = add_query_arg(array(
			'action' => self::ACTION,
			'ufv' => self::CACHE_VERSION
		), admin_url('admin-ajax.php'));
		return apply_filters('upfront_google_fonts_stylesheet_url', $url);
	}

	public static function sync_theme_fonts ($theme_fonts) {
		if (is_string($theme_fonts)) $theme_fonts = json_decode($theme_fonts, true);
		$families = array();
		foreach ((array)$theme_fonts as $theme_font) {
			$theme_font = (array)$theme_font;
			$font = isset($theme_font['font']) ? $theme_font['font'] : array();
			$font = (array)$font;
			$family = isset($font['family']) ? $font['family'] : '';
			$variant = isset($theme_font['variant']) ? $theme_font['variant'] : false;
			if (!$family) continue;
			if (!isset($families[$family])) $families[$family] = array();
			if ($variant) $families[$family][] = $variant;
		}

		foreach ($families as $family => $variants) {
			self::cache_family($family, $variants);
		}
		self::prune();
	}

	public function output_css () {
		$requests = self::parse_family_request(isset($_GET['family']) ? wp_unslash($_GET['family']) : '');
		$css = '';
		foreach ($requests as $family => $variants) {
			$family_css = self::cache_family($family, $variants);
			if ($family_css) $css .= $family_css . "\n";
		}

		status_header($css ? 200 : 404);
		header('Content-Type: text/css; charset=UTF-8');
		header('Cache-Control: public, max-age=86400');
		header('X-Content-Type-Options: nosniff');
		echo $css ? $css : '/* Requested fonts are unavailable; system fallbacks remain active. */';
		die;
	}

	private static function parse_family_request ($request) {
		$families = array();
		foreach (explode('|', (string)$request) as $font_request) {
			$parts = explode(':', trim($font_request), 2);
			$family = trim(str_replace('+', ' ', $parts[0]));
			if (!$family || !preg_match('/^[\p{L}\p{N} ._-]+$/u', $family)) continue;
			$variants = isset($parts[1]) ? array_filter(array_map('trim', explode(',', $parts[1]))) : array();
			$families[$family] = $variants;
		}
		return $families;
	}

	private static function cache_family ($family, $variants = array()) {
		$locations = self::get_cache_locations();
		if (!$locations) return false;

		$slug = strtolower(preg_replace('/[^a-z0-9]/i', '', $family));
		if (!$slug) return false;
		$directory = trailingslashit($locations['directory']) . $slug;
		$css_path = trailingslashit($directory) . 'font.css';
		if (is_readable($css_path)) {
			touch($css_path);
			return file_get_contents($css_path);
		}
		if (!wp_mkdir_p($directory)) return false;
		$lock = fopen(trailingslashit($directory) . '.cache.lock', 'c');
		if (!$lock || !flock($lock, LOCK_EX)) return false;
		if (is_readable($css_path)) {
			$css = file_get_contents($css_path);
			touch($css_path);
			flock($lock, LOCK_UN);
			fclose($lock);
			return $css;
		}

		$source = apply_filters('upfront_google_fonts_source_url', 'https://eimen.net/fonts/css.php');
		$request = $slug;
		if ($variants) $request .= ':' . join(',', array_unique($variants));
		$response = wp_safe_remote_get(add_query_arg('family', $request, $source), array('timeout' => 20));
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) return self::unlock($lock);
		$css = wp_remote_retrieve_body($response);
		if (false === strpos($css, '@font-face')) return self::unlock($lock);

		preg_match_all('/url\([\'\"]?([^\'\")]+)[\'\"]?\)/i', $css, $matches);
		foreach (array_unique($matches[1]) as $css_font_url) {
			$font_url = html_entity_decode($css_font_url, ENT_QUOTES, 'UTF-8');
			$filename = self::font_filename($font_url);
			if (!$filename) continue;
			$font_response = wp_safe_remote_get($font_url, array('timeout' => 30));
			if (is_wp_error($font_response) || 200 !== wp_remote_retrieve_response_code($font_response)) continue;
			$font_data = wp_remote_retrieve_body($font_response);
			if (!$font_data || strlen($font_data) > 20 * 1024 * 1024) continue;
			if (!self::write_file(trailingslashit($directory) . $filename, $font_data)) continue;
			$local_url = trailingslashit($locations['url']) . $slug . '/' . rawurlencode($filename);
			$css = str_replace($css_font_url, $local_url, $css);
		}

		$css = preg_replace('/font-family:\s*([\'\"])' . preg_quote($slug, '/') . '\1/i', "font-family: '" . str_replace("'", "\\'", $family) . "'", $css);
		$css = preg_replace_callback('/@font-face\s*\{.*?\}/is', array(__CLASS__, 'normalize_font_face'), $css);
		$css = preg_replace('/(@font-face\s*\{.*?)(\})/is', '$1    font-display: swap;' . "\n" . '$2', $css);
		preg_match_all('/url\([\'\"]?([^\'\")]+)[\'\"]?\)/i', $css, $local_matches);
		foreach ($local_matches[1] as $local_font_url) {
			if (0 !== strpos($local_font_url, trailingslashit($locations['url']))) return self::unlock($lock);
		}
		if (!self::write_file($css_path, $css)) return self::unlock($lock);
		self::unlock($lock);
		return $css;
	}

	private static function normalize_font_face ($match) {
		$block = $match[0];
		if (!preg_match('/url\([\'\"]?([^\'\")]+)[\'\"]?\)/i', $block, $url_match)) return $block;
		$filename = pathinfo((string)parse_url($url_match[1], PHP_URL_PATH), PATHINFO_FILENAME);
		if (preg_match('/(?:^|[-_])(wght|variable)(?:$|[-_])/i', $filename)) return $block;

		$weights = array(
			'thin' => 100,
			'extralight' => 200,
			'light' => 300,
			'medium' => 500,
			'semibold' => 600,
			'bold' => 700,
			'extrabold' => 800,
			'black' => 900
		);
		foreach ($weights as $name => $weight) {
			if (!preg_match('/(?:^|[-_])' . $name . '(?:italic)?$/i', $filename)) continue;
			return preg_replace('/font-weight:\s*[^;]+;/i', 'font-weight: ' . $weight . ';', $block);
		}
		return preg_replace('/font-weight:\s*[^;]+;/i', 'font-weight: 400;', $block);
	}

	private static function write_file ($path, $contents) {
		$temporary = $path . '.tmp-' . wp_generate_password(8, false);
		if (false === file_put_contents($temporary, $contents)) return false;
		if (@rename($temporary, $path)) return true;
		@unlink($temporary);
		return false;
	}

	private static function unlock ($lock) {
		flock($lock, LOCK_UN);
		fclose($lock);
		return false;
	}

	private static function font_filename ($url) {
		$query = array();
		parse_str((string)parse_url($url, PHP_URL_QUERY), $query);
		$filename = isset($query['file']) ? $query['file'] : basename((string)parse_url($url, PHP_URL_PATH));
		$filename = sanitize_file_name($filename);
		return preg_match('/\.(?:ttf|otf|woff2?|eot)$/i', $filename) ? $filename : false;
	}

	private static function get_cache_locations () {
		$uploads = wp_upload_dir();
		if (!empty($uploads['error'])) return false;
		return array(
			'directory' => trailingslashit($uploads['basedir']) . self::CACHE_DIRECTORY,
			'url' => trailingslashit($uploads['baseurl']) . self::CACHE_DIRECTORY
		);
	}

	private static function prune () {
		$locations = self::get_cache_locations();
		if (!$locations || !is_dir($locations['directory'])) return;
		foreach (glob(trailingslashit($locations['directory']) . '*', GLOB_ONLYDIR) as $directory) {
			$css_path = trailingslashit($directory) . 'font.css';
			$last_used = is_file($css_path) ? filemtime($css_path) : filemtime($directory);
			if ($last_used > time() - self::UNUSED_CACHE_LIFETIME) continue;
			$lock = fopen(trailingslashit($directory) . '.cache.lock', 'c');
			if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) continue;
			foreach (glob(trailingslashit($directory) . '*') as $file) {
				if (is_file($file)) @unlink($file);
			}
			flock($lock, LOCK_UN);
			fclose($lock);
		}
	}
}

Upfront_Local_Fonts_Server::serve();
