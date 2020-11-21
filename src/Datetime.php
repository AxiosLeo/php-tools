<?php

declare(strict_types=1);

namespace axios\tools;

class Datetime
{
    private $base_timestamp;

    public function __construct($base_timestamp = null)
    {
        if (null === $base_timestamp) {
            $base_timestamp = time();
        }
        $this->base_timestamp = $base_timestamp;
    }

    public function hourBeginEnd($hour)
    {
        $date  = date('Y-m-d', $this->base_timestamp);
        $hour  = sprintf('%02d', $hour);
        $begin = strtotime($date . ' ' . $hour . ':00:00');
        $end   = strtotime($date . ' ' . $hour . ':00:00 +1 hour -1 seconds');

        return [$begin, $end];
    }

    public function dayBeginEnd($date = null)
    {
        if (null === $date) {
            $date = date('Y-m-d', $this->base_timestamp);
        }
        $begin = strtotime($date . ' 00:00:00');
        $end   = strtotime("{$date} +1 day -1 seconds");

        return [$begin, $end];
    }

    public function monthBeginEnd($year = null, $month = null)
    {
        if (null === $year) {
            $year = date('Y', $this->base_timestamp);
        }
        if (null === $month) {
            $month = date('m', $this->base_timestamp);
        }
        $month = sprintf('%02d', $month);
        $ymd   = $year . '-' . $month . '-01';
        $begin = strtotime($ymd . ' 00:00:00');
        $end   = strtotime("{$ymd} +1 month -1 seconds");

        return [$begin, $end];
    }

    public function yearBeginEnd($year)
    {
        if (null === $year) {
            $year = date('Y', $this->base_timestamp);
        }
        $ymd   = $year . '-01-01';
        $begin = strtotime($ymd . ' 00:00:00');
        $end   = strtotime("{$ymd} +1 year -1 seconds");

        return [$begin, $end];
    }
}
