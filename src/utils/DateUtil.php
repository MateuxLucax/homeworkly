<?php

class DateUtil
{
    public static function parseTimestamp(string $timestamp, $format = 'Y-m-d H:i:s') : Date {
        return new DateTime($format, strtotime($timestamp));
    }
}