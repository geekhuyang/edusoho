<?php

namespace AppBundle\Common;

use DateTime;
use DateTimeZone;

class TimeMachine
{
    private static $mockedTime = 0;

    private $timezone;

    const HALF_HOUR = 1800;

    const ONE_HOUR = 3600;

    const ONE_DAY = 86400;

    const ONE_MONTH = 259200;

    public function __construct($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * 单元测试时，解决因为时间引起的测试报错问题
     */
    public static function time()
    {
        return empty(self::$mockedTime) ? time() : self::$mockedTime;
    }

    public static function setMockedTime($time)
    {
        self::$mockedTime = $time;
    }

    public function format($format, $timestamp = null)
    {
        $datetime = new DateTime(null, new DateTimeZone($this->timezone));
        $datetime->setTimestamp($timestamp ?: time());

        return $datetime->format($format);
    }

    public function inSameDay($timestamp1, $timestamp2)
    {
        $datetime = new DateTime(null, new DateTimeZone($this->timezone));

        $datetime->setTimestamp($timestamp1);
        $date1 = $datetime->format('Y-m-d');

        $datetime->setTimestamp($timestamp2);
        $date2 = $datetime->format('Y-m-d');

        return $date1 == $date2;
    }

    public function getDayTimeRange($timestamp)
    {
        $datetime = new DateTime(null, new DateTimeZone($this->timezone));
        $datetime->setTimestamp($timestamp);

        $date = $datetime->format('Y-m-d');
        $datetime = new DateTime($date, new DateTimeZone($this->timezone));

        $startTime = $datetime->getTimestamp();
        $endTime = $startTime + 86400;

        return array($startTime, $endTime);
    }

    public function diffDays($timestamp1, $timestamp2)
    {
        $datetime1 = new DateTime(null, new DateTimeZone($this->timezone));
        $datetime1->setTimestamp($timestamp1);
        $datetime1->setTime(0, 0, 0);

        $datetime2 = new DateTime(null, new DateTimeZone($this->timezone));
        $datetime2->setTimestamp($timestamp2);
        $datetime2->setTime(0, 0, 0);

        $diff = $datetime1->diff($datetime2);

        return $diff->d;
    }
}
