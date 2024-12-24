<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class OSLinkedServicesController extends OsController
{


    function __construct()
    {
        parent::__construct();

        $this->views_folder = LATEPOINT_LINKED_SERVICES_DIR . '/templates/views/';
        $this->vars['page_header'] = __('Date & Time Picker', 'latepoint-linked-services');
        $this->vars['breadcrumbs'][] = array('label' => __('Date & Time Picker', 'latepoint-linked-services'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('linked_service_datepicker', 'index')));
    }


}
