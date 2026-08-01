<?php
/**
 * @group upfront-core
 */
class UpfrontVirtualPostTest extends WP_UnitTestCase {

	private function create_singular_query ($post, $is_404 = false) {
		$query = new WP_Query();
		$query->queried_object = $post;
		$query->queried_object_id = $post->ID;
		$query->post_count = 1;
		$query->is_404 = $is_404;
		return $query;
	}

	public function test_runtime_page_uses_generic_page_layout () {
		$post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => 'Runtime page',
			'post_content' => 'Runtime content',
		));
		$query = $this->create_singular_query($post);

		$this->assertFalse(Upfront_EntityResolver::get_persisted_post_id($query));
		$this->assertSame(array(
			'item' => 'page',
			'specificity' => false,
			'type' => 'single',
		), Upfront_EntityResolver::get_entity_cascade($query));
		$this->assertSame(array(
			'item' => 'single-page',
			'type' => 'single',
		), Upfront_EntityResolver::get_entity_ids(Upfront_EntityResolver::get_entity_cascade($query)));
	}

	public function test_runtime_string_id_is_not_treated_as_database_id () {
		$post = new WP_Post((object)array(
			'ID' => 'virtual-plugin-page',
			'post_type' => 'page',
			'post_status' => 'publish',
		));
		$query = $this->create_singular_query($post);

		$this->assertFalse(Upfront_EntityResolver::get_persisted_post_id($query));
	}

	public function test_post_data_uses_runtime_queried_object_without_global_post () {
		global $post, $wp_query;
		$original_post = $post;
		$original_query = $wp_query;
		$runtime_post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'course',
			'post_status' => 'publish',
			'post_content' => 'Runtime course content',
		));
		$wp_query = $this->create_singular_query($runtime_post);
		$post = null;

		$this->assertSame($runtime_post, Upfront_Post_Data_Model::get_post());
		$post = $original_post;
		$wp_query = $original_query;
	}

	public function test_database_post_keeps_specific_layout_identity () {
		$post_id = self::factory()->post->create(array('post_type' => 'page'));
		$post = get_post($post_id);
		$query = $this->create_singular_query($post);

		$this->assertSame($post_id, Upfront_EntityResolver::get_persisted_post_id($query));
		$this->assertSame((string)$post_id, (string)Upfront_EntityResolver::get_entity_cascade($query)['specificity']);
	}
}