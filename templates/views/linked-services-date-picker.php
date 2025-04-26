<?php

?>
<style>

    .latepoint-link-service-date-box {
        margin-bottom: 10px;
        width: 100%;
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

    <?php foreach ($dates as $item): ?>
        <div class="latepoint-link-service-date-box">
            <p><?php echo htmlspecialchars($item['day']); ?></p>
            <small><?php echo htmlspecialchars($item['date']); ?> - <?php echo htmlspecialchars($item['time']); ?> </small>
        </div>
    <?php endforeach; ?>


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