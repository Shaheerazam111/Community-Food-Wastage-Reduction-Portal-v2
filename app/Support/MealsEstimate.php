<?php

namespace App\Support;

class MealsEstimate
{
    // Guess meal count from quantity text (e.g. "50 servings", "10 kg")
    public static function fromQuantityString(?string $quantity): int
    {
        if ($quantity === null || $quantity === '') {
            return 1;
        }

        if (preg_match('/(\d+)/', $quantity, $m)) {
            $n = (int) $m[1];

            return $n > 0 ? min($n, 9999) : 1;
        }

        return 1;
    }
}
