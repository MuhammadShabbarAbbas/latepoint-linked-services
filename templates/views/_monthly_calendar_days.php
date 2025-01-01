<?php
/* @var $booking_request \LatePoint\Misc\BookingRequest */
/* @var $target_date DateTime */
/* @var $calendar_settings array */
?>
<?php
OsLinkedServicesCalendarHelper::generate_single_month($booking_request, $target_date, $calendar_settings);