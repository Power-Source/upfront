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
		$query->is_page = 'page' === $post->post_type;
		$query->is_single = !$query->is_page;
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

	public function test_empty_runtime_page_with_tax_query_object_uses_generic_page_layout () {
		$post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
		));
		$query = $this->create_singular_query($post);
		$query->post_count = 0;
		$query->tax_query = new WP_Tax_Query(array());

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

	public function test_virtual_post_injected_into_archive_uses_single_layout () {
		register_post_status('virtual', array('public' => true));
		$post_id = self::factory()->post->create(array('post_status' => 'virtual'));
		$post = get_post($post_id);
		$query = new WP_Query();
		$query->posts = array($post);
		$query->post_count = 1;
		$query->is_archive = true;

		$this->assertSame($post, Upfront_EntityResolver::get_virtual_post($query));
		$this->assertSame(array(
			'item' => 'post',
			'specificity' => $post_id,
			'type' => 'single',
		), Upfront_EntityResolver::get_entity_cascade($query));
	}

	public function test_single_result_archive_stays_archive_without_virtual_status () {
		$post_id = self::factory()->post->create();
		$query = new WP_Query();
		$query->posts = array(get_post($post_id));
		$query->post_count = 1;
		$query->is_archive = true;

		$this->assertFalse(Upfront_EntityResolver::get_virtual_post($query));
		$this->assertSame('archive', Upfront_EntityResolver::get_entity_cascade($query)['type']);
	}

	public function test_post_data_prefers_injected_virtual_post_over_global_post () {
		global $post, $wp_query;
		$original_post = $post;
		$original_query = $wp_query;
		$virtual_post = new WP_Post((object)array(
			'ID' => 123,
			'post_type' => 'jbp_pro',
			'post_status' => 'virtual',
			'post_content' => '[jbp-expert-archive-page]',
		));
		$wp_query = new WP_Query();
		$wp_query->posts = array($virtual_post);
		$wp_query->post_count = 1;
		$wp_query->is_archive = true;
		$post = self::factory()->post->create_and_get();

		$this->assertSame($virtual_post, Upfront_Post_Data_Model::get_post());
		$post = $original_post;
		$wp_query = $original_query;
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

	public function test_post_data_uses_runtime_global_post_without_query_object () {
		global $post, $wp_query;
		$original_post = $post;
		$original_query = $wp_query;
		$runtime_post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_content' => 'Filtered at runtime',
		));
		$wp_query = new WP_Query();
		$post = $runtime_post;

		$this->assertSame($runtime_post, Upfront_Post_Data_Model::get_post());
		$post = $original_post;
		$wp_query = $original_query;
	}

	public function test_filtered_content_is_reused_for_repeated_layout_rendering () {
		$post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_content' => 'Runtime content',
		));
		$filter_calls = 0;
		$filter = function ($content) use (&$filter_calls) {
			$filter_calls++;
			return 'Filtered runtime content';
		};
		add_filter('the_content', $filter, 999);

		$view = new UpfrontVirtualPostPartView();
		$this->assertSame('Filtered runtime content', $view->get_content($post));
		$this->assertSame('Filtered runtime content', $view->get_content(clone $post));
		$this->assertSame(1, $filter_calls);
		remove_filter('the_content', $filter, 999);
	}

	public function test_legacy_content_template_renders_filtered_content () {
		$post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_content' => 'Runtime content',
		));
		$view = new Upfront_Post_Data_PartView_Post_data(array(
			'content' => 'content',
			'post-part-content' => '<div class="content">the_content();</div>',
		));
		$markup = $view->get_markup($post);

		$this->assertContains('Runtime content', $markup['content']);
		$this->assertNotContains('the_content();', $markup['content']);
	}

	public function test_this_post_renders_runtime_post_instead_of_editor_placeholder () {
		$post = new WP_Post((object)array(
			'ID' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => 'Runtime directory title',
			'post_content' => 'Runtime directory content',
		));
		$markup = Upfront_ThisPostView::get_template_markup($post, array());

		$this->assertContains('Runtime directory title', $markup);
		$this->assertNotContains('Enter your new page title here', $markup);
	}

	public function test_database_post_keeps_specific_layout_identity () {
		$post_id = self::factory()->post->create(array('post_type' => 'page'));
		$post = get_post($post_id);
		$query = $this->create_singular_query($post);

		$this->assertSame($post_id, Upfront_EntityResolver::get_persisted_post_id($query));
		$this->assertSame((string)$post_id, (string)Upfront_EntityResolver::get_entity_cascade($query)['specificity']);
	}
}

class UpfrontVirtualPostPartView extends Upfront_PostPart_View {
	public function get_content ($post) {
		$this->_post = $post;
		return $this->_get_content();
	}
}