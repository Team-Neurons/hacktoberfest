<?php


class GetTimezoneTime
{
    public function getTime($tz)
    {
        $date = new DateTime("now", new DateTimeZone($tz));

        return $date->format('Y-m-d H:i:s');
    }
}
