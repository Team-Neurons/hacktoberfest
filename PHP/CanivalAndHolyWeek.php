<?php

/**
 * This calculates the "Semana Santa"(LATAM)/Easter for the given year
 * The formula was made by Gauss a lot of years ago.
 * @param int $year in YYYY format
 * @return array Thursday, Friday and Sunday dates
 */
function getHolyWeek($year): array
{
    $a = $year % 19;
    $b = $year % 4;
    $c = $year % 7;
    $d = (19 * $a + 24) % 30;
    $e = (2 * $b + 4 * $c + 6 * $d + 5) % 7;
    $days = 22 + $d + $e;
    $timeString = $days <= 31 ? $year . '-03-' . $days : $year . '-04-' . ($days - 31);
    $dateTime = DateTime::createFromFormat('Y-m-d', $timeString);
    $sunday = $dateTime->format('Y-m-d');
    $friday = $dateTime->sub(new DateInterval('P2D'))->format('Y-m-d');
    $thursday = $dateTime->sub(new DateInterval('P1D'))->format('Y-m-d');
    return [$thursday, $friday, $sunday];
}

/**
 * Latin America's Carnival is 40 days before the Holy Sunday,
 * so we must calculate it before get the carnival
 * @param int $year in YYYY format
 * @return array Monday and Tuesday dates
 */
function getCarnival($year): array
{
    $holyWeek = getHolyWeek($year);
    $dateTime = DateTime::createFromFormat('Y-m-d', $holyWeek[2]);
    $tuesday = $dateTime->sub(new DateInterval('P47D'))->format('Y-m-d');
    $monday = $dateTime->sub(new DateInterval('P1D'))->format('Y-m-d');
    return [$monday, $tuesday];
}