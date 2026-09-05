<?php

class Upfront_Compat_Wiki {

	public function __construct() {
		add_filter('template_include', array($this, 'override_single_template'), 99);
	}

	public function override_single_template($template) {
		if (!is_singular('psource_wiki')) return $template;

		$upfront_template = get_template_directory() . DIRECTORY_SEPARATOR . 'single.php';
		return file_exists($upfront_template) ? $upfront_template : $template;
	}
}