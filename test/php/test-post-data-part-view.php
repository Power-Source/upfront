<?php
/**
 * @group upfront-core
 */
class UpfrontPostDataPartViewTest extends WP_UnitTestCase {

	public function test_default_parts_resolve_from_base_view_class () {
		$this->assertSame(
			array('date_posted', 'title', 'content'),
			Upfront_Post_Data_PartView_Post_data::get_default_parts(array('data_type' => 'post_data'))
		);
	}
}