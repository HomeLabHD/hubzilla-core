<?php

namespace Root23\JsonCanonicalizer;

class Converter
{
    public static function toHex(string $data): string
    {
        return rtrim(chunk_split(bin2hex($data), 2, ' '));
    }

    public static function toEs6NumberFormat(float $number): string
    {
        if (is_nan($number) || is_infinite($number)) {
            throw new \LogicException("Infinity or NaN can't use in json");
        }

        if ($number === 0.0) {
            return '0';
        }

        $sign = '';
        if ($number < 0) {
            $sign = '-';
            $number = -$number;
        }

        if ($number < 1e+21 && $number >= 1e-6) {
            $formatted = number_format($number, 7, '.', '');
            $formatted = rtrim($formatted, '.0');
        } else {
            $formatted = sprintf('%e', $number);
            $parts = explode('e', $formatted);
            $parts[0] = rtrim($parts[0], '.0');
            $formatted = implode('e', $parts);
        }

        return $sign . $formatted;
    }
}
