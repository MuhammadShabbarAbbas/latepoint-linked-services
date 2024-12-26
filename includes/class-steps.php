<?php

if (!defined('ABSPATH')) {
    exit;
}

class OSLinkedServicesSteps
{
    public $step_code = 'bundled_services_datepicker';

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
            $skip = true;
            $aci = OsStepsHelper::$active_cart_item;
            $is_bundle = OsStepsHelper::$active_cart_item->is_bundle();
            if(OsStepsHelper::$active_cart_item->is_bundle()){
                // bundle bookings have preset duration, no need to ask customer for it
                $skip = false;
            }
        }
        return $skip;
    }

    public function add_linked_service_step_code(array $steps): array
    {
        $steps[$this->step_code] = [];
        return $steps;
    }

    public function load_step_linked_service_date_picker($step_code, $format = 'json')
    {
        if ($step_code == $this->step_code) {

            $active_cart_item = OsStepsHelper::$active_cart_item;
            $item_data__str = $active_cart_item->item_data;
            $item_data = json_decode($item_data__str);
            $bundle_id=$item_data->bundle_id;
            $bundle = new OsBundleModel($bundle_id);
            $services = $bundle->get_services();
            $service = new OsServiceModel($services[0]->id); //todo: foreach for all services to be inserted here.

            $linked_service_datepicker = new OSLinkedServicesController();
            $booking = new OsBookingModel(); //OsStepsHelper::$booking_object;
            $booking->service_id = $service->id;
            $booking->agent_id = OsStepsHelper::$booking_object->agent_id;
            $booking->location_id = OsStepsHelper::$booking_object->location_id;
            $linked_service_datepicker->vars['booking'] = $booking;
            $linked_service_datepicker->vars['cart'] = OsStepsHelper::$cart_object;
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

//            $steps_controller                            = new OsStepsController();
//            $steps_controller->vars                      = OsStepsHelper::$vars_for_view;
//            $steps_controller->vars['booking']           = OsStepsHelper::$booking_object;
//            $steps_controller->vars['cart']              = OsStepsHelper::$cart_object;
//            $steps_controller->vars['current_step_code'] = $step_code;
//            $steps_controller->vars['restrictions']      = OsStepsHelper::$restrictions;
//            $steps_controller->vars['presets']           = OsStepsHelper::$presets;
//            $steps_controller->set_layout( 'none' );
//            $steps_controller->set_return_format( $format );
//            $steps_controller->format_render('_booking_datepicker', [], [
//                    'step_code' => $step_code,
//                    'show_next_btn' => OsStepsHelper::can_step_show_next_btn($step_code),
//                    'show_prev_btn' => OsStepsHelper::can_step_show_prev_btn($step_code),
//                    'is_first_step' => OsStepsHelper::is_first_step($step_code),
//                    'is_last_step' => OsStepsHelper::is_last_step($step_code),
//                    'is_pre_last_step' => OsStepsHelper::is_pre_last_step($step_code)]
//            );

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
