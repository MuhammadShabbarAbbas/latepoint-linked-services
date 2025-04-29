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
    $dates = [
        ['day' => 'Friday and Saturday', 'date' => '19 and 20 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '20 and 21 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '21 and 22 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '22 and 23 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '23 and 24 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '24 and 25 April, 2025', 'time' => '8 Am'],
        ['day' => 'Friday and Saturday', 'date' => '25 and 26 April, 2025', 'time' => '8 Am'],
    ];
    ?>

    <?php
        $target_date = new OsWpDateTime('now');
        $service = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($booking, $target_date, ['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true]);
        foreach ($service['months'] as $month) {
            foreach ($month['days'] as $day) {
                if(!$day['is_past'] && $day['bookable_slots_count'] > 0){
                    $end_date = new OsWpDateTime($day['date']);
                    $end_date->modify('+1 week');
                    $end_date = $end_date->format('Y-m-d');
                    $linked_service = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($linked_services_booking, new OsWpDateTime($day['date']),['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true, 'earliest_possible_booking'=> $day['date'], 'latest_possible_booking' => $end_date]);
                    echo json_encode($linked_service);
                    break;
                }
            }
        }

    //foreach by service
            // if is not past and bookable slot is 1 or more
            //  $linkedService = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($linked_services_booking, new OsWpDateTime($booking->start_date), ['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true, 'earliest_possible_booking'=> $booking->start_date, 'latest_possible_booking' => $end_date]);
                    //new OsWpDateTime($booking->start_date) -> should be form current foreach date
                    //'latest_possible_booking' => -> should be current foreach date + 7 days
                    //in result you'll get same json, but the days and month will be only one.
                    // now create html:

//        $linked_service = OsLinkedServicesCalendarHelper::extract_dates_and_times_data($linked_services_booking, new OsWpDateTime($booking->start_date), ['timezone_name' => OsTimeHelper::get_timezone_name_from_session(), 'consider_cart_items' => true, 'earliest_possible_booking'=> $booking->start_date, 'latest_possible_booking' => $end_date]);
//        echo json_encode($linked_service);


    ?>
    <div class="latepoint-link-service-date-container">
        <?php foreach ($dates as $item): ?>
            <div class="latepoint-link-service-date-box">
                <p><?php echo htmlspecialchars($item['day']); ?></p>
                <small><?php echo htmlspecialchars($item['date']); ?> - <?php echo htmlspecialchars($item['time']); ?></small>
            </div>
        <?php endforeach; ?>
    </div>



    <?php

    echo OsStepsHelper::get_formatted_extra_step_content($current_step_code, 'after');
    do_action('latepoint_after_step_content', $current_step_code);

//    echo OsFormHelper::hidden_field('booking[start_date]', $booking->start_date, [ 'class' => 'latepoint_start_date', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('booking[start_time]', $booking->start_time, [ 'class' => 'latepoint_start_time', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('timeshift_minutes', $timeshift_minutes, [ 'class' => 'latepoint_timeshift_minutes', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('timezone_name', $timezone_name, [ 'class' => 'latepoint_timezone_name', 'skip_id' => true]);
//
//    //todo: add values here
//    echo OsFormHelper::hidden_field('booking[linked_service][start_date]', '', [ 'class' => 'latepoint_linked_service_start_date', 'skip_id' => true]);
//    //todo: add values here
//    echo OsFormHelper::hidden_field('booking[linked_service][start_time]', '', [ 'class' => 'latepoint_linked_service_start_time', 'skip_id' => true]);
//    echo OsFormHelper::hidden_field('booking[linked_service][id]', $linked_services_booking->service_id, ['class' => 'latepoint_linked_service_id', 'skip_id' => true]);
    ?>

    <script>
        // JS to handle box selection
        document.addEventListener('click', function(e) {
            if (e.target.closest('.latepoint-link-service-date-box')) {
                const boxes = document.querySelectorAll('.latepoint-link-service-date-box');
                boxes.forEach(box => box.classList.remove('selected'));
                e.target.closest('.latepoint-link-service-date-box').classList.add('selected');
            }
        });
    </script>

</div>