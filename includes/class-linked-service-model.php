<?php

class OsLinkedService
{
    var $start_date = null;
    var $start_time = null;
    var $id = null;
    var $end_date = null;
    var $end_time = null;


    public function calculate_end_date()
    {
        $service = new OsServiceModel($this->id);
        if (((int)$this->start_time + (int)$service->duration) >= (24 * 60)) {
            $date_obj = new OsWpDateTime($this->start_date);
            $end_date = $date_obj->modify('+1 day')->format('Y-m-d');
        } else {
            $end_date = $this->start_date;
        }

        return $end_date;
    }


    public function calculate_end_time()
    {
        $service = new OsServiceModel($this->id);
        $end_time = (int)$this->start_time + (int)$service->duration;
        // continues to next day?
        if ($end_time > (24 * 60)) {
            $end_time = $end_time - (24 * 60);
        }
        return $end_time;
    }


    public function get_nice_start_datetime(bool $hide_if_today = true, bool $hide_year_if_current = true): string
    {
        if ($hide_if_today && $this->start_date == OsTimeHelper::today_date('Y-m-d')) {
            $date = __('Today', 'latepoint');
        } else {
            $date = $this->get_nice_start_date($hide_year_if_current);
        }

        return implode(', ', array_filter([$date, $this->get_nice_start_time()]));
    }

    public function get_nice_start_date($hide_year_if_current = false)
    {
        $d = OsWpDateTime::os_createFromFormat("Y-m-d", $this->start_date);
        if (!$d) {
            return 'n/a';
        }
        if ($hide_year_if_current && ($d->format('Y') == OsTimeHelper::today_date('Y'))) {
            $format = OsSettingsHelper::get_readable_date_format(true);
        } else {
            $format = OsSettingsHelper::get_readable_date_format();
        }

        return OsUtilHelper::translate_months($d->format($format));
    }

    public function get_nice_start_time()
    {
        return OsTimeHelper::minutes_to_hours_and_minutes($this->start_time);
    }

}