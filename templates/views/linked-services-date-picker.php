<?php
/**
 * @var $current_step_code string
 * @var $cart OsCartModel
 * @var $booking OsBookingModel
 * @var $restrictions array
 * @var $presets array
 * @var $calendar_start_date string
 * @var $linked_services_booking OsBookingModel
 */

?>

<div class="step-datepicker-w latepoint-step-content" data-step-code="<?php echo $current_step_code; ?>"  data-clear-action="clear_step_datepicker">
    <?php
    do_action('latepoint_before_step_content', $current_step_code);
    echo OsStepsHelper::get_formatted_extra_step_content($current_step_code, 'before');
    ?>
    <div class="os-dates-w">
        <?php OsCalendarHelper::generate_calendar_for_datepicker_step(\LatePoint\Misc\BookingRequest::create_from_booking_model($linked_services_booking), new OsWpDateTime($calendar_start_date), ['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true]); ?>
    </div>
    <div class="time-selector-w <?php echo OsStepsHelper::hide_unavailable_slots() ? 'hide-not-available-slots' : ''; ?> <?php echo 'time-system-'.OsTimeHelper::get_time_system(); ?> <?php echo (OsSettingsHelper::is_on('show_booking_end_time')) ? 'with-end-time' : 'without-end-time'; ?> style-<?php echo OsStepsHelper::get_time_pick_style(); ?>">
        <div class="times-header">
            <div class="th-line"></div>
            <div class="times-header-label">
                <?php _e('Pick a slot for', 'latepoint'); ?> <span></span>
                <?php do_action('latepoint_step_datepicker_linked_services_appointment_time_header_label', $linked_services_booking); ?>
            </div>
            <div class="th-line"></div>
        </div>
        <div class="os-times-w">
            <div class="timeslots"></div>
        </div>
    </div>

    <?php

    echo OsStepsHelper::get_formatted_extra_step_content($current_step_code, 'after');
    do_action('latepoint_after_step_content', $current_step_code);

    echo OsFormHelper::hidden_field('booking[start_date]', $booking->start_date, [ 'class' => 'latepoint_start_date', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[start_time]', $booking->start_time, [ 'class' => 'latepoint_start_time', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('timeshift_minutes', $timeshift_minutes, [ 'class' => 'latepoint_timeshift_minutes', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('timezone_name', $timezone_name, [ 'class' => 'latepoint_timezone_name', 'skip_id' => true]);
    ?>
</div>