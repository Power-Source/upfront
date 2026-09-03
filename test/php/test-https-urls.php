<?php
/**
 * @group upfront-core
 */
class UpfrontHttpsUrlsTest extends WP_UnitTestCase {

	private $server;

	public function setUp () {
		parent::setUp();
		$this->server = $_SERVER;
		unset($_SERVER['HTTPS']);
		$_SERVER['HTTP_HOST'] = 'example.test';
		$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
	}

	public function tearDown () {
		$_SERVER = $this->server;
		parent::tearDown();
	}

	public function test_detects_https_behind_proxy () {
		$this->assertTrue(upfront_is_https_request());
	}

	public function test_upgrades_same_origin_http_url () {
		$this->assertSame(
			'https://example.test/wp-content/theme.css',
			upfront_https_same_origin_url('http://example.test/wp-content/theme.css')
		);
	}

	public function test_does_not_rewrite_external_http_url () {
		$this->assertSame(
			'http://external.test/resource.js',
			upfront_https_same_origin_url('http://external.test/resource.js')
		);
	}

	public function test_adds_upgrade_directive_to_existing_csp () {
		$headers = upfront_https_security_headers(array('content-security-policy' => "default-src 'self'"));

		$this->assertSame(
			"default-src 'self'; upgrade-insecure-requests; block-all-mixed-content",
			$headers['content-security-policy']
		);
	}
}