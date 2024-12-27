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

    }

    public function should_step_be_skipped(bool $skip, string $step_code, OsCartModel $cart, OsCartItemModel $cart_item, OsBookingModel $booking): bool
    {
        if ($step_code == $this->step_code) {
            if($booking->is_part_of_bundle()){
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

            $linked_services = [];
            foreach ($linked_services__ids as $id){
                $linked_services[] = new OsServiceModel($id);
            }



            $linked_service_datepicker = new OSLinkedServicesController();

            $booking = new OsBookingModel(); //OsStepsHelper::$booking_object;
            $booking->service_id = 2;
            $booking->agent_id = OsStepsHelper::$booking_object->agent_id;
            $booking->location_id = OsStepsHelper::$booking_object->location_id;
            $linked_service_datepicker->vars['linked_services_booking'] = $booking;
//            $linked_service_datepicker->vars['linked_services'] = $linked_services;

            $linked_service_datepicker->vars['booking']           = OsStepsHelper::$booking_object;
            $linked_service_datepicker->vars['cart']              = OsStepsHelper::$cart_object;
            $linked_service_datepicker->vars['current_step_code'] = $step_code;
            $linked_service_datepicker->vars['restrictions']      = OsStepsHelper::$restrictions;
            $linked_service_datepicker->vars['presets']           = OsStepsHelper::$presets;
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
        $step_show_btn_rules[$this->step_code] = true;
        return $step_show_btn_rules;
    }

    public function process_step()
    {

    }


}

return new OSLinkedServicesSteps();
