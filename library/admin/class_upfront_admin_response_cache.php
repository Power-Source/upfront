<?php

class Upfront_Admin_ResponseCache extends Upfront_Admin_Page {

	const FORM_NONCE_KEY = 'upfront_fe_cache_wpnonce';
	const FORM_NONCE_ACTION = 'upfront_fe_cache_save';

	public function render_page () {
		// yeah...
	}

	/**
	 * Processes POST submissions
	 *
	 * @return bool
	 */
	public function process_submissions () {
		$data = stripslashes_deep($_POST);

		if (empty($data)) return false;
		if (!$this->can_access()) return false;
		if (empty($data[self::FORM_NONCE_KEY])) return false;


		if (!wp_verify_nonce($_POST[self::FORM_NONCE_KEY], self::FORM_NONCE_ACTION)) return false;

		$levels = $this->get_levels();
		$level = !empty($data['fe_cache-level'])
			? $data['fe_cache-level']
			: ''
		;
		if (!in_array($level, array_keys($levels))) return false;

		return update_option('upfront-response_cache-level', $level);
	}

	/**
	 * Known levels getter
	 *
	 * @return array
	 */
	 public function get_levels () {
		return array(
			'' => array(
				'label' => __('Keine', 'upfront'),
				'help' => __('Legacy-Verhalten von UpFront. Jegliche Warteschlangenbildung für Anfragen sowie Caching werden umgangen.', 'upfront'),
			),
			'stub' => array(
				'label' => __('Nur Warteschlange', 'upfront'),
				'help' => __('Nur Warteschlangenbildung für Anfragen, kein Caching der Antworten. Mehrere Anfragen werden zu einer zusammengefasst.', 'upfront'),
			),
			'memory' => array(
				'label' => __('Speicher', 'upfront'),
				'help' => __('Warteschlangenbildung für Anfragen mit Caching der Antworten im Speicher. Gechachte Antworten überleben kein Neuladen der Seite.', 'upfront'),
			),
			'persistent' => array(
				'label' => __('Sitzung', 'upfront'),
				'help' => __('Warteschlangenbildung für Anfragen mit Caching der Antworten im Sitzungs-Speicher. Gechachte Antworten überleben ein Neuladen der Seite, aber nicht das Schließen des Fensters/Tabs.', 'upfront'),
			),
			'permanent' => array(
				'label' => __('Permanent', 'upfront'),
				'help' => __('Warteschlangenbildung für Anfragen mit Caching der Antworten im lokalen Speicher. Gechachte Antworten bleiben gecacht, bis sie bereinigt werden.', 'upfront'),
			),
		);
	}

	/**
	 * Access protection abstraction
	 *
	 * @return bool
	 */
	public function can_access () {
		return $this->_can_access(Upfront_Permissions::MODIFY_RESTRICTIONS);
	}

	/**
	 * Renders level key entry box
	 *
	 * @param string $level Known service index key
	 */
	public function render_level_box ($level=false) {
		$levels = $this->get_levels();
		if (!in_array($level, array_keys($levels))) return false;

		$value = get_option('upfront-response_cache-level', '');

		?>
<div class="fe_cache level <?php echo sanitize_html_class($level); ?>">
	<div class="upfront_toggle_radio">
		<p>
			<input
				type="radio" name="fe_cache-level"
				id="fe_cache-level-<?php echo esc_attr($level); ?>"
				value="<?php echo esc_attr($level); ?>"
				<?php checked($value, $level); ?>
			/>
			<label class="upfront_toggle_radio_label" for="fe_cache-level-<?php echo esc_attr($level); ?>">
				<span class="upfront_toggle_radio_button"></span>
				<span class="upfront_toggle_radio_main_label">
					<?php echo esc_html($levels[$level]['label']); ?>
				</span>
			<?php if (!empty($levels[$level]['help'])) { ?>
				<span class="upfront_toggle_radio_sub_label">
					<?php echo $levels[$level]['help']; ?>
				</span>
			<?php } ?>
			</label>
		</p>
	</div>
</div>
		<?php
	}

	public function render_footer () {
		wp_nonce_field(self::FORM_NONCE_ACTION, self::FORM_NONCE_KEY);
	}


}
