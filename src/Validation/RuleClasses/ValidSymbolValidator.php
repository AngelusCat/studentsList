<?php

namespace App\Validation\RuleClasses;

use App\Validation\ValidatorRuleInterface;
use App\Validation\ValidationError;

class ValidSymbolValidator implements ValidatorRuleInterface
{
    private const FORMAT = 'В этом поле можно использовать следующие символы: %s. Вы использовали символ(ы), который не входит в этот список, а именно - %s.';
    
    private string $pattern;
    
    private string $verbalDescriptionOfPattern;
    
    public function __construct(string $pattern, string $verbalDescriptionOfPattern)
    {
        $this->pattern = $pattern;
        $this->verbalDescriptionOfPattern = $verbalDescriptionOfPattern;
    }

    public function __invoke(mixed $value): ?ValidationError
    {
        $numberOfCharacters = iconv_strlen($value);
        
        for($i = 0; $i < $numberOfCharacters; $i++) {
            $letter = mb_substr($value, $i, 1);
            $result = preg_match($this->pattern, $letter);
            $invalidSymbols = [];
            if (!$result) {
                if ($letter === " ") {
                    $invalidSymbols [] = "пробел";
                } elseif ($letter === ",") {
                    $invalidSymbols [] = "запятая";
                } else {
                    $invalidSymbols [] = $letter;
                }
            }
        }
        if ($invalidSymbols) {
            $invalidSymbols = array_unique($invalidSymbols);
            $invalidSymbols = implode(", ", $invalidSymbols);
            $errorText = sprintf(
                self::FORMAT, 
                $this->verbalDescriptionOfPattern, 
                $invalidSymbols
            );
            return new ValidationError($errorText);
        } else {
            return null;
        }
    }
}