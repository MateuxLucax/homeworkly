<?php

class PasswordUtil
{
    public static function hash(string $password) : string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function validate(string $password, string $hashed) : bool {
        return password_verify($password, $hashed);
    }
}