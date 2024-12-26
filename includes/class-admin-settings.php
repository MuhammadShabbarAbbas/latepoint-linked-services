<?php

if (!defined('ABSPATH')) {
    exit;
}

class OSLinkedServicesBackendSettings
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
    public function hooks(): void
    {
        add_action('latepoint_service_form_after', [$this, 'load_admin_settings']);
        add_action('latepoint_service_saved', [$this, 'save_linked_services'], 16, 3);
    }


    public function save_linked_services($current_service, $is_new_record, $params): void
    {
        $other_services = (new OsServiceModel())->should_be_active()->where_not_in('id', [$current_service->id])->order_by('order_number asc')->get_results_as_models();
        $linked_services = [];
        foreach ($other_services as $s) {
            if (isset($params['links']['link_' . $s->id]['connected']) && $params['links']['link_' . $s->id]['connected'] == 'yes') {
                $linked_services[] = $s->id;
            }
        }
        $current_service->save_meta_by_key('linked_services', json_encode($linked_services));
    }

    public function load_admin_settings($current_service): void
    {
        $services = new OsServiceModel();
        $linked_services = $current_service->get_meta_by_key('linked_services');
        $args = [
            'other_services' => $services->should_be_active()->where_not_in('id', [$current_service->id])->order_by('order_number asc')->get_results_as_models(),
            'linked_services' => $linked_services ? json_decode($linked_services) : [],
            'current_service' => $current_service
        ];
        os_get_template_part('admin', 'settings-form', $args);
    }

}

//return new OSLinkedServicesBackendSettings();
