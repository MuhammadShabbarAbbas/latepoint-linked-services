<?php

if (!defined('ABSPATH')) {
    exit;
}

class OSLinkedServicesSetup
{
    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Hook in to actions & filters
     *
     * @since 1.0.0
     */
    public function hooks()
    {
//        add_action('wp_enqueue_scripts', array($this, 'scripts'));
//        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    public function admin_scripts()
    {
        wp_enqueue_style('latepoint-linked-services-admin-style', URL . 'assets/style/admin.css');
    }

    public function scripts()
    {
        wp_enqueue_script('latepoint-linked-services-script', URL . 'dist/assets/js/index.js');
        wp_enqueue_style('latepoint-linked-services-style', URL . 'dist/assets/css/index.css');

        // js options and i18n
        $options = array(
            'ajax_url' => admin_url('admin-ajax.php'),
           );
        wp_localize_script('latepoint-linked-services-script', 'os_linked_services_plugin', $options);
    }

}

return new OSLinkedServicesSetup();
