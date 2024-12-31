window.os_linked_services_public = window.os_linked_services_public || {};
(function (window, document, $, app, undefined) {
    'use strict';

    var $document;
    var elements = {};

    app.init = function () {
        $document = $(document);
        $.extend(app, elements);

        $document.on("latepoint:initStep:booking__linked_service_datepicker", latepoint_init_step_linked_service_datepicker);
    }


    function latepoint_init_step_linked_service_datepicker(e) {
        let $booking_form_element = $('.latepoint-booking-form-element');
        if (!$booking_form_element) return;
        init_timeslots($booking_form_element);
        init_monthly_calendar_navigation($booking_form_element);
        $booking_form_element.off('click', '.os-linked-services-months .os-linked-services-day', day_clicked);
        $booking_form_element.on('click', '.os-linked-services-months .os-linked-services-day', day_clicked);
        $booking_form_element.off('keydown', '.os-linked-services-months .os-linked-services-day', day_clicked);
        $booking_form_element.on('keydown', '.os-linked-services-months .os-linked-services-day', day_clicked);
        if ($booking_form_element.find('input[name="booking[start_date]"]').val()) $booking_form_element.find('.os-linked-services-day[data-date="' + $booking_form_element.find('input[name="booking[start_date]"]').val() + '"]').trigger('click');
    }

    function calendar_set_month_label($booking_form_element) {
        $booking_form_element.find('.os-current-month-label .current-year').text($booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').data('calendar-year'));
        $booking_form_element.find('.os-current-month-label .current-month').text($booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').data('calendar-month-label'));
    }

    function init_monthly_calendar_navigation($booking_form_element = false) {
        if (!$booking_form_element) return;
        $booking_form_element.find('.os-linked-services-month-next-btn').on('click', function () {
            var $booking_form_element = jQuery(this).closest('.latepoint-booking-form-element');
            var next_month_route_name = jQuery(this).data('route');
            if ($booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active + .os-linked-services-monthly-calendar-days-w').length) {
                $booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').removeClass('active').next('.os-linked-services-monthly-calendar-days-w').addClass('active');
                calendar_set_month_label($booking_form_element);
            } else {
                // TODO add condition to check maximum number months to call into the future
                if (true) {
                    var $btn = jQuery(this);
                    $btn.addClass('os-loading');
                    var $calendar_element = $booking_form_element.find('.os-linked-services-monthly-calendar-days-w').last();
                    var calendar_year = $calendar_element.data('calendar-year');
                    var calendar_month = $calendar_element.data('calendar-month');
                    if (calendar_month == 12) {
                        calendar_year = calendar_year + 1;
                        calendar_month = 1;
                    } else {
                        calendar_month = calendar_month + 1;
                    }
                    var form_data = new FormData($booking_form_element.find('.latepoint-form')[0]);
                    form_data.set('target_date_string', `${calendar_year}-${calendar_month}-1`);
                    var params = latepoint_formdata_to_url_encoded_string(form_data);
                    var data = {
                        action: latepoint_helper.route_action,
                        route_name: next_month_route_name,
                        params: params,
                        layout: 'none',
                        return_format: 'json'
                    }
                    jQuery.ajax({
                        type: "post",
                        dataType: "json",
                        url: latepoint_timestamped_ajaxurl(),
                        data: data,
                        success: function (data) {
                            $btn.removeClass('os-loading');
                            if (data.status === "success") {
                                    $booking_form_element.find('.os-linked-services-months').append(data.message);
                                $booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').removeClass('active').next('.os-linked-services-monthly-calendar-days-w').addClass('active');
                                calendar_set_month_label($booking_form_element);
                            } else {
                                // console.log(data.message);
                            }
                        }
                    });
                }
            }
            latepoint_calendar_show_or_hide_prev_next_buttons($booking_form_element);
            return false;
        });
        
        $booking_form_element.find('.os-month-prev-btn').on('click', function () {
            var $booking_form_element = jQuery(this).closest('.latepoint-booking-form-element');
            if ($booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').prev('.os-linked-services-monthly-calendar-days-w').length) {
                $booking_form_element.find('.os-linked-services-monthly-calendar-days-w.active').removeClass('active').prev('.os-linked-services-monthly-calendar-days-w').addClass('active');
                calendar_set_month_label($booking_form_element);
            }
            latepoint_calendar_show_or_hide_prev_next_buttons($booking_form_element);
            return false;
        });
    }

    function init_timeslots($booking_form_element = false) {
        if (!$booking_form_element) return;
        $booking_form_element.off('click', '.dp-linked-service-timepicker-trigger', timeslot_clicked);
        $booking_form_element.on('click', '.dp-linked-service-timepicker-trigger', timeslot_clicked);
        $booking_form_element.off('keydown', '.dp-linked-service-timepicker-trigger', timeslot_clicked);
        $booking_form_element.on('keydown', '.dp-linked-service-timepicker-trigger', timeslot_clicked);
    }

    function timeslot_clicked(event) {
        if(event.type === 'keydown' && event.key !== ' ' &&  event.key !== 'Enter') return;
        let $booking_form_element = jQuery(this).closest('.latepoint-booking-form-element');
        if (jQuery(this).hasClass('is-booked') || jQuery(this).hasClass('is-off')) {
            // Show error message that you cant select a booked period
        } else {
            if (jQuery(this).hasClass('selected')) {
                jQuery(this).removeClass('selected');
                jQuery(this).find('.dp-success-label').remove();
                $booking_form_element.find('.latepoint_linked_service_start_time').val('');
                latepoint_hide_next_btn($booking_form_element);
                latepoint_reload_summary($booking_form_element);
            } else {
                $booking_form_element.find('.dp-linked-service-timepicker-trigger.selected').removeClass('selected').find('.dp-success-label').remove();
                var selected_timeslot_time = jQuery(this).find('.dp-label-time').html();
                jQuery(this).addClass('selected').find('.dp-label').prepend('<span class="dp-success-label">' + latepoint_helper.datepicker_timeslot_selected_label + '</span>');

                var minutes = parseInt(jQuery(this).data('minutes'));
                var timeshift_minutes = parseInt($booking_form_element.find('.latepoint_timeshift_minutes').val());
                // we substract timeshift minutes because its timeshift minutes that the business is running in, in opposite of what we do when we generate a calendar for a client
                if (timeshift_minutes) minutes = minutes - timeshift_minutes;
                var start_date = new Date($booking_form_element.find('.os-linked-services-day.selected').data('date'));
                if (minutes < 0) {
                    // business minutes are in previous day
                    minutes = 24 * 60 + minutes;
                    // move start date back 1 day
                    start_date.setDate(start_date.getDate() - 1);
                } else if (minutes >= 24 * 60) {
                    // business minutes are in next day
                    minutes = minutes - 24 * 60;
                    start_date.setDate(start_date.getDate() + 1);
                }
                $booking_form_element.find('.latepoint_linked_service_start_date').val(start_date.toISOString().split('T')[0])
                $booking_form_element.find('.latepoint_linked_service_start_time').val(minutes);
                latepoint_trigger_next_btn($booking_form_element);
                latepoint_reload_summary($booking_form_element);
            }
        }
        return false;
    }

    function day_clicked(event) {
        if (event.type === 'keydown' && event.key !== ' ' && event.key !== 'Enter') return;
        if ($(this).hasClass('os-day-passed')) return false;
        if ($(this).hasClass('os-not-in-allowed-period')) return false;
        var $booking_form_element = $(this).closest('.latepoint-booking-form-element');
        if ($(this).closest('.os-linked-services-monthly-calendar-days-w').hasClass('hide-if-single-slot')) {
            // HIDE TIMESLOT IF ONLY ONE TIMEPOINT
            if ($(this).hasClass('os-not-available')) {
                // clicked on a day that has no available timeslots
                // do nothing
            } else {
                $booking_form_element.find('.os-linked-services-day.selected').removeClass('selected');
                $(this).addClass('selected');
                // set date
                $booking_form_element.find('.latepoint_linked_service_start_date').val($(this).data('date'));
                if ($(this).hasClass('os-one-slot-only')) {
                    // clicked on a day that has only one slot available
                    var bookable_minutes = $(this).data('bookable-minutes').toString().split(':')[0];
                    var selected_timeslot_time = latepoint_format_minutes_to_time(Number(bookable_minutes), Number($(this).data('service-duration')));
                    $booking_form_element.find('.latepoint_linked_service_start_time').val($(this).data('bookable-minutes'));
                    latepoint_show_next_btn($booking_form_element);
                    $booking_form_element.find('.linked-service-time-selector-w').slideUp(200);
                } else {
                    // regular day with more than 1 timeslots available
                    // build timeslots
                    linked_services_day_timeslots($(this));
                    // clear time and hide next btn
                    $booking_form_element.find('.latepoint_linked_service_start_time').val('');
                    latepoint_hide_next_btn($booking_form_element);
                }
                latepoint_reload_summary($booking_form_element);
            }
        } else {

            // SHOW TIMESLOTS EVEN IF ONLY ONE TIMEPOINT
            $booking_form_element.find('.latepoint_linked_service_start_date').val($(this).data('date'));
            $booking_form_element.find('.os-linked-services-day.selected').removeClass('selected');
            $(this).addClass('selected');

            // build timeslots
            linked_services_day_timeslots($(this));
            // clear time and hide next btn
            latepoint_reload_summary($booking_form_element);
            $booking_form_element.find('.latepoint_linked_service_start_time').val('');
            latepoint_hide_next_btn($booking_form_element);
        }


        return false;
    }


    function linked_services_day_timeslots($day, $wrapper_element = false, $scrollable_wrapper = false) {
        if (!$wrapper_element) $wrapper_element = $day.closest('.latepoint-booking-form-element');
        $day.addClass('selected');

        var service_duration = $day.data('service-duration');
        var interval = $day.data('interval');
        var work_start_minutes = $day.data('work-start-time');
        var work_end_minutes = $day.data('work-end-time');
        var total_work_minutes = $day.data('total-work-minutes');
        var bookable_minutes = [];
        var available_capacities_of_bookable_minute = [];
        if ($day.attr('data-bookable-minutes')) {
            if ($day.data('bookable-minutes').toString().indexOf(':') > -1) {
                // has capacity information embedded into bookable minutes string
                let bookable_minutes_with_capacity = $day.data('bookable-minutes').toString().split(',');
                for (let i = 0; i < bookable_minutes_with_capacity.length; i++) {
                    bookable_minutes.push(parseInt(bookable_minutes_with_capacity[i].split(':')[0]));
                    available_capacities_of_bookable_minute.push(parseInt(bookable_minutes_with_capacity[i].split(':')[1]));
                }
            } else {
                bookable_minutes = $day.data('bookable-minutes').toString().split(',').map(Number);
            }
        }
        var work_minutes = $day.data('work-minutes').toString().split(',').map(Number);

        var $timeslots = $wrapper_element.find('.linked-service-timeslots');
        $timeslots.html('');

        if (total_work_minutes > 0 && bookable_minutes.length && work_minutes.length) {
            var prev_minutes = false;
            work_minutes.forEach(function (current_minutes) {
                var ampm = latepoint_am_or_pm(current_minutes);

                var timeslot_class = 'dp-linked-service-timepicker-trigger';
                var timeslot_available_capacity = 0;
                if (latepoint_helper.time_pick_style == 'timeline') {
                    timeslot_class += ' dp-timeslot';
                } else {
                    timeslot_class += ' dp-timebox';
                }

                if (prev_minutes !== false && ((current_minutes - prev_minutes) > service_duration)) {
                    // show interval that is off between two work periods
                    var off_label = latepoint_minutes_to_hours_and_minutes(prev_minutes + service_duration) + ' ' + latepoint_am_or_pm(prev_minutes + service_duration) + ' - ' + latepoint_minutes_to_hours_and_minutes(current_minutes) + ' ' + latepoint_am_or_pm(current_minutes);
                    var off_width = (((current_minutes - prev_minutes - service_duration) / total_work_minutes) * 100);
                    $timeslots.append('<div class="' + timeslot_class + ' is-off" style="max-width:' + off_width + '%; width:' + off_width + '%"><span class="dp-label">' + off_label + '</span></div>');
                }

                if (!bookable_minutes.includes(current_minutes)) {
                    timeslot_class += ' is-booked';
                } else {
                    if (available_capacities_of_bookable_minute.length) timeslot_available_capacity = available_capacities_of_bookable_minute[bookable_minutes.indexOf(current_minutes)];
                }
                var tick_html = '';
                var capacity_label = '';
                var capacity_label_html = '';
                var capacity_internal_label_html = '';

                if (((current_minutes % 60) == 0) || (interval >= 60)) {
                    timeslot_class += ' with-tick';
                    tick_html = '<span class="dp-tick"><strong>' + latepoint_minutes_to_hours_preferably(current_minutes) + '</strong>' + ' ' + ampm + '</span>';
                }
                var timeslot_label = latepoint_minutes_to_hours_and_minutes(current_minutes) + ' ' + ampm;
                if (latepoint_show_booking_end_time()) {
                    var end_minutes = current_minutes + service_duration;
                    if (end_minutes > 1440) end_minutes = end_minutes - 1440;
                    var end_minutes_ampm = latepoint_am_or_pm(end_minutes);
                    timeslot_label += ' - <span class="dp-label-end-time">' + latepoint_minutes_to_hours_and_minutes(end_minutes) + ' ' + end_minutes_ampm + '</span>';
                }
                if (timeslot_available_capacity) {
                    var spaces_message = timeslot_available_capacity > 1 ? latepoint_helper.many_spaces_message : latepoint_helper.single_space_message;
                    capacity_label = timeslot_available_capacity + ' ' + spaces_message;
                    capacity_label_html = '<span class="dp-capacity">' + capacity_label + '</span>';
                    capacity_internal_label_html = '<span class="dp-label-capacity">' + capacity_label + '</span>';
                }
                timeslot_label = timeslot_label.trim();
                $timeslots.removeClass('slots-not-available').append('<div tabindex="0" class="' + timeslot_class + '" data-minutes="' + current_minutes + '"><span class="dp-label">' + capacity_internal_label_html + '<span class="dp-label-time">' + timeslot_label + '</span>' + '</span>' + tick_html + capacity_label_html + '</div>');
                prev_minutes = current_minutes;
            });
        } else {
            // No working hours this day
            $timeslots.addClass('slots-not-available').append('<div class="not-working-message">' + latepoint_helper.msg_not_available + "</div>");
        }
        jQuery('.linked-service-times-header-label span').text($day.data('nice-date'));
        $wrapper_element.find('.linked-service-time-selector-w').slideDown(200, function () {
            if (!$scrollable_wrapper) $scrollable_wrapper = $wrapper_element.find('.latepoint-body');
            $scrollable_wrapper.stop();
            $wrapper_element.find('.linked-service-time-selector-w')[0].scrollIntoView({block: "nearest", behavior: 'smooth'});
        });
    }


    $(app.init);
})(window, document, jQuery, window.os_linked_services_public);
