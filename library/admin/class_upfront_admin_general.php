<?php

class Upfront_Admin_General extends Upfront_Admin_Page {

	function __construct(){
		if ($this->_can_access( Upfront_Permissions::SEE_USE_DEBUG )) {
			add_submenu_page( Upfront_Admin::$menu_slugs['main'], __("Allgemeine Einstellungen", 'upfront'), __("Allgemein", 'upfront'), 'manage_options', Upfront_Admin::$menu_slugs['main'], array($this, "render_page") );
		}
	}

	public function render_page() {
		$core_version = $child_version = '0.0.0';
		$current = wp_get_theme();
		// Deal with caches
		if (class_exists('Upfront_Compat') && is_callable(array('Upfront_Compat', 'get_upfront_core_version')) && is_callable(array('Upfront_Compat', 'get_upfront_child_version'))) {
			$core_version = Upfront_Compat::get_upfront_core_version();
			$child_version = Upfront_Compat::get_upfront_child_version();

			$child_version = !empty($child_version)
				? $child_version
				: '0.0.0'
			;
		}
		?>
		<div class="wrap upfront_admin upfront-general-settings">
			<h1><?php esc_html_e("Allgemeine Einstellungen", 'upfront'); ?><span class="upfront_logo"></span></h1>
			<div class="upfront-col-left">
				<div class="postbox-container version-info">
					<div class='postbox'>
						<h2 class="title"><?php esc_html_e("Versions-Info", 'upfront') ?></h2>
						<div class="inside version-info">
							<div class="upfront-debug-block">
								Upfront <span>V <?php echo esc_html($core_version); ?></span>
							</div>
							<div class="upfront-debug-block">
								<?php echo esc_html(sprintf(__('%s (Aktives Theme)', 'upfront'), $current->Name)); ?>
									<span>V <?php echo esc_html($child_version); ?></span>
							</div>

							<?php do_action('upfront-admin-general_settings-versions'); ?>
						</div>
					</div>
				</div>
				<?php $this->_render_under_construction_box(); ?>
				<?php $this->_render_api_options(); ?>
				<?php $this->_render_response_cache_options(); ?>
				<?php $this->_render_debug_options(); ?>
				<?php $this->_render_changelog_box(); ?>
			</div>
			<div class="upfront-col-right">
				<?php Upfront_Admin_Theme_Catalog::get_instance()->render(); ?>
				<div class="postbox-container helpful-resources">
					<div class='postbox'>
						<h2 class="title"><?php esc_html_e("Hilfreiche Resourcen", 'upfront') ?></h2>
						<div class="inside">
							<div class="upfront-debug-block">
								<a target="_blank" href="https://psource.eimen.net/wiki/upfront-dokumentation/" class="documentation">Upfront Dokumentation</a> <a target="_blank" href="https://psource.eimen.net/wiki/upfront-dokumentation/upfront-builder-dokumentation/" class="documentation">Erstelle Upfront Themes</a> <a target="_blank" href="https://psource.eimen.net/wiki/psource-manager-dokumentation/" class="documentation">PSOURCE MANAGER</a>
							</div>
							<div class="upfront-debug-block">
								<h4><?php esc_html_e("UpFront Wiki", 'upfront') ?></h4>
								<ul>

									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/' target="_blank"><?php esc_html_e("Upfront 1.0", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-part-1-die-basics-themefarben-und-schriftarten/' target="_blank"><?php esc_html_e("Upfront Part 1: Die Basics, Themefarben und Schriftarten", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-2-strukturierung-deiner-webseite-mit-bereichen/' target="_blank"><?php esc_html_e("Upfront Teil 2: Strukturierung Deiner Webseite mit Bereichen", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-3-layout-deiner-webseite-mit-elementen/' target="_blank"><?php esc_html_e("Upfront Teil 3: Layout Deiner Webseite mit Elementen", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-4-elemente-mit-individuellem-code-anpassen/' target="_blank"><?php esc_html_e("Upfront Teil 4: Elemente mit individuellem Code anpassen", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-5-plugins-hinzufuegen-und-powerform-formulare-stylen/' target="_blank"><?php esc_html_e("Upfront Teil 5: Plugins hinzufügen und Powerform Formulare stylen", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-6-erstellung-von-responsiven-webseiten/' target="_blank"><?php esc_html_e("Upfront Teil 6: Erstellung von responsiven Webseiten", 'upfront') ?></a></li>
									<li><a href='https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-7-arbeiten-mit-seiten-und-beitraegen/' target="_blank"><?php esc_html_e("Upfront Teil 7: Arbeiten mit Seiten und Beiträgen", 'upfront') ?></a></li>
								</ul>
							</div>
							<div class="upfront-debug-block">
								<h4><?php _e("PSOURCE Hilfe", 'upfront') ?></h4>
								<a class="upfront_button visit-forum" href="https://psource.eimen.net/" target="_blank"><?php esc_html_e("Besuche unsere Webseite", 'upfront') ?></a> <a class="upfront_button" href="https://github.com/Power-Source/upfront" target="_blank"><?php esc_html_e("Hilf auf GitHub mit", 'upfront') ?></a> <a class="upfront_button find-themes" href="https://psource.eimen.net/ps-upfront-theme-framework/ps-upfront-themes/" target="_blank"><?php esc_html_e("FINDE UPFRONT THEMES", 'upfront') ?></a>
							</div>
							<div class="upfront-debug-block">
								<h4><?php esc_html_e('Theme-Werkzeuge', 'upfront'); ?></h4>
								<a class="upfront_button" href="<?php echo esc_url(admin_url('admin.php?page=' . Upfront_Admin::$menu_slugs['codepen'])); ?>"><?php esc_html_e('CodePen-Styleguide erstellen', 'upfront'); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

	}

	private function _render_api_options () {
		$admin_keys = new Upfront_Admin_ApiKeys;
		if (!$admin_keys->can_access()) return false;

		$services = $admin_keys->get_services();
		if (empty($services)) return false;

		$admin_keys->process_submissions();

		?>
<div class="postbox-container api_keys">
	<div class='postbox'>
		<h2 class="title"><?php esc_html_e("API Schlüssel", 'upfront') ?></h2>
		<div class="inside api_keys">
			<form method="POST">
				<?php
				foreach ($services as $service => $label) {
					$admin_keys->render_key_box($service);
				}
				$admin_keys->render_footer();
				?>
				<p>
					<button class="upfront_button">
						<?php esc_html_e('Speichern', 'upfront'); ?>
					</button>
				</p>
			</form>
		</div>
	</div>
</div>
		<?php
	}

	private function _render_response_cache_options () {
		$admin = new Upfront_Admin_ResponseCache;
		if (!$admin->can_access()) return false;

		$levels = $admin->get_levels();
		if (empty($levels)) return false;

		$admin->process_submissions();

		?>
<div class="postbox-container response_caching">
	<div class='postbox'>
		<h2 class="title"><?php esc_html_e("Anforderungswarteschlange und Caching-Strategie", 'upfront') ?></h2>
		<div class="inside api_keys">
			<form method="POST">
				<?php
				foreach ($levels as $level => $label) {
					$admin->render_level_box($level);
				}
				$admin->render_footer();
				?>
				<p>
					<button class="upfront_button">
						<?php esc_html_e('Speichern', 'upfront'); ?>
					</button>
				</p>
			</form>
		</div>
	</div>
</div>
		<?php
	}

	private function _render_debug_options(){
		if( !Upfront_Permissions::current( Upfront_Permissions::SEE_USE_DEBUG ) ) return;
		Upfront_Layout::get_db_layouts();
		$exclude_google_maps_api = Upfront_Cache_Utils::get_option( 'upfront_exclude_google_maps_api', 0 );
		$external_avatars_enabled = (int) get_option('upfront_external_avatars_enabled', 0);
		?>
		<div class="postbox-container debug-options">
			<div class='postbox'>
				<h2 class="title"><?php esc_html_e("Debug-Optionen", 'upfront') ?></h2>
				<div class="inside debug-options">
					<div class="upfront-debug-block lightgrey">
						<p><?php printf( __('Hier findest Du verschiedene Debug-Hilfen, die Du ausprobieren kannst, wenn etwas schief geht. Bevor Du die untenstehenden Optionen ausprobierst, stelle bitte sicher, dass Du einen <a target="_blank" href="%s"><strong>Leeren Cache &amp; Hard Reload</strong></a> durchgeführt hast, das löst normalerweise die meisten Probleme.', 'upfront' ), "http://refreshyourcache.com/en/home/"); ?> </p>
					</div>
					<div class="upfront-debug-block">
						<p class="left"><?php esc_html_e("Kann nach Core-Upgrades hilfreich sein.", 'upfront') ?></p>
						<button id="upfront_reset_cache"><?php esc_html_e("Upfront Cache leeren", 'upfront') ?></button>
					</div>
					<div class="upfront-debug-block exclude-google-maps-api">
						<p class="left"><?php esc_html_e( 'Google Maps API ausschließen', 'upfront' ) ?></p>
						<div class="upfront_toggle right">
							<input value="1" type="checkbox" name="upfront_exclude_google_maps_api" class="upfront_toggle_checkbox" id="upfront_exclude_google_maps_api" <?php checked( true, $exclude_google_maps_api ); ?> data-current="<?php echo $exclude_google_maps_api;?>">
							<label class="upfront_toggle_label" for="upfront_exclude_google_maps_api">
								<span class="upfront_toggle_inner"></span>
								<span class="upfront_toggle_switch"></span>
							</label>
						</div>
					</div>
					<div class="upfront-debug-block external-avatars">
						<p class="left"><?php esc_html_e( 'Externe Avatare laden', 'upfront' ) ?></p>
						<div class="upfront_toggle right">
							<input value="1" type="checkbox" name="upfront_external_avatars_enabled" class="upfront_toggle_checkbox" id="upfront_external_avatars_enabled" <?php checked( true, $external_avatars_enabled ); ?> data-current="<?php echo $external_avatars_enabled;?>">
							<label class="upfront_toggle_label" for="upfront_external_avatars_enabled">
								<span class="upfront_toggle_inner"></span>
								<span class="upfront_toggle_switch"></span>
							</label>
						</div>
						<p class="left"><small><?php esc_html_e( 'Aus: nur lokale Platzhalterbilder. An: Gravatar/andere externe Avatar-Dienste laden.', 'upfront' ) ?></small></p>
					</div>
					<div class="upfront-debug-block lightgrey">
						<p class="left">
							<small><?php esc_html_e("Setzt das Layout auf das Standard-Erscheinungsbild zurück – Vorsicht!", 'upfront') ?></small>
						</p>
						<div class="upfront-layout-reset">
							<?php
							$db_layouts = Upfront_Server_PageLayout::get_instance()->parse_theme_layouts(Upfront_Debug::get_debugger()->is_dev());
							if( $db_layouts ): ?>
								<select class="upfront-layouts-list">
									<option value="0"><?php esc_html_e("Bitte wähle ein Layout zum Zurücksetzen", 'upfront'); ?></option>
									<?php ; foreach( $db_layouts as $key => $item ): ?>
										<option value="<?php echo (is_array($item)) ? esc_attr($item['name']) : esc_attr($item); ?>"><?php echo esc_html(Upfront_Server_PageLayout::get_instance()->db_layout_to_name($item)); ?></option>
									<?php endforeach; ?>
								</select>
								<div class="upfront-reset-global-option">
									<div class="upfront_toggle">
										<input value="1" type="checkbox" name="upfront_reset_include_global" class="upfront_toggle_checkbox" id="upfront_reset_include_global" >
										<label class="upfront_toggle_label" for="upfront_reset_include_global">
											<span class="upfront_toggle_inner"></span>
											<span class="upfront_toggle_switch"></span>
										</label>
									</div>
									<small><?php esc_html_e("Globale Bereiche einbeziehen", 'upfront'); ?></small>
								</div>
							<?php else: ?>
								<h4><?php esc_html_e("Du hast kein gespeichertes Layout zum Zurücksetzen", 'upfront'); ?></h4>
							<?php endif; ?>
						</div>
						<button id="upfront_reset_layout" disabled="disabled" data-dev="<?php echo (int)Upfront_Debug::get_debugger()->is_dev();?>"><?php esc_html_e("Layout zurücksetzen", 'upfront') ?></button>
					</div>
					<div class="upfront-debug-block">
						<p class="left"><?php esc_html_e("Theme auf Standardzustand zurücksetzen", 'upfront') ?></p>
						<p class="left"><?php _e('<small><strong class="warning-text">ACHTUNG:</strong> Dies setzt Dein aktives Theme auf den Zustand zurück, in dem es sich bei der ersten Installation befand. Dies kann nicht rückgängig gemacht werden, also bitte vorher sichern.</small>', 'upfront'); ?></p>
						<button class="warning" id="upfront_reset_theme"><?php esc_html_e("Theme zurücksetzen", 'upfront') ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the site under construction box
	 */
	private function _render_under_construction_box () {
		$maintenance_mode = Upfront_Cache_Utils::get_option(Upfront_Server::MAINTENANCE_MODE, false);
		$enable_maintenance_mode = false;
		if ( $maintenance_mode ) {
			$maintenance_mode = json_decode($maintenance_mode);
			$maintenance_mode = ( $maintenance_mode && is_object($maintenance_mode) ) ? $maintenance_mode : new stdClass();
			$enabled = (isset($maintenance_mode->enabled)) ? (int)$maintenance_mode->enabled : 0;
			$enable_maintenance_mode = ( $enabled == 1 ) ? true : false;
		}
		?>
		<div class="postbox-container under-construction">
			<div class='postbox'>
				<h2 class="title"><?php esc_html_e("Website-Wartung", 'upfront') ?></h2>
				<div class="inside">
					<p class="label"><?php esc_html_e("Wartungsmodus der Website aktivieren", 'upfront') ?></p>
					<div class="upfront_toggle">
						<input value="1" type="checkbox" name="upfront_under_construction" class="upfront_toggle_checkbox" id="upfront_under_construction" <?php checked(true, $enable_maintenance_mode ); ?> data-current="<?php echo $enable_maintenance_mode;?>" >
						<label class="upfront_toggle_label" for="upfront_under_construction">
							<span class="upfront_toggle_inner"></span>
							<span class="upfront_toggle_switch"></span>
						</label>
					</div>
					<?php
					if ( $maintenance_mode && isset($maintenance_mode->permalink) ) {
						echo '<span class="link">' . sprintf(
							__('Du kannst die Wartungsseite <a href="%s" target="_blank">hier</a> bearbeiten', 'upfront'),
							$maintenance_mode->permalink . '?editmode=true'
						) . '</span>';
					}
					?>
					<p><button id="upfront_save_under_construction" disabled="disabled"><?php esc_html_e("Speichern", 'upfront') ?></button></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the changelog box
	 */
	private function _render_changelog_box () {
		$changelog = $this->_get_changelog();
		if (empty($changelog)) return false;
		?>
		<div class="postbox-container changelog">
			<div class='postbox'>
				<h2 class="title"><?php esc_html_e("Changelog", 'upfront') ?></h2>
				<div class="inside changelog">
				<?php
					reset($changelog);
					$current_key = key($changelog);
					$current_value = current($changelog);
				?>
					<div class="current">
						<dl>
							<dt><?php echo $current_key; ?></dt>
							<dd><?php echo $current_value; ?></dd>
						</dl>
					</div>

					<div class="changelog navigation">
						<a href="#more"><?php esc_html_e('Frühere Einträge', 'upfront'); ?></a>
					</div>

					<div class="previous">
						<dl>
						<?php foreach ($changelog as $version => $changeset) { ?>
							<?php if ($version === $current_key) continue; ?>
							<dt><?php echo $version; ?></dt>
							<dd><?php echo $changeset; ?></dd>
						<?php } ?>
						</dl>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Gets the raw changelog entries from the file
	 *
	 * @return array
	 */
	private function _get_raw_changelog_entries () {

		$path = trailingslashit(wp_normalize_path(Upfront::get_root_dir())) . 'CHANGELOG.md';
		$entries = array();
		if (!file_exists($path) || !is_readable($path)) return $entries;

		$fp = fopen($path, 'r');
		$idx = '';
		while (false !== ($line = fgets($fp, 4096))) {
			if (preg_match('/-{3,}/', $line)) continue; // drop line junk
			if (preg_match('/^\d\.\d.*?-\s\d{4}/', $line)) {
				$idx = $line;
				continue;
			}
			if (empty($idx)) continue; // Sanity check, header junk

			if (empty($entries[$idx])) $entries[$idx] = array();
			$entries[$idx][] = $line;
		}
		fclose($fp);

		return $entries;
	}

	/**
	 * Gets the changelog entries array
	 *
	 * @return array
	 */
	private function _get_changelog () {
		$entries = $this->_get_raw_changelog_entries();
		$changelog = array();
		$df = Upfront_Cache_Utils::get_option('date_format');
		foreach ($entries as $version => $entry) {
			if (empty($entry)) continue;

			$tmp = explode('-', $version, 2);
			if (empty($tmp[1])) continue;

			$date = strtotime($tmp[1]);
			if (empty($date)) continue;

			$key = "" .
				"<b>{$tmp['0']}</b>" .
				'&nbsp;' .
				'(' .
					date_i18n($df, $date) .
				')' .
			"";

			$changeset = array();
			$separated = false;
			$total_lines = count($entry);
			for ($i=0; $i<$total_lines; $i++) {
				$line = trim(ltrim($entry[$i], '- '));
				if (empty($line)) {
					if ($separated) continue;

					$next_line = isset($entry[$i+1])
						? trim(ltrim($entry[$i+1], '- '))
						: false
					;
					if (empty($next_line)) continue;

					$line = '</li></ul><div class="extra-toggle">' .
						'<a href="#toggle" data-expanded="' . esc_attr(__('Weniger anzeigen', 'upfront')) . '" data-contracted="' . esc_attr(__('Mehr anzeigen', 'upfront')) . '">' . esc_html(__('Mehr anzeigen', 'upfront')) . '</a>' .
					'</div><ul class="extra"><li>';
					$separated = true;
				}

				$changeset[] = $line;
			}

			if (empty($changeset)) continue;

			$changelog[$key] = '<ul><li>' . join('</li><li>', $changeset) . '</li></ul>';
		}
		return $changelog;
	}
}
