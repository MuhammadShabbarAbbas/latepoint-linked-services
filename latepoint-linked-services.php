<?php
/**
 * Plugin Name: LatePoint Addon - Linked Services
 * Description: This plugin makes it possible to link separate services dependent on each other
 * Version: 0.0.1
 * Author: ODES
 * Author URI: https://odes.pk
 * Text Domain: latepoint-linked-services
 * Domain Path: /languages
 */


if (!defined('ABSPATH')) {
    exit;
}


/**
 * Main Class.
 *
 * @since 1.0.0
 */
final class OSLinkedServices
{
    /**
     * @var $_instance : The one true instance
     * @since 1.0.0
     */
    protected static $_instance = null;

    public $version = '0.0.1';

    /**
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->localisation();
        $this->init();
        add_action('latepoint_includes', [$this, 'include_dependents']);
        do_action('latepoint_linked_services_loaded');
    }


    /**
     * Define Constants.
     * @since  1.0.0
     */
    private function define_constants()
    {
        $this->define('LATEPOINT_LINKED_SERVICES_DIR', plugin_dir_path(__FILE__));
        $this->define('LATEPOINT_LINKED_SERVICES_URL', plugin_dir_url(__FILE__));
        $this->define('LATEPOINT_LINKED_SERVICES_BASENAME', plugin_basename(__FILE__));
        $this->define('LATEPOINT_LINKED_SERVICES_VERSION', $this->version);
    }

    /**
     * Define constant if not already set.
     * @since  1.0.0
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Include required files.
     * @since  1.0.0
     */
    public function includes()
    {

        //functions
        include_once 'includes/functions.php';

        include_once 'includes/class-setup.php';
        include_once 'includes/class-admin-settings.php';
        include_once 'includes/class-steps.php';
        include_once 'includes/class-helper.php';
        include_once 'includes/os-linked-services-calendar-helper.php';
    }

    /**
     * Load Localisation files.
     * @since  1.0.0
     */
    public function localisation()
    {
        $locale = apply_filters('plugin_locale', get_locale(), 'latepoint-linked-services');

        load_textdomain('latepoint-linked-services', WP_LANG_DIR . '/latepoint-linked-services/latepoint-linked-services-' . $locale . '.mo');
        load_plugin_textdomain('latepoint-linked-services', false, plugin_basename(dirname(__FILE__)) . '/languages');
    }

    public function init()
    {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('plugins_loaded', [$this, 'upgrade']);
    }

    /**
     * Main Instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function include_dependents()
    {
        //functions
        include_once 'includes/class-linked-services-controller.php';
    }

    /**
     * Throw error on object clone.
     *
     * @return void
     * @since 1.0.0
     * @access protected
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'islsw'), '1.0.0');
    }

    /**
     * Disable unserializing of the class.
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'islsw'), '1.0.0');
    }

    /**
     * responsible to do actions on activation
     */
    public function activate()
    {
//        no wer're saving as meta
//        $this->add_column_to_wp_latepoint_services();
    }


    function add_column_to_wp_latepoint_services()
    {
        global $wpdb;

        // Table name
        $table_name = $wpdb->prefix . 'latepoint_services';

        // The column you want to add
        $column_name = 'linked_services';
        $column_definition = 'JSON DEFAULT NULL';

        // Check if the column exists
        $column_exists = $wpdb->get_results(
            "SHOW COLUMNS FROM {$table_name} LIKE '{$column_name}'"
        );

        // If the column does not exist, add it
        if (empty($column_exists)) {
            $wpdb->query(
                "ALTER TABLE {$table_name} ADD {$column_name} {$column_definition}"
            );
        }
    }

    /**
     * responsible to do actions on activation
     */
    public function deactivate()
    {
    }

    /**
     * responsible to upgrade plugin
     */
    public function upgrade()
    {

    }
}


/**
 * Run the plugin.
 */
function OSLinkedServices()
{
    return OSLinkedServices::instance();
}

OSLinkedServices();