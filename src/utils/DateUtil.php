<?php

class DateUtil
{
    public static function toLocalDateTime(string $date): DateTime
    {
        return new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
    }

    public static function formatTo(DateTime $date, string $format = 'Y-m-d H:i:s'): string
    {
        return date_format($date, $format);
    }
}