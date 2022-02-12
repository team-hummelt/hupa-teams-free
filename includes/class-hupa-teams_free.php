<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 */


use Hupa\TeamFreeMembers\Free_Render_Callback_Templates;
use Hupa\TeamFreeMembers\Hupa_Teams_Free_Rest_Endpoint;
use Hupa\TeamFreeMembers\Register_Teams_Free_Gutenberg_Patterns;
use Hupa\TeamFreeMembers\Register_Teams_Free_Gutenberg_Tools;
use Hupa\TeamFreeMembers\Teams_Free_Members_Block_Callback;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Teams_Free {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hupa_Teams_Free_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected Hupa_Teams_Free_Loader $loader;

    /**
     * TWIG autoload for PHP-Template-Engine
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Environment $twig TWIG autoload for PHP-Template-Engine
     */
    protected Environment $twig;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected string $version;

    /**
     * The current database version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $db_version    The current database version of the plugin.
     */
    protected string $db_version;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @var object The main class.
     */
    public object $main;

    /**
     * The plugin Slug Path.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_slug plugin Slug Path.
     */
    private string $plugin_slug;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'HUPA_TEAMS_FREE_VERSION' ) ) {
			$this->version = HUPA_TEAMS_FREE_VERSION;
		} else {
			$this->version = '1.0.0';
		}


        if ( defined( 'HUPA_TEAMS_FREE_DB_VERSION' ) ) {
            $this->db_version = HUPA_TEAMS_FREE_DB_VERSION;
        } else {
            $this->db_version = '1.0.0';
        }

        $this->plugin_name = HUPA_TEAMS_FREE_BASENAME;
        $this->plugin_slug = HUPA_TEAMS_FREE_SLUG_PATH;
        $this->main = $this;

        //Check PHP AND WordPress Version
        $this->check_dependencies();
		$this->load_dependencies();
		$this->set_locale();
        $tempDir = plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'class-gutenberg' . DIRECTORY_SEPARATOR . 'callback-templates';
        $twig_loader = new FilesystemLoader($tempDir);
        $this->twig = new Environment($twig_loader);
        $this->register_team_members_render_callback();
        $this->register_team_members_callback();
        $this->register_gutenberg_patterns();
		$this->register_gutenberg_sidebar();
        $this->define_admin_hooks();
		$this->define_public_hooks();
        $this->register_hupa_team_members_endpoint();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hupa_Teams_Loader. Orchestrates the hooks of the plugin.
	 * - Hupa_Teams_i18n. Defines internationalization functionality.
	 * - Hupa_Teams_Admin. Defines all hooks for the admin area.
	 * - Hupa_Teams_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        /**
         * The trait for the default settings of the Hupa-Team-Members
         * of the plugin.
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/hupa_teams_free_members_defaults_trait.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hupa-teams-free-i18n.php';

        /**
         * The code that runs during plugin activation.
         * This action is documented in includes/class-hupa-teams-free-activator.php
         */
        require_once plugin_dir_path(dirname(__FILE__ ) ) . 'includes/class-hupa-teams-free-activator.php';

        /**
         * TWIG autoload for PHP-Template-Engine
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/Twig/autoload.php';

        /**
         * The class responsible for defining Callback Templates
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-gutenberg/class_free_render_callback_templates.php';

        /**
         * The class responsible for defining WP REST API Routes
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-gutenberg/class_hupa_teams_free_rest_endpoint.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gutenberg/class_teams_free_members_block_callback.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gutenberg/class_register_teams_free_gutenberg_tools.php';

        /**
         * The class responsible for defining all Gutenberg Patterns.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gutenberg/class_register_teams_free_gutenberg_patterns.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hupa-teams-free-admin.php';

        /**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hupa-teams-free-public.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hupa-teams-free-loader.php';

		$this->loader = new Hupa_Teams_Free_Loader();

	}

    /**
     * Check PHP and WordPress Version
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function check_dependencies(): void
    {
        global $wp_version;
        if (version_compare(PHP_VERSION, HUPA_TEAMS_FREE_PHP_VERSION, '<') || $wp_version < HUPA_TEAMS_FREE_WP_VERSION) {
            $this->maybe_self_deactivate();
        }
    }

    /**
     * Self-Deactivate
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function maybe_self_deactivate(): void
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins($this->plugin_slug);
        add_action('admin_notices', array($this, 'self_deactivate_notice'));
    }

    /**
     * Self-Deactivate Admin Notiz
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function self_deactivate_notice(): void
    {
        echo sprintf('<div class="error" style="margin-top:5rem"><p>' . __('This plugin has been disabled because it requires a PHP version greater than %s and a WordPress version greater than %s. Your PHP version can be updated by your hosting provider.', 'hupa-teams') . '</p></div>', HUPA_TEAMS_FREE_PHP_VERSION, HUPA_TEAMS_FREE_WP_VERSION);
        exit();
    }

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hupa_Teams_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hupa_Teams_Free_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

    /**
     * Register all the hooks related to the Gutenberg Sidebar functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_gutenberg_sidebar() {
        $registerGBTools = new Register_Teams_Free_Gutenberg_Tools($this->get_plugin_name(), $this->get_version(), $this->main);

        $this->loader->add_action( 'init', $registerGBTools, 'team_free_member_posts_sidebar_meta_fields' );
        $this->loader->add_action( 'init', $registerGBTools, 'wp_team_free_members_register_sidebar' );
        $this->loader->add_action( 'enqueue_block_editor_assets', $registerGBTools, 'team_free_members_sidebar_script_enqueue' );
        $this->loader->add_action( 'init', $registerGBTools, 'register_team_free_members_block_type' );
        $this->loader->add_action( 'enqueue_block_editor_assets', $registerGBTools, 'team_free_members_block_type_scripts' );

        //
    }

    /**
     * Register all the hooks related to the Gutenberg Sidebar functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_gutenberg_patterns() {
        $registerPatterns = new Register_Teams_Free_Gutenberg_Patterns($this->get_plugin_name(), $this->get_version(), $this->main);

        $this->loader->add_action( 'init', $registerPatterns, 'register_block_pattern_free_category' );
        $this->loader->add_action( 'init', $registerPatterns, 'register_gutenberg_free_patterns' );
        $this->loader->add_filter( $this->plugin_name . '/get_template_select', $registerPatterns, 'get_template_gutenberg_free_select' );
    }

    /**
     * Register all the hooks related to the Gutenberg Sidebar functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_team_members_callback() {
        global $registerTeamsCallback;
        $registerTeamsCallback = new Teams_Free_Members_Block_Callback();
    }

    /**
     * Register all the hooks related to the Gutenberg Sidebar functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_team_members_render_callback() {
        global $registerTeamsRenderCallback;
        $registerTeamsRenderCallback = new Free_Render_Callback_Templates($this->get_plugin_name(), $this->get_version(), $this->main, $this->twig);
        $this->loader->add_filter($this->plugin_name.'/render_callback_free_template', $registerTeamsRenderCallback, 'render_callback_free_template');
    }

    /**
     * Register all the hooks related to the Plugin Endpoints functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_hupa_team_members_endpoint() {
        $registerEndpoint = new Hupa_Teams_Free_Rest_Endpoint($this->get_plugin_name(), $this->get_version(), $this->main);
        $this->loader->add_action('rest_api_init', $registerEndpoint, 'register_free_routes');
        $this->loader->add_filter($this->plugin_name.'/get_custom_terms', $registerEndpoint, 'team_free_members_get_custom_terms');
    }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        $postTypes = new Hupa_Teams_Free_Activator();
        $this->loader->add_action( 'init', $postTypes, 'hupa_register_team_free_members' );
        $this->loader->add_action( 'init', $postTypes, 'hupa_register_team_free_members_taxonomies' );

        $plugin_admin = new Hupa_Teams_Free_Admin( $this->get_plugin_name(), $this->get_version(), $this->main );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hupa_Teams_Free_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name(): string
    {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hupa_Teams_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader(): Hupa_Teams_Loader
    {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version(): string
    {
		return $this->version;
	}

    /**
     * Retrieve the database version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The database version number of the plugin.
     */
    public function get_db_version(): string {
        return $this->db_version;
    }

}
