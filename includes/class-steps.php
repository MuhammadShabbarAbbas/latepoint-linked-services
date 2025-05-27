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


//        add_filter('latepoint_booking_summary_formatted_booking_start_datetime', [$this, 'add_linked_service_datetime_to_active_cart_item'], 10, 2);


        add_action('latepoint_model_set_data', [$this, 'set_linked_service_data'], 10, 2);

        add_filter('latepoint_generated_params_for_booking_form', [$this, 'add_linked_service_to_booking_form_params'], 10, 2);

        add_filter('latepoint_booking_get_service_name_for_summary', [$this, 'update_service_name_for_summary'], 10, 2);
        add_filter('latepoint_get_nice_datetime_for_summary', [$this, 'update_nice_date_for_summary'], 999, 3);

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
        add_filter('latepoint_svg_for_step_code', [$this, 'add_svg_for_booking'], 10, 2);


        //TODO: FOR THE REQGISTRATION OF LINKED SERVICE, ORDER ITEMS ARE NOT POPULATE
    }


    public static function add_linked_service_to_booking_form_params(array $params, OsBookingModel $booking)
    {
        if (!empty($booking->linked_service)) {
            $params['linked_service'] = $booking->linked_service;
        }

        return $params;
    }

    function update_service_name_for_summary($service_name, $booking_instance): string
    {
        if (!empty($booking_instance->service->short_description)) return $booking_instance->service->short_description;
        return $service_name;
    }

    function add_svg_for_booking(string $svg, string $step_code)
    {
        if ($step_code == "booking__linked_service_datepicker") {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 73 73">
					<path class="latepoint-step-svg-highlight" d="M36.270771,27.7026501h16.8071289c0.4140625,0,0.75-0.3359375,0.75-0.75s-0.3359375-0.75-0.75-0.75H36.270771 c-0.4140625,0-0.75,0.3359375-0.75,0.75S35.8567085,27.7026501,36.270771,27.7026501z"/>
					<path class="latepoint-step-svg-highlight" d="M40.5549507,42.3081207c0,0.4140625,0.3359375,0.75,0.75,0.75h12.6015625c0.4140625,0,0.75-0.3359375,0.75-0.75 s-0.3359375-0.75-0.75-0.75H41.3049507C40.8908882,41.5581207,40.5549507,41.8940582,40.5549507,42.3081207z"/>
					<path class="latepoint-step-svg-highlight" d="M45.6980171,51.249527H29.9778023c-0.4140625,0-0.75,0.3359375-0.75,0.75s0.3359375,0.75,0.75,0.75h15.7202148 c0.4140625,0,0.75-0.3359375,0.75-0.75S46.1120796,51.249527,45.6980171,51.249527z"/>
					<path class="latepoint-step-svg-highlight" d="M62.1623726,11.5883932l0.3300781-3.3564453c0.0405273-0.4121094-0.2607422-0.7792969-0.6728516-0.8193359 c-0.4091797-0.0458984-0.77882,0.2597656-0.8203125,0.6728516l-0.3300781,3.3564453 c-0.0405273,0.4121094,0.2612305,0.7792969,0.6733398,0.8193359 C61.7317963,12.3070383,62.1204109,12.0155325,62.1623726,11.5883932z"/>
					<path class="latepoint-step-svg-highlight" d="M63.9743843,13.9233541c1.1010704-0.3369141,2.0717735-1.0410156,2.7333946-1.9814453 c0.2382813-0.3388672,0.1567383-0.8066406-0.1816406-1.0449219c-0.3383789-0.2392578-0.8066406-0.1572266-1.0449219,0.1816406 c-0.4711914,0.6699219-1.1621094,1.1708984-1.9462852,1.4111328c-0.3959961,0.1210938-0.6186523,0.5400391-0.4975586,0.9365234 C63.1588402,13.8212023,63.5774651,14.0450754,63.9743843,13.9233541z"/>
					<path class="latepoint-step-svg-highlight" d="M68.8601227,17.4516735c0.0356445-0.4121094-0.2695313-0.7763672-0.6826172-0.8115234l-3.859375-0.3349609 c-0.4072227-0.0390625-0.7758751,0.2695313-0.8115196,0.6826172c-0.0356445,0.4121094,0.2695313,0.7763672,0.6826134,0.8115234 l3.859375,0.3349609C68.4594727,18.1708145,68.8244781,17.8649578,68.8601227,17.4516735z"/>
					<path class="latepoint-step-svg-highlight" d="M4.7497134,18.4358044c1.0574932,1.9900436,1.9738078,2.5032253,13.2814941,11.7038574 c0.5604858,11.4355488,0.9589844,22.8789082,1.1829224,34.3259277c0.3128052,0.1918945,0.6256714,0.3835449,0.9384766,0.5751953 c0.1058846,0.3764038,0.416275,0.5851364,0.7949219,0.5466309c12.6464844-1.4892578,25.8935547-2.0419922,40.4916992-1.6767578 c0.4600639-0.0021172,0.763813-0.3514481,0.7685547-0.7421875c0.1805725-16.3819695-0.080349-32.8599472,0.0605469-49.1875 c0.003418-0.3740234-0.2685547-0.6923828-0.6376953-0.7480469c-14.1435547-2.140625-28.5092773-2.3291016-42.6953125-0.5664063 c-0.331604,0.0407715-0.5751953,0.2971191-0.6331177,0.6113281c-0.3464966,0.277832-0.6930542,0.5556641-1.0396118,0.8334961 c0.1156616,1.137207,0.0985718,2.392333,0.1765137,3.5629873c-2.2901011-1.8925772-4.5957651-3.8081045-6.9354258-5.7802725 c-0.7441406-0.6269531-1.6889648-0.9277344-2.683105-0.8378906C4.4105406,11.3600969,3.320657,15.7476349,4.7497134,18.4358044z M60.7629585,14.6196432c-0.1265907,15.9033155,0.1148987,31.8954544-0.046875,47.7734375 c-14.0498047-0.3193359-26.8598633,0.2099609-39.1044922,1.6074219c0.0154419-10.8208008-0.2228394-21.3803711-0.6828613-31.503418 c8.6963615,7.0753174,9.1210613,7.5400124,10.6517334,8.1962891c2.7804565,1.1923828,7.8590698,1.5974121,8.4487305,0.6987305 c0.0741577-0.0522461,0.1495361-0.1047363,0.2015381-0.1826172c0.1469727-0.2207031,0.1669922-0.5029297,0.0517578-0.7412109 c-1.0354347-2.1505203-2.3683548-6.0868149-3.1914063-6.7568359c-5.5252628-4.5023842-10.581501-8.5776329-16.84375-13.7214375 c-0.1300049-1.973877-0.2654419-3.9484863-0.4165039-5.9221182C33.4343452,12.4419088,47.1985054,12.6274557,60.7629585,14.6196432 z M9.5368834,13.0405416c9.0454321,7.6246099,17.5216217,14.4366217,26.5917969,21.8203125 c0.3883591,0.3987503,1.5395088,3.3786926,2.2700195,5.078125c-1.4580688-0.1650391-2.9936523-0.479248-4.7089233-0.8842773 c0.4859009-0.9790039,1.1461182-1.8769531,1.953064-2.6108398c0.3061523-0.2783203,0.3286133-0.7529297,0.0498047-1.0595703 c-0.2783203-0.3046875-0.7519531-0.328125-1.0595703-0.0498047c-0.9295654,0.8461914-1.6932373,1.8774414-2.2598877,3.0026855 c-8.9527779-7.1637478-17.1909065-14.1875877-25.8739014-21.1394062c-0.5556641-0.4443359-0.8725586-1.09375-0.8481445-1.7363272 C5.7526169,12.8167362,8.1288319,11.8543167,9.5368834,13.0405416z"/>
				</svg>';
        }
        return $svg;
    }

    function update_nice_date_for_summary($nice_datetime, $booking_instance, $viewer): string
    {
        if (empty($booking_instance->linked_service)) return $nice_datetime;
        $start_date = $booking_instance->start_date; // e.g. '2025-06-02'
        $end_date = $booking_instance->linked_service->start_date ?? $start_date; // Optional fallback if end_date missing
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);

        //  Time
        $minutes = $booking_instance->start_time;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $start_time = DateTime::createFromFormat('H:i', sprintf('%02d:%02d', $hours, $mins));

        // Clone the start time and add duration
        $end_time = clone $start_time;
        $end_time->add(new DateInterval('PT' . $booking_instance->duration . 'M'));


        $formatted_range = $start->format('F j') . ' & ' . $end->format('F j') . ' at ' . $start_time->format('g:i A') . ' - ' . $end_time->format('g:i A');
        return $formatted_range;
    }

    public static function process_linked_service_in_booking_data_for_order_intent(array $booking_data): array
    {


        // get files from $_FILES object
        $files = OsParamsHelper::get_file('booking');

        $custom_fields_structure = OsCustomFieldsHelper::get_custom_fields_arr('booking', 'agent');
        if (!isset($booking_data['custom_fields'])) {
            $booking_data['custom_fields'] = [];
        }
        if ($custom_fields_structure) {
            foreach ($custom_fields_structure as $custom_field) {
                switch ($custom_field['type']) {
                    case 'file_upload':
                        if (!empty($files['name']['custom_fields'][$custom_field['id']])) {
                            if (!function_exists('wp_handle_upload')) {
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                            }
                            for ($i = 0; $i < count($files['name']['custom_fields'][$custom_field['id']]); $i++) {
                                $file = [
                                    'name' => $files['name']['custom_fields'][$custom_field['id']][$i],
                                    'type' => $files['type']['custom_fields'][$custom_field['id']][$i],
                                    'tmp_name' => $files['tmp_name']['custom_fields'][$custom_field['id']][$i],
                                    'error' => $files['error']['custom_fields'][$custom_field['id']][$i],
                                    'size' => $files['size']['custom_fields'][$custom_field['id']][$i]
                                ];
                                $result = wp_handle_upload($file, ['test_form' => false]);
                                if (!isset($result['error']) && !empty($result['url'])) {
                                    $booking_data['custom_fields'][$custom_field['id']] = $result['url'];
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

    public static function save_custom_fields($model)
    {
        if ($model->is_new_record()) {
            return;
        }
        if ($model instanceof OsBookingModel) {
            $model->save_meta_by_key('linked_service', $model->linked_service);
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
        $linked_booking->end_time = $order_booking->linked_service->calculate_end_time();
        $linked_booking->end_date = $order_booking->linked_service->calculate_end_date();
        $linked_booking->agent_id = $order_booking->agent_id;
        $linked_booking->order_item_id = $order_booking->order_item_id;
        $linked_booking->customer_id = $order_booking->customer_id;
        if ($order_booking->custom_fields) {
            $linked_booking->custom_fields = $order_booking->custom_fields;
        }
        $linked_booking->set_utc_datetimes();
        $service = new OsServiceModel($linked_booking->service_id);
        $linked_booking->buffer_before = $service->buffer_before;
        $linked_booking->buffer_after = $service->buffer_after;
        $linked_booking->customer_comment = $order_booking->customer_comment;
        $result = $linked_booking->save();

//        $booking                = OsOrdersHelper::create_booking_object_from_booking_data_form( $booking_params );
//        $booking->customer_id   = $order->customer_id;
//        $booking->order_item_id = $order_item_model->id;
//        $booking->form_id       = $booking_id;

//        $a = $order_booking;
    }

    public static function load_custom_fields_for_model($model)
    {
        if ($model instanceof OsBookingModel) {
            $model->linked_service_test1 = 1;
        }

        return $model;
    }

    public function add_linked_services_to_order($order)
    {
        $order_items = OsOrdersHelper::get_items_for_order_id($order->id);
        if (empty($order_items)) return;
        foreach ($order_items as $order_item) { //parse through each order item/booking

            if ($order_item->variant !== LATEPOINT_ITEM_VARIANT_BOOKING) continue;
            if (empty($order_item->item_data)) continue;

            //used by teams plugin
            $item_data = apply_filters('latepoint_linked_services_linked_service_item_data', json_decode($order_item->item_data));

            /***************save booking**************/
            $linked_booking = new OsBookingModel();
            $linked_booking->start_date = $item_data->linked_service->start_date;
            $linked_booking->location_id = $item_data->location_id;
            $linked_booking->start_time = $item_data->start_time;
            $linked_booking->total_attendees = $item_data->total_attendees;
            $linked_booking->service_id = $item_data->linked_service->id;
            $linked_booking->end_time = $linked_booking->calculate_end_time();
            $linked_booking->end_date = $linked_booking->calculate_end_date();
            $linked_booking->agent_id = $item_data->agent_id;
            $linked_booking->status = 'approved';
            $linked_booking->customer_id = $item_data->customer_id;
            if (!empty($item_data->custom_fields)) {
                $linked_booking->custom_fields = get_object_vars($item_data->custom_fields);
            }
            $linked_booking->set_utc_datetimes();
            $service = new OsServiceModel($item_data->linked_service->id);
            $linked_booking->buffer_before = $service->buffer_before;
            $linked_booking->buffer_after = $service->buffer_after;
            $linked_booking->customer_comment = $order_item->customer_comment;


            /***************cart item**************/
//            $cart_item = new OsCartItemModel();
//            $cart = new OsCartModel();
//            $cart = $cart->where(['order_id' => $order->id])->get_results_as_models();
//            if (empty($cart)) {
//                $order->add_error('order_error', sprintf(__('Error: %s', 'latepoint'), "No cart found with order it"));
//                return;
//            }
//            OsBookingHelper::build_booking_model_from_item_data();
//            OsOrdersHelper::create_order_item_from_cart_item();
//            OsCartsHelper::create_cart_item_from_item_data();
//            OsCartsHelper::add_booking_to_cart()
//            $cart_item->item_data = $linked_booking->generate_params_for_booking_form();
//            $cart_item->cart_id = $cart[0]->id;
//            if (!$cart_item->save()) {
//                $order->add_error('order_error', sprintf(__('Error: %s', 'latepoint'), "Error saving cart"));
//                return;
//            }
//            $linked_booking->cart_item_id = $cart_item->id;

            /***************order item**************/
            $new_order_item = new OsOrderItemModel();
            $new_order_item->order_id = $order_item->order_id;
            $new_order_item->variant = LATEPOINT_ITEM_VARIANT_BOOKING;
            $new_order_item->item_data = $linked_booking->generate_item_data();

            if (!$new_order_item->save()) {
                $order->add_error('order_error', sprintf(__('Error: %s', 'latepoint'), "Error saving order item"));
                return;
            }

            $linked_booking->order_item_id = $new_order_item->id;

            if (!$linked_booking->save()) {
                $order->add_error('order_error', sprintf(__('Error: %s', 'latepoint'), "Error creating registration"));
                return;
            }
            //saving again order item id to include linked booking id
            $new_order_item->update_attributes(['item_data' => $linked_booking->generate_item_data()]);
        }
    }

    public function set_linked_service_data($model, $data)
    {
        if ($model instanceof OsBookingModel) {
            $linked_service = new OsBookingModel();
            if (isset($data['linked_service']['start_date'])) {
                $linked_service->start_date = $data['linked_service']['start_date'];
                $model->linked_service = $linked_service;
            }

            if (isset($data['linked_service']['start_time'])) {
                $linked_service->start_time = $data['linked_service']['start_time'];
                $model->linked_service = $linked_service;
            }

            if (isset($data['linked_service']['id'])) {
                $linked_service->id = $data['linked_service']['id'];
                $model->linked_service = $linked_service;
            }
        }
    }


    public function should_step_be_skipped(bool $skip, string $step_code, OsCartModel $cart, OsCartItemModel $cart_item, OsBookingModel $booking): bool
    {
        if ($step_code === "booking__datepicker") {
            $service = new OsServiceModel($booking->service_id);
            $linked_services = $service->get_meta_by_key('linked_services');
            $linked_services = $linked_services ? json_decode($linked_services) : [];
            if (!empty($linked_services)) {
                $skip = true;
            }
        }
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
