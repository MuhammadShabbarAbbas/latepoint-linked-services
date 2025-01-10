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


        add_action('latepoint_model_set_data', [$this, 'set_linked_service_data'], 10, 2);

        add_filter( 'latepoint_generated_params_for_booking_form', [$this, 'add_linked_service_to_booking_form_params'], 10, 2 );

//        add_filter( 'latepoint_cart_data_for_order_intent', [$this, 'process_custom_fields_in_booking_data_for_order_intent'] );

//        add_filter( 'latepoint_get_results_as_models', [$this, 'load_custom_fields_for_model'] );
//        add_filter( 'latepoint_model_loaded_by_id', [$this, 'load_custom_fields_for_model'] );

//        add_action('latepoint_booking_created', [$this, 'book_linked_service']);
        //from wp-content/plugins/latepoint/lib/models/order_intent_model.php:256
        //todo: need to look at it.
//        add_action('latepoint_booking_updated', [$this, 'book_linked_service']);

        add_action('latepoint_order_created', [$this, 'add_linked_services_to_order']);
//

//        add_filter( 'latepoint_model_view_as_data', 'OsFeatureCustomFieldsHelper::add_booking_custom_fields_data_vars_to_booking', 10, 2 );

//
//        add_action( 'latepoint_available_vars_after', 'OsFeatureCustomFieldsHelper::output_custom_fields_vars' );


//        add_filter( 'latepoint_cart_data_for_order_intent', [$this, 'process_linked_service_in_booking_data_for_order_intent'] );



        //add_action( 'latepoint_model_save', [$this, 'save_custom_fields'] ); //we don't need this one as we're using booking_created instead


        //todo must have following after completion of functionality
