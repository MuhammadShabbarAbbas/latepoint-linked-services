<?php

?>
<style>

    .latepoint-link-service-date-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Two columns */
        gap: 15px; /* Space between boxes */
    }

    .latepoint-link-service-date-box {
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }

    .latepoint-link-service-date-box.selected {
        border-color: #28c76f;
        background-color: #e6f9ee;
    }

    .latepoint-link-service-date-box p {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    .latepoint-link-service-date-box small {
        font-size: 14px;
        color: #666;
    }

</style>
<div class="step-datepicker-w latepoint-step-content" data-step-code="<?php echo $current_step_code; ?>"  data-clear-action="clear_step_datepicker">

    <?php
        $slots = [];
        $target_date = new OsWpDateTime('now');
        $service = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($booking, $target_date, ['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true]);
        foreach ($service['months'] as $month) {
            foreach ($month['days'] as $day) {
                if(!$day['is_past'] && $day['bookable_slots_count'] > 0){
                    $end_date = new OsWpDateTime($day['date']);
                    $end_date->modify('+1 week');
                    $end_date = $end_date->format('Y-m-d');
                    $linked_service = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($linked_services_booking, new OsWpDateTime($day['date']),['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true, 'earliest_possible_booking'=> $day['date'], 'latest_possible_booking' => $end_date]);
                    foreach ($linked_service['months'] as $linked_month) {
                        foreach ($linked_month['days'] as $linked_day) {
                            if(!$linked_day['is_past'] && $linked_day['bookable_slots_count'] > 0){

                                $minutes = $day['work_minutes'][0];
                                $hours = floor($minutes / 60);
                                $mins = $minutes % 60;
                                $time = DateTime::createFromFormat('H:i', sprintf('%02d:%02d', $hours, $mins));
                                $slots[] = [
                                        'weekday_name' => $day['weekday_name'] . ' - ' . $linked_day['weekday_name'],
                                        'date_range' => $day['date'] . ' - ' . $linked_day['date'],
                                        'start_date'=> $day['date'],
                                        'linked_service_start_date' => $linked_day['date'],
                                        'time' => $time->format('g:i A'),
                                        'minutes' => $minutes,
                                ];
                            }
                        }
                    }
                }
            }
        }
    ?>

    <div class="latepoint-link-service-date-container os-animated-parent os-items os-selectable-items">
        <?php foreach ($slots as $slot): ?>
            <div class="latepoint-link-service-date-box os-animated-child os-item os-selectable-item"  data-linked-date="<?php echo $slot['linked_service_start_date']?>" data-minutes="<?php echo $slot['minutes']?>"  data-id-holder=".latepoint_start_date" data-item-id="<?php echo $slot['start_date']?>">
                <p><?php echo htmlspecialchars($slot['weekday_name']); ?></p>
                <small><?php echo htmlspecialchars($slot['date_range']); ?> - <?php echo htmlspecialchars($slot['time']); ?></small>
            </div>
        <?php endforeach; ?>
    </div>



    <?php

    echo OsStepsHelper::get_formatted_extra_step_content($current_step_code, 'after');
    do_action('latepoint_after_step_content', $current_step_code);

    echo OsFormHelper::hidden_field('booking[start_date]', $booking->start_date, [ 'class' => 'latepoint_start_date', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[start_time]', $booking->start_time, [ 'class' => 'latepoint_start_time', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('timeshift_minutes', $timeshift_minutes, [ 'class' => 'latepoint_timeshift_minutes', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('timezone_name', $timezone_name, [ 'class' => 'latepoint_timezone_name', 'skip_id' => true]);
//
    echo OsFormHelper::hidden_field('booking[linked_service][start_date]', '', [ 'class' => 'latepoint_linked_service_start_date', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[linked_service][start_time]', '', [ 'class' => 'latepoint_linked_service_start_time', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[linked_service][id]', $linked_services_booking->service_id, ['class' => 'latepoint_linked_service_id', 'skip_id' => true]);
    ?>

    <script>
        // JS to handle box selection
        document.querySelectorAll('.latepoint-link-service-date-box').forEach(box => {
            box.addEventListener('click', function () {
                const time = this.getAttribute('data-minutes');
                const date = this.getAttribute('data-linked-date');
                // Start time
                const start_time = document.querySelector('.latepoint_start_time');
                if (start_time) {
                    start_time.value = time;
                }
                // linked Start time
                const linked_start_time = document.querySelector('.latepoint_linked_service_start_time');
                if (linked_start_time) {
                    linked_start_time.value = time;
                }

                // linked Start date
                const linked_start_date = document.querySelector('.latepoint_linked_service_start_date');
                if (linked_start_date) {
                    linked_start_date.value = date;
                }

            });
        });
    </script>

</div>