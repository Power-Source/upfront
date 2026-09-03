<?php
include_once "admin/class_upfront_admin_page.php";
include_once "admin/class_upfront_admin_theme_catalog.php";
include_once "admin/class_upfront_admin_general.php";
include_once "admin/class_upfront_admin_restrictions.php";
include_once "admin/class_upfront_admin_experimental.php";
include_once "admin/class_upfront_admin_api_keys.php";
include_once "admin/class_upfront_admin_response_cache.php";
include_once "admin/class_upfront_admin_codepen.php";

/**
 * Constructs Upfront admin pages
 *
 * Class Upfront_Admin
 */
class Upfront_Admin
{

	public static $menu_slugs = array(
		"main" => "upfront",
		"restrictions" => "upfront_restrictions",
		"experimental" => "upfront_experimental",
		"codepen" => "upfront_to_codepen"
	);

	/**
	 * Hook to necessary hooks, add assets and initialize the admin
	 *
	 * Upfront_Admin constructor.
	 */
	function __construct()
	{
		Upfront_Admin_Theme_Catalog::get_instance();
		Upfront_Admin_CodePen::register_ajax();
		add_action( 'admin_menu', array( $this, "add_menus" ) );
		add_action("admin_enqueue_scripts", array( $this, "enqueue_scripts" ) );
	}

	/**
	 * Enqueue necessary scripts and styles
	 *
	 *
	 */
	function enqueue_scripts( $hook ){
		// using preg_replace instead str_replace("upfront_page_" since sometimes word upfront gets translated
		// to other languages e.g. chinese taiwan
		$is_upfront_admin_subpage =  in_array( preg_replace("#.+_page_#", "", $hook), self::$menu_slugs);
		$is_upfront_admin_mainpage = "toplevel_page_upfront" === $hook;

		if ( false === ( $is_upfront_admin_subpage || $is_upfront_admin_mainpage ) ) return;

		$admin_css = file_exists(Upfront::get_root_dir() . '/styles/admin.css')
			? '/styles/admin.css'
			: '/styles/build/admin.css'
		;
		$admin_css_version = filemtime(Upfront::get_root_dir() . $admin_css);

		wp_enqueue_style( 'upfront_admin', Upfront::get_root_url() . $admin_css, array(), $admin_css_version );
		wp_register_script( 'upfront_admin_js', Upfront::get_root_url() . "/scripts/admin.js", array("jquery"), Upfront_ChildTheme::get_version(), true);
		wp_localize_script( 'upfront_admin_js', "Upfront_Data", array(
			'l10n' => array(
				"sure_to_reset_theme" => __('Bist Du sicher, dass Du das Theme auf den Standardzustand zurücksetzen möchtest? Bitte beachte, dass dieser Vorgang nicht rückgängig gemacht werden kann.', 'upfront'),
				"sure_to_reset_layout" => __('Bist Du sicher, dass Du das Layout "{layout}" auf den Standardzustand zurücksetzen möchtest? Bitte beachte, dass dieser Vorgang nicht rückgängig gemacht werden kann.', 'upfront')
			)
		) );
		wp_enqueue_script('upfront_admin_js');
	}

	/**
	 * Adds menu and sub-menu pages
	 *
	 *
	 */
	function add_menus(){

		global $menu, $submenu;

		if (Upfront_Permissions::current( Upfront_Permissions::SEE_USE_DEBUG ) || Upfront_Permissions::current( Upfront_Permissions::MODIFY_RESTRICTIONS )) {
			add_menu_page( __("Allgemeine Einstellungen", 'upfront'), __("Upfront", 'upfront'), "manage_options", self::$menu_slugs['main'], null, "", 58);
		}

		new Upfront_Admin_General();
		new Upfront_Admin_Restrictions();
		new Upfront_Admin_Experimental();
		new Upfront_Admin_CodePen();
	}

	/**
	 * Renders main menu page
	 */
	function render_main_menu(){
		?>
			<div class="wrap upfront_admin">
				<h1><?php _e("Allgemeine Einstellungen", 'upfront'); ?></h1>
			</div>
		<?php
	}

		function RemoveAddMediaButtonsForNonAdmins(){
				remove_action( 'media_buttons', 'media_buttons' );
		}


}

new Upfront_Admin;
