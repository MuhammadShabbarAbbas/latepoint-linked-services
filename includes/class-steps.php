<?php

if (!defined('ABSPATH')) {
    exit;
}

class OSLinkedServicesSteps
{
    public $step_code = 'booking__linked_service_datepicker';

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
        add_action('latepoint_process_step', [$this, 'process_step']);

        add_action('latepoint_load_step', [$this, 'load_step_linked_service_date_picker'], 10, 2);

        add_filter('latepoint_step_show_next_btn_rules', [$this, 'step_show_btn_rules'], 10, 2);

        add_filter('latepoint_get_step_codes_with_rules', [$this, 'add_linked_service_step_code'], 10, 2);

        add_filter('latepoint_should_step_be_skipped', [$this, 'should_step_be_skipped'], 10, 5);


        add_filter('latepoint_booking_summary_formatted_booking_start_datetime', [$this, 'add_linked_service_datetime_to_active_cart_item'], 10, 2);


        add_filter('latepoint_model_set_data', [$this, 'add_linked_service_data_to_booking'], 10, 2);

//        add_action('latepoint_booking_created', [$this, 'add_linked_services_to_order_booking']);
//        add_action('latepoint_order_created', [$this, 'add_linked_services_to_order']);
    }

    public function add_linked_services_to_order_booking($order_booking)
    {
        $a = $order_booking;
    }

    public function add_linked_services_to_order($order)
    {
        $a = $order;
    }

    public function add_linked_service_data_to_booking($model, $data)
    {
        //todo: look into this return further, as currently this is being called for settings model as well.
        if ($model instanceof OsBookingModel) {
            if ( $data && isset( $data['linked_service'] ) ) {

                $linked_service = $data['linked_service'];
                $service = new OsServiceModel($linked_service['id']);
                $linked_service->end_date = $this->calculate_end_date($service, $model);
                $linked_service->end_time = $this->calculate_end_time($service, $model);
                $model->linked_service = $linked_service;
            }
        }

        return $model;
    }

    public function calculate_end_date($service, $booking_object)
    {

        if (((int)$booking_object->linked_service_start_time + (int)$service->duration) >= (24 * 60)) {
            $date_obj = new OsWpDateTime($booking_object->linked_service_start_date);
            $end_date = $date_obj->modify('+1 day')->format('Y-m-d');
        } else {
            $end_date = $booking_object->linked_service_start_date;
        }

        return $end_date;
    }


    public function calculate_end_time($service, $booking_object)
    {
        $end_time = (int)$booking_object->start_time + (int)$service->duration;
        // continues to next day?
        if ($end_time > (24 * 60)) {
            $end_time = $end_time - (24 * 60);
        }
        return $end_time;
    }


    public function add_linked_service_datetime_to_active_cart_item($booking_start_datetime, $booking)
    {
        if (isset($booking->linked_service_start_date)) {
            $booking_start_datetime .= ',<br/>' . $this->get_nice_start_datetime($booking);
        }

//        return json_encode($booking);
        return $booking_start_datetime;
    }


    public function get_nice_start_datetime($booking, bool $hide_if_today = true, bool $hide_year_if_current = true): string
    {
        if ($hide_if_today && $booking->linked_service_start_date == OsTimeHelper::today_date('Y-m-d')) {
            $date = __('Today', 'latepoint');
        } else {
            $date = $this->get_nice_start_date($booking, $hide_year_if_current);
        }

        return implode(', ', array_filter([$date, $this->get_nice_start_time($booking)]));
    }

    public function get_nice_start_date($booking, $hide_year_if_current = false)
    {
        $d = OsWpDateTime::os_createFromFormat("Y-m-d", $booking->linked_service_start_date);
        if (!$d) {
            return 'n/a';
        }
        if ($hide_year_if_current && ($d->format('Y') == OsTimeHelper::today_date('Y'))) {
            $format = OsSettingsHelper::get_readable_date_format(true);
        } else {
            $format = OsSettingsHelper::get_readable_date_format();
        }

        return OsUtilHelper::translate_months($d->format($format));
    }

    public function get_nice_start_time($booking)
    {
        return OsTimeHelper::minutes_to_hours_and_minutes($booking->linked_service_start_time);
    }

    public function should_step_be_skipped(bool $skip, string $step_code, OsCartModel $cart, OsCartItemModel $cart_item, OsBookingModel $booking): bool
    {
        if ($step_code == $this->step_code) {
            if ($booking->is_part_of_bundle()) {
                // bundle bookings have preset duration, no need to ask customer for it
                $skip = true;
            }

            $service = new OsServiceModel($booking->service_id);
            $linked_services = $service->get_meta_by_key('linked_services');
            $linked_services = $linked_services ? json_decode($linked_services) : [];
            if (empty($linked_services)) {
                $skip = true;
            }
        }
        return $skip;
    }

    public function add_linked_service_step_code(array $steps): array
    {
        $steps[$this->step_code] = ['after' => 'datepicker'];
        return $steps;
    }

    public function load_step_linked_service_date_picker($step_code, $format = 'json')
    {
        if ($step_code == $this->step_code) {
            $service = new OsServiceModel(OsStepsHelper::$booking_object->service_id);

            $linked_services__ids = $service->get_meta_by_key('linked_services');
            $linked_services__ids = json_decode($linked_services__ids); //linked services will be present always, otherwise, this step would have skipped.

//            $linked_services = [];
//            foreach ($linked_services__ids as $id){
//                $linked_services[] = new OsServiceModel($id);
//            }


            $linked_service_datepicker = new OsLinkedServicesDatePickerController();

            $booking = new OsBookingModel(); //OsStepsHelper::$booking_object;
            $booking->service_id = $linked_services__ids[0];
            $booking->agent_id = OsStepsHelper::$booking_object->agent_id;
            $booking->location_id = OsStepsHelper::$booking_object->location_id;
            $linked_service_datepicker->vars['linked_services_booking'] = $booking;
//            $linked_service_datepicker->vars['linked_services'] = $linked_services;

            $linked_service_datepicker->vars['booking'] = OsStepsHelper::$booking_object;
            $linked_service_datepicker->vars['cart'] = OsStepsHelper::$cart_object;
            $linked_service_datepicker->vars['current_step_code'] = $step_code;
            $linked_service_datepicker->vars['restrictions'] = OsStepsHelper::$restrictions;
            $linked_service_datepicker->vars['presets'] = OsStepsHelper::$presets;
            $linked_service_datepicker->set_layout('none');
            $linked_service_datepicker->set_return_format($format);
            $linked_service_datepicker->format_render('linked-services-date-picker', [], [
                    'step_code' => $step_code,
                    'show_next_btn' => OsStepsHelper::can_step_show_next_btn($step_code),
                    'show_prev_btn' => OsStepsHelper::can_step_show_prev_btn($step_code),
                    'is_first_step' => OsStepsHelper::is_first_step($step_code),
                    'is_last_step' => OsStepsHelper::is_last_step($step_code),
                    'is_pre_last_step' => OsStepsHelper::is_pre_last_step($step_code)]
            );
        }
    }

    public function step_show_btn_rules($step_show_btn_rules, $step_code)
    {
        $step_show_btn_rules[$this->step_code] = false;
        return $step_show_btn_rules;
    }

    public function process_step()
    {

    }


}

return new OSLinkedServicesSteps();
