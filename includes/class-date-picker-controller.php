<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class OsLinkedServicesDatePickerController extends OsController
{


    function __construct()
    {
        parent::__construct();

        $this->views_folder = LATEPOINT_LINKED_SERVICES_DIR . '/templates/views/';
        $this->vars['page_header'] = __('Date & Time Picker', 'latepoint-linked-services');
        $this->vars['breadcrumbs'][] = array('label' => __('Date & Time Picker', 'latepoint-linked-services'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('linked_service_datepicker', 'index')));
    }

    public function load_linked_services_datepicker_month() {
        OsStepsHelper::set_required_objects( $this->params );

        $target_date       = new OsWpDateTime( $this->params['target_date_string'] );
        $calendar_settings = [
            'layout'                => $this->params['calendar_layout'] ?? 'classic',
            'timezone_name'         => $this->params['timezone_name'] ?? false,
        ];

        $end_date = new DateTime(OsStepsHelper::$booking_object->start_date);
// Add one week
        $end_date->modify('+1 week');
// Display the updated date and time
        $end_date = $end_date->format('Y-m-d');

        $calendar_settings['earliest_possible_booking'] = OsStepsHelper::$booking_object->start_date;
        $calendar_settings['latest_possible_booking']   = $end_date;

        $this->format_render( '_monthly_calendar_days', [
            'target_date'       => $target_date,
            'calendar_settings' => $calendar_settings,
            'booking_request'   => \LatePoint\Misc\BookingRequest::create_from_booking_model( OsStepsHelper::$booking_object )
        ] );
    }


}
