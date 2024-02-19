<?php

namespace App\Utility;

class ViewHelper
{
    public function shield(string|int $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    public function space($quantity): string
    {
        $result = '';

        for ($i = 1; $i <= $quantity; $i++) {
            $result .= '&nbsp';
        }

        return $result;
    }

    private function howManySpaces(string $line, int $max, int $totalNumberSpaces): int
    {
        $characters = iconv_strlen($line);

        if ($characters < $max) {
            return ($max - $characters) + $totalNumberSpaces;
        } elseif ($characters === $max) {
            return $totalNumberSpaces;
        }
    }

    public function formatting(string $line, int $max, int $totalNumberSpaces): string
    {
        $numberOfSpaces = $this->howManySpaces($line, $max, $totalNumberSpaces);

        return $this->space($numberOfSpaces);
    }
}