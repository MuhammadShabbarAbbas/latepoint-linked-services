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
        add_action('wp_enqueue_scripts', array($this, 'scripts'));
        add_action('latepoint_order_created', array($this, 'my_custom_order_handler'), 10, 1);
//        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }


    function my_custom_order_handler($order) {
       error_log("ok this is working");
       error_log(print_r($order));
    }
    public function admin_scripts()
    {
        wp_enqueue_style('latepoint-linked-services-admin-style', LATEPOINT_LINKED_SERVICES_URL . 'assets/style/admin.css');
    }

    public function scripts()
    {
        wp_enqueue_script('latepoint-linked-services-script', LATEPOINT_LINKED_SERVICES_URL . 'assets/public.js', ['jquery'], time());
        wp_enqueue_style('latepoint-linked-services-style', LATEPOINT_LINKED_SERVICES_URL . 'assets/public.css', [], time());

        // js options and i18n
//        $options = array(
//            'ajax_url' => admin_url('admin-ajax.php'),
//           );
//        wp_localize_script('latepoint-linked-services-script', 'os_linked_services_plugin', $options);
    }

}

return new OSLinkedServicesSetup();