//        add_filter( 'latepoint_booking_summary_service_attributes', 'OsFeatureCustomFieldsHelper::add_booking_custom_fields_to_service_attributes', 10, 2 );
//        add_filter( 'latepoint_svg_for_step_code', 'OsFeatureCustomFieldsHelper::add_svg_for_step', 10, 2 );




    }



    public static function add_linked_service_to_booking_form_params(array $params, OsBookingModel $booking ) {
        if ( ! empty( $booking->linked_service ) ) {
            $params['linked_service'] = $booking->linked_service;
        }

        return $params;
    }

    public static function process_linked_service_in_booking_data_for_order_intent(array $booking_data ): array {


        // get files from $_FILES object
        $files = OsParamsHelper::get_file( 'booking' );

        $custom_fields_structure = OsCustomFieldsHelper::get_custom_fields_arr( 'booking', 'agent' );
        if ( ! isset( $booking_data['custom_fields'] ) ) {
            $booking_data['custom_fields'] = [];
        }
        if ( $custom_fields_structure ) {
            foreach ( $custom_fields_structure as $custom_field ) {
                switch ( $custom_field['type'] ) {
                    case 'file_upload':
                        if ( ! empty( $files['name']['custom_fields'][ $custom_field['id'] ] ) ) {
                            if ( ! function_exists( 'wp_handle_upload' ) ) {
                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                            }
                            for ( $i = 0; $i < count( $files['name']['custom_fields'][ $custom_field['id'] ] ); $i ++ ) {
                                $file   = [
                                    'name'     => $files['name']['custom_fields'][ $custom_field['id'] ][ $i ],
                                    'type'     => $files['type']['custom_fields'][ $custom_field['id'] ][ $i ],
                                    'tmp_name' => $files['tmp_name']['custom_fields'][ $custom_field['id'] ][ $i ],
                                    'error'    => $files['error']['custom_fields'][ $custom_field['id'] ][ $i ],
                                    'size'     => $files['size']['custom_fields'][ $custom_field['id'] ][ $i ]
                                ];
                                $result = wp_handle_upload( $file, [ 'test_form' => false ] );
                                if ( ! isset( $result['error'] ) && ! empty( $result['url'] ) ) {
                                    $booking_data['custom_fields'][ $custom_field['id'] ] = $result['url'];
                                }
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        return $booking_data;
    }

    public static function save_custom_fields( $model ) {
        if ( $model->is_new_record() ) {
            return;
        }
        if ( $model instanceof OsBookingModel ) {
            $model->save_meta_by_key( 'linked_service', $model->linked_service );
        }
    }

    public function book_linked_service($order_booking)
    {
        $linked_booking = new OsBookingModel();

        $linked_booking->start_date = $order_booking->linked_service->start_date;
        $linked_booking->location_id = $order_booking->location_id;
        $linked_booking->start_time = $order_booking->linked_service->start_time;
        $linked_booking->total_attendees = $order_booking->total_attendees;
        $linked_booking->service_id = $order_booking->linked_service->id;
        $linked_booking->end_time      = $order_booking->linked_service->calculate_end_time();
        $linked_booking->end_date      = $order_booking->linked_service->calculate_end_date();
        $linked_booking->agent_id = $order_booking->agent_id;
        $linked_booking->order_item_id = $order_booking->order_item_id;
        $linked_booking->customer_id = $order_booking->customer_id;
        if($order_booking->custom_fields){
            $linked_booking->custom_fields = $order_booking->custom_fields;
        }
        $linked_booking->set_utc_datetimes();
        $service                         = new OsServiceModel( $linked_booking->service_id );
        $linked_booking->buffer_before    = $service->buffer_before;
        $linked_booking->buffer_after     = $service->buffer_after;
        $linked_booking->customer_comment = $order_booking->customer_comment;
        $result = $linked_booking->save();

//        $booking                = OsOrdersHelper::create_booking_object_from_booking_data_form( $booking_params );
//        $booking->customer_id   = $order->customer_id;
//        $booking->order_item_id = $order_item_model->id;
//        $booking->form_id       = $booking_id;

//        $a = $order_booking;
    }

    public static function load_custom_fields_for_model( $model ) {
        if (  $model instanceof OsBookingModel ) {
            $model->linked_service_test1 = 1;
        }

        return $model;
    }
    public function add_linked_services_to_order($order)
    {
        $order_items = new OsOrderItemModel();
        $order_items = $order_items->where(['order_id' => $order->id])->get_results_as_models();
        if (empty($order_items)) return;
        foreach ($order_items as $order_item){

            if($order_item->variant !== LATEPOINT_ITEM_VARIANT_BOOKING) continue;
            if(empty($order_item->item_data)) continue;

            $item_data = json_decode($order_item->item_data);


            /***************save booking**************/
            $linked_service = new OsLinkedService();
            $linked_service->start_date = $item_data->linked_service->start_date;
            $linked_service->start_date = $item_data->linked_service->start_date;
            $linked_service->id = $item_data->linked_service->id;

            $linked_booking = new OsBookingModel();
            $linked_booking->start_date = $linked_service->start_date;
            $linked_booking->location_id = $item_data->location_id;
            $linked_booking->start_time = $linked_service->start_date;
            $linked_booking->total_attendees = $item_data->total_attendees;
            $linked_booking->service_id = $linked_service->id;
            $linked_booking->end_time      = $linked_service->calculate_end_time();
            $linked_booking->end_date      = $linked_service->calculate_end_date();
            $linked_booking->agent_id = $item_data->agent_id;
            $linked_booking->order_item_id = $item_data->order_item_id;
            $linked_booking->customer_id = $item_data->customer_id;
            if( !empty($item_data->custom_fields)){
                $linked_booking->custom_fields = $item_data->custom_fields;
            }
            $linked_booking->set_utc_datetimes();
            $service                         = new OsServiceModel( $linked_booking->service_id );
            $linked_booking->buffer_before    = $service->buffer_before;
            $linked_booking->buffer_after     = $service->buffer_after;
            $linked_booking->customer_comment = $order_item->customer_comment;


            /***************cart item**************/
            $cart_item = new OsCartItemModel();
            $cart = new OsCartModel();
            $cart = $cart->where(['order_id' => $order->id])->get_results_as_models();
            if(empty($cart)) {
                $order->add_error( 'order_error', sprintf(__('Error: %s', 'latepoint'), "No cart found with order it" ));
                return;
            }
            $cart_item->item_data = json_encode($linked_booking);
            $cart_item->cart_id = $cart[0]->id;
            if (!$cart_item->save()) {
                $order->add_error( 'order_error', sprintf(__('Error: %s', 'latepoint'), "Error saving cart" ));
                return;
            }


            if (!$linked_booking->save()) {
                $order->add_error( 'order_error', sprintf(__('Error: %s', 'latepoint'), "Error creating registration" ));
                return;
            }

            /***************order item**************/
            $new_order_item = new OsOrderItemModel();
            $new_order_item->variant = LATEPOINT_ITEM_VARIANT_BOOKING;
            $new_order_item->item_data = json_encode($linked_booking);
            if (!$new_order_item->save()) {
                $order->add_error( 'order_error', sprintf(__('Error: %s', 'latepoint'), "Error saving order item" ));
                return;
            }

        }
    }


    public function process_custom_fields_in_booking_data_for_order_intent(array $booking_data)
    {
        $booking_data->linked_service_test = 1;
        return $booking_data;
    }


    public static function set_custom_fields_data( $model, $data = [] ) {
        if ( ( $model instanceof OsBookingModel ) || ( $model instanceof OsCustomerModel ) ) {
            if ( $data && isset( $data['custom_fields'] ) ) {
                $fields_for              = ( $model instanceof OsBookingModel ) ? 'booking' : 'customer';
                $custom_fields_structure = OsCustomFieldsHelper::get_custom_fields_arr( $fields_for, 'agent' );
                if ( ! isset( $model->custom_fields ) ) {
                    $model->custom_fields = [];
                }
                foreach ( $data['custom_fields'] as $key => $custom_field ) {
                    // check if data is allowed
                    if ( isset( $custom_fields_structure[ $key ] ) ) {
                        $model->custom_fields[ $key ] = $custom_field;
                    }
                }
            }
        }
    }

    public function set_linked_service_data($model, $data)
    {
        if( $model instanceof OsBookingModel ){
            $linked_service = new OsLinkedService();
            if (isset($data['linked_service']['start_date'])) {
                $linked_service->start_date = $data['linked_service']['start_date'];
                $model->linked_service = $linked_service;
            }

            if (isset($data['linked_service']['start_time'])) {
                $linked_service->start_time = $data['linked_service']['start_time'];
                $model->linked_service = $linked_service;
            }

            if (isset($data['linked_service']['id']))
            {
                $linked_service->id = $data['linked_service']['id'];
                $model->linked_service = $linked_service;
            }
        }
    }




    public function add_linked_service_datetime_to_active_cart_item($booking_start_datetime, $booking)
    {
        if ($booking->linked_service instanceof OsLinkedService && $booking->linked_service->start_date ) {
            $booking_start_datetime .= ',<br/>' . $booking->linked_service->get_nice_start_datetime();
        }
        return $booking_start_datetime;
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

            $linked_service_booking = new OsBookingModel(); //OsStepsHelper::$booking_object;
            $linked_service_booking->service_id = $linked_services__ids[0];
            $linked_service_booking->agent_id = OsStepsHelper::$booking_object->agent_id;
            $linked_service_booking->location_id = OsStepsHelper::$booking_object->location_id;
            $linked_service_datepicker->vars['linked_services_booking'] = $linked_service_booking;
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
