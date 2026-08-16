<?php

class Upfront_Compat_Backup_Info {

	public function __construct () {}

	/**
	 * Check to see if we're even capable of doing something like this
	 *
	 * @return bool
	 */
	public function is_actionable () {
		return is_multisite()
			? current_user_can('manage_network_options')
			: current_user_can('manage_options')
		;
	}

	/**
	 * Check if we have our plugin ready, or at least present
	 *
	 * @return bool
	 */
	public function has_plugin () {
		if ($this->is_plugin_active()) return true;
		return (bool)$this->is_plugin_present();
	}

	/**
	 * Check if our backup plugin is ready for work
	 *
	 * @return bool
	 */
	public function is_plugin_active () {
		return class_exists('WPMUDEVSnapshot') && is_callable(array('WPMUDEVSnapshot', 'instance'));
	}

	/**
	 * Check if our backup plugin is present, just not activated
	 *
	 * @return bool
	 */
	public function is_plugin_present () {
		$present = false;
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		if (!is_array($plugins) || empty($plugins)) return false;

		return !empty($plugins['snapshot/snapshot.php']);
	}

	/**
	 * Returns the backup nag link
	 *
	 * If Snapshot is active, return backup link
	 * If Snapshot is not active but it's there, return activation link
	 * Otherwise, go with project URL
	 *
	 * @return string
	 */
	public function get_plugin_link () {
		if ($this->is_plugin_active()) return $this->_get_backup_url();
		if ($this->is_plugin_present() && current_user_can('activate_plugins')) return $this->_get_activation_url();
		return $this->_get_project_url();
	}

	/**
	 * Get proper action verb, based on plugin state
	 *
	 * If Snapshot active, backup
	 * If not active and preset, activate
	 * Otherwise, install
	 *
	 * @return string
	 */
	public function get_plugin_action () {
		if ($this->is_plugin_active()) return __('Backup mit Snapshot', 'upfront');
		if ($this->is_plugin_present() && current_user_can('activate_plugins')) return __('Snapshot aktivieren', 'upfront');

		return __('Snapshot installieren', 'upfront');
	}

	/**
	 * Gets project page URL
	 *
	 * @return string
	 */
	private function _get_project_url () {
		return 'https://psource.eimen.net/wiki/snapshot-dokumentation/';
	}

	/**
	 * Gets backup actionable URL
	 *
	 * @return string
	 */
	private function _get_backup_url () {
		return admin_url('admin.php?page=snapshots_new_panel');
	}

	/**
	 * Gets plugins activation URL
	 *
	 * @return string
	 */
	private function _get_activation_url () {
		return admin_url('plugins.php');
	}

}
