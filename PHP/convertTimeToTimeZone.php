<?php


class ConvertTimeToTimeZone
{
    public function convert($tz_from,$tz_to,$time)
    {
        $date = DateTime::createFromFormat('U',$time);
        
        $date_tz = new DateTime($date->format("Y-m-d H:i:s", new DateTimeZone($tz_from));
       
        $date_tz->setTimezone(new DateTimeZone($tz_to));

        return $date_tz->format('Y-m-d H:i:s');
    }
}
