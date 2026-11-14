<?php

class UpfrontVideoAutoplayTest extends WP_UnitTestCase {

	public function test_autoplay_video_starts_muted () {
		$atts = apply_filters('shortcode_atts_video', array('autoplay' => 'true', 'muted' => false));
		$this->assertTrue($atts['muted']);
	}

	public function test_video_without_autoplay_keeps_its_mute_setting () {
		foreach (array('', 'false', '0') as $autoplay) {
			$atts = apply_filters('shortcode_atts_video', array('autoplay' => $autoplay, 'muted' => false));
			$this->assertFalse($atts['muted']);
		}
	}
}
