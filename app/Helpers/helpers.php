<?php

if (!function_exists('excelString')) {
    function excelString($value): ?string
    {
        return $value ? "'{$value}" : null;
    }
}