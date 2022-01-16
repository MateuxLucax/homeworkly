<?php

class DateUtil
{
    public static function parseTimestamp(string $timestamp, $format = 'Y-m-d H:i:s') : Date {
        return new DateTime($format, strtotime($timestamp));
    }

    public static function toLocalDateTime(string $date): DateTime
    {
        return new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
    }
}