<?php

function validerCodeBarreEAN13(string $code): bool {
    if (strlen($code) !== 13 || !ctype_digit($code)) {
        return false;
    }
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $digit = intval($code[$i]);
        if ($i % 2 === 0) {
            $sum += $digit;
        } else {
            $sum += $digit * 3;
        }
    }
    $checkDigit = (10 - ($sum % 10)) % 10;
    return $checkDigit === intval($code[12]);
}